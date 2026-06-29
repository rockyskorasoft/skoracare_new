<?php

namespace App\DataTables;

use App\Models\Role;
use App\Support\SecureRouteParameter;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class RolesDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $user = auth()->user();

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($user) {
                $routeId = SecureRouteParameter::encode($row->id);
                $editRoute = $user->can('role-edit') ? route('admin.roles.edit', $routeId) : '';
                $deleteRoute = $user->can('role-delete') ? route('admin.roles.destroy', $routeId) : '';

                return view('layouts.partials.dataTable-action-button', compact('editRoute', 'deleteRoute'));
            })
            ->addColumn('created_at', function ($row) {
                return $this->formatDate($row->getRawOriginal('created_at') ?? $row->created_at);
            })
            ->addColumn('updated_at', function ($row) {
                return $this->formatDate($row->getRawOriginal('updated_at') ?? $row->updated_at);
            })
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     */
    public function query(Role $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        $createRole = auth()->user()->can('role-create');

        return $this->builder()
            ->setTableId('roles')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'d-flex justify-content-start mb-2'B><'search-bar-wrapper'lf>r<'table-wrapper yajra-table-custom-class table-responsive'tr><'pagination-wrapper'ip>")
            ->orderBy('2', 'desc')
            ->parameters([
                'processing' => true,
                'serverSide' => true,
                'language' => [
                    'searchPlaceholder' => 'Search..',
                ],
                'buttons' => array_merge(
                    $createRole ? [
                        [
                            'extend' => 'add',
                            'text' => __('buttons.create'),
                            'attr' => ['class' => 'btn btn-primary'],
                        ],
                    ] : [],
                    []
                ),
            ]);
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
            Column::make('name')->title(__('labels.role_name')),
            Column::make('created_at')->title(__('labels.created_at')),
            Column::make('updated_at')->title(__('labels.updated_at')),
            Column::computed('action')->title(__('labels.action'))
                ->exportable(false)
                ->printable(false)
                ->width(60)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get filename for export.
     */
    protected function filename(): string
    {
        return 'Roles_'.date('YmdHis');
    }

    private function formatDate(mixed $value): string
    {
        if (! $value) {
            return 'N/A';
        }

        if ($value instanceof CarbonInterface) {
            return $value->format('Y-m-d');
        }

        return date('Y-m-d', strtotime((string) $value));
    }
}
