<?php

namespace App\DataTables;

use App\Helpers\DateHelper;
use App\Helpers\UserHelper;
use App\Models\Activity;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ActivityLogDataTable extends DataTable
{
    private $user;

    public function __construct()
    {
        $this->user = UserHelper::getLoggedInUser();
    }

    /**
     * Build the DataTable class.
     *
     * @param QueryBuilder $query Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $user = $this->user;
        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($user) {
                $viewRoute = $user->can('activity-log-show') ? route('admin.activity-log.show', encrypt($row->id)) : '';
                return view('layouts.partials.dataTable-action-button', compact('viewRoute'));
            })
            ->addColumn('changes', function ($activity) {
                return $this->changesPreview($activity);
            })
            ->editColumn('created_by', function ($activity) {
                return $this->userName($this->subjectCreatedBy($activity) ?: $activity->createdBy ?: $activity->causer);
            })
            ->filter(function ($query) {
                $search = request()->get('search')['value'] ?? '';

                if (! $search) {
                    return;
                }

                $keyword = $this->searchKeyword($search);

                $query->where(function ($subquery) use ($keyword) {
                    $subquery->whereRaw('LOWER(log_name) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(description) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(ip_address) LIKE ?', [$keyword])
                        ->orWhereRaw('LOWER(CAST(properties AS CHAR)) LIKE ?', [$keyword])
                        ->orWhereHas('createdBy', function ($userQuery) use ($keyword) {
                            $userQuery->whereRaw(
                                "LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?",
                                [$keyword]
                            )->orWhereRaw('LOWER(email) LIKE ?', [$keyword]);
                        });
                });
            })
            ->filterColumn('changes', function ($query, $keyword) {
                $query->whereRaw('LOWER(CAST(properties AS CHAR)) LIKE ?', [$this->searchKeyword($keyword)]);
            })
            ->filterColumn('created_by', function ($query, $keyword) {
                $keyword = $this->searchKeyword($keyword);

                $query->whereHas('createdBy', function ($userQuery) use ($keyword) {
                    $userQuery->whereRaw(
                        "LOWER(CONCAT(first_name, ' ', last_name)) LIKE ?",
                        [$keyword]
                    )->orWhereRaw('LOWER(email) LIKE ?', [$keyword]);
                });
            })
            ->rawColumns(['changes'])
            ->setRowId('id');
    }

    /**
     * Get the query source of dataTable.
     */
    public function query(Activity $model): QueryBuilder
    {
        $query = $model->newQuery()
            ->with(['createdBy', 'causer'])
            ->orderBy('id', 'desc');

        $user = $this->user;
        if ($user && ! $user->hasRole(config('constants.super_admin_role_name'))) {
            $query->where(function ($activityQuery) use ($user) {
                $activityQuery->where('causer_id', $user->id)
                    ->orWhere('created_by', $user->id);
            });
        }

        $filters = request()->get('filters');

        if (!empty($filters)) {
            foreach ($filters as $key => $value) {
                if ($key === 'created_by' && ! $this->canViewAllActivity()) {
                    continue;
                }

                if ($key === 'created_at' && !empty($value)) {
                    if (str_contains($value, ' to ')) {
                        [$startDate, $endDate] = explode(' to ', $value);
                        $start = DateHelper::parseDateTime(trim($startDate))->startOfDay();
                        $end = DateHelper::parseDateTime(trim($endDate))->endOfDay();
                        $query->whereBetween($key, [$start, $end]);
                    } else {
                        $date = DateHelper::parseDateTime(trim($value));
                        $query->whereDate($key, $date);
                    }
                } elseif ($key === 'created_by') {
                    $query->whereHas('createdBy', function ($subquery) use ($value) {
                        $subquery->where('id', $value);
                    });
                } else {
                    $query = $query->where($key, $value);
                }
            }
        }

        return $query;
    }

    /**
     * Optional method if you want to use the html builder.
     */
    public function html(): HtmlBuilder
    {
        return $this->builder()
            ->setTableId('activitylog-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            //->dom('Bfrtip')
            ->orderBy(1)
            ->selectStyleSingle()
            ->buttons([]);
    }

    /**
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title(__('labels.id'))
                ->width(50)
                ->addClass('text-center'),
            Column::make('log_name')->title(__('labels.name')),
            Column::make('description')->title(__('labels.description')),
            Column::make('changes')->title(__('labels.changes')),
            Column::make('ip_address')->title(__('labels.ip_address')),
            Column::make('created_by')->title(__('labels.created_by')),
            Column::make('created_at')->title(__('labels.created_at')),
            Column::computed('action')->title(__('labels.action'))
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'ActivityLog_' . date('YmdHis');
    }

    /**
     * Get the user's display name.
     */
    private function userName(?Model $user): string
    {
        if (! $user) {
            return 'N/A';
        }

        return trim(($user->first_name ?? '') . ' ' . ($user->last_name ?? '')) ?: $user->email ?? 'N/A';
    }

    /**
     * Get the creator of the activity subject.
     */
    private function subjectCreatedBy(Activity $activity): ?Model
    {
        $subject = $activity->subject;

        if (! $subject || ! method_exists($subject, 'createdBy')) {
            return null;
        }

        return $subject->createdBy;
    }

    /**
     * Get a short changes preview for the index table.
     */
    private function changesPreview(Activity $activity): string
    {
        $changes = $activity->changeLines();

        if (empty($changes)) {
            return 'N/A';
        }

        $visibleChanges = array_slice($changes, 0, 2);
        $html = collect($visibleChanges)
            ->map(fn($change) => e($change))
            ->implode('<br>');

        $remainingCount = count($changes) - count($visibleChanges);
        if ($remainingCount > 0) {
            $html .= '<br><small><em>and ' . $remainingCount . ' more...</em></small>';
        }

        return $html . '<br><small><em>Changed on: ' . e($activity->created_at ?? 'N/A') . '</em></small>';
    }

    /**
     * Prepare a keyword for LIKE search.
     */
    private function searchKeyword(string $keyword): string
    {
        return '%' . addcslashes(strtolower($keyword), '\\%_') . '%';
    }

    /**
     * Determine whether the current user can view every user's activity.
     */
    private function canViewAllActivity(): bool
    {
        return $this->user?->hasRole(config('constants.super_admin_role_name')) ?? false;
    }
}
