<?php

namespace App\DataTables;

use App\Helpers\UserHelper;
use App\Models\User;
use App\Support\SecureRouteParameter;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class StaffDataTable extends DataTable
{
    private $user;

    public function __construct()
    {
        $this->user = UserHelper::getLoggedInUser();
    }

    /**
     * Build DataTable class.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $user = $this->user;

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($user) {
                $routeId = SecureRouteParameter::encode($row->id);
                $routeParameters = ['staff' => $routeId];
                $editRoute = $user->can('staff-edit') || $user->can('user-edit') ? route('admin.staff.edit', $routeParameters) : '';
                $deleteRoute = $user->can('staff-delete') || $user->can('user-delete') ? route('admin.staff.destroy', $routeParameters) : '';
                $viewRoute = $user->can('staff-show') || $user->can('user-show') ? route('admin.staff.show', $routeParameters) : '';

                return view('layouts.partials.dataTable-action-button', compact('editRoute', 'deleteRoute', 'viewRoute'));
            })
            ->addColumn('name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })
            ->addColumn('doctor_name', function ($row) {
                return $row->creator ? ($row->creator->first_name . ' ' . $row->creator->last_name) : 'Super Admin';
            })
            ->addColumn('assigned_clinics', function ($row) {
                $clinics = $row->assignedClinics->pluck('name')->toArray();
                if (empty($clinics)) {
                    return '<span class="badge bg-light text-muted">All Doctor Clinics</span>';
                }
                $html = '';
                foreach ($clinics as $cName) {
                    $html .= '<span class="badge bg-info text-dark me-1">' . e($cName) . '</span>';
                }
                return $html;
            })
            ->editColumn('status', function ($row) {
                $badgeClass = $row->status === 'active' ? 'bg-success' : 'bg-secondary';
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->rawColumns(['assigned_clinics', 'status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable filtered by logged-in user role.
     */
    public function query(User $model): QueryBuilder
    {
        $query = $model->newQuery()->with(['creator', 'assignedClinics'])->role('Staff');

        // Scoped for Doctor role users
        if ($this->user && $this->user->hasRole(config('constants.doctor_role_name'))) {
            $query->where('created_by', $this->user->id);
        }

        return $query;
    }

    /**
     * Build DataTable HTML and buttons.
     */
    public function html(): HtmlBuilder
    {
        $staffCreatePermission = $this->user->can('staff-create') || $this->user->can('user-create');
        $canCreateLimit = $this->user->canCreateUser();

        $dataTable = $this->builder()
            ->setTableId('staff-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'d-flex justify-content-start mb-2'B><'search-bar-wrapper'lf>r<'table-wrapper yajra-table-custom-class table-responsive'tr><'pagination-wrapper'ip>")
            ->orderBy(1);

        $buttons = [];
        if ($staffCreatePermission) {
            if ($canCreateLimit) {
                $buttons[] = Button::make('add')
                    ->attr(['class' => 'btn text-center my-custom-btn'])
                    ->text(__('buttons.create'));
            } else {
                $buttons[] = Button::make('add')
                    ->attr([
                        'class' => 'btn text-center btn-secondary disabled',
                        'disabled' => 'disabled',
                        'title' => 'User creation limit reached for your active package plan',
                        'style' => 'cursor: not-allowed; opacity: 0.65;'
                    ])
                    ->text(__('buttons.create') . ' (Limit Reached)');
            }
        }
        $dataTable->buttons($buttons);

        return $dataTable
            ->parameters([
                'processing' => false,
                'language' => [
                    'searchPlaceholder' => __('labels.search'),
                ],
            ]);
    }

    /**
     * Get columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title(__('labels.id'))
                ->width(50)
                ->addClass('text-center'),
            Column::computed('name')->title('Staff Name'),
            Column::make('email')->title('Email'),
            Column::make('phone_no')->title('Phone'),
            Column::computed('doctor_name')->title('Doctor / Owner'),
            Column::computed('assigned_clinics')->title('Assigned Clinics'),
            Column::make('status')->title('Status'),
            Column::computed('action')
                ->title(__('labels.action'))
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    protected function filename(): string
    {
        return 'Staff_' . date('YmdHis');
    }
}
