<?php

namespace App\DataTables;

use App\Helpers\UserHelper;
use App\Models\Clinic;
use App\Support\SecureRouteParameter;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class ClinicsDataTable extends DataTable
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
                $routeParameters = ['clinic' => $routeId];
                $editRoute = $user->can('clinic-edit') ? route('admin.clinics.edit', $routeParameters) : '';
                $deleteRoute = $user->can('clinic-delete') ? route('admin.clinics.destroy', $routeParameters) : '';
                $viewRoute = $user->can('clinic-show') ? route('admin.clinics.show', $routeParameters) : '';

                return view('layouts.partials.dataTable-action-button', compact('editRoute', 'deleteRoute', 'viewRoute'));
            })
            ->addColumn('doctor_name', function ($row) {
                return $row->doctor ? ($row->doctor->first_name . ' ' . $row->doctor->last_name) : 'N/A';
            })
            ->editColumn('consultation_fee', function ($row) {
                return '₹' . number_format($row->consultation_fee, 2);
            })
            ->editColumn('phone_no', function ($row) {
                return $row->phone_no ? $row->phone_no : 'N/A';
            })
            ->editColumn('status', function ($row) {
                $badgeClass = $row->status === 'active' ? 'bg-success' : 'bg-secondary';
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->rawColumns(['status', 'action'])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable filtered by logged-in user role.
     */
    public function query(Clinic $model): QueryBuilder
    {
        $query = $model->newQuery()->with('doctor');

        // Scoped for Doctor role users
        if ($this->user && $this->user->hasRole(config('constants.doctor_role_name'))) {
            $query->where('doctor_id', $this->user->id);
        }

        return $query;
    }

    /**
     * Build DataTable HTML and buttons according to role & limit permissions.
     */
    public function html(): HtmlBuilder
    {
        $clinicCreatePermission = $this->user->can('clinic-create');
        $canCreateLimit = $this->user->canCreateClinic();

        $dataTable = $this->builder()
            ->setTableId('clinics')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'d-flex justify-content-start mb-2'B><'search-bar-wrapper'lf>r<'table-wrapper yajra-table-custom-class table-responsive'tr><'pagination-wrapper'ip>")
            ->orderBy('1', 'asc');

        $buttons = [];
        if ($clinicCreatePermission) {
            if ($canCreateLimit) {
                $buttons[] = Button::make('add')
                    ->attr(['class' => 'btn text-center btn-primary'])
                    ->text(__('buttons.create'));
            } else {
                $buttons[] = Button::make('add')
                    ->attr([
                        'class' => 'btn text-center btn-secondary disabled',
                        'disabled' => 'disabled',
                        'title' => 'Clinic creation limit reached for your active package plan',
                        'style' => 'cursor: not-allowed; opacity: 0.65;'
                    ])
                    ->text(__('buttons.create') . ' (Limit Reached)');
            }
        }

        if ($this->user->can('clinic-export') || $this->user->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')])) {
            $buttons[] = Button::make('export')
                ->attr(['class' => 'btn text-center btn-outline-secondary ms-2']);
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
     * Get the dataTable columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title(__('labels.id'))
                ->width(50)
                ->addClass('text-center'),
            Column::make('name')->title(__('labels.clinic_name')),
            Column::make('email')->title('Clinic Email'),
            Column::make('doctor_name')->title(__('labels.owner_doctor')),
            Column::make('phone_no')->title(__('labels.mobile_number')),
            Column::make('consultation_fee')->title(__('labels.consultation_fee')),
            Column::make('status')->title('Status'),
            Column::computed('action')
                ->title(__('labels.action'))
                ->exportable(false)
                ->printable(false)
                ->width(100)
                ->addClass('text-center'),
        ];
    }

    /**
     * Get the filename for export.
     */
    protected function filename(): string
    {
        return 'Clinics_' . date('YmdHis');
    }
}
