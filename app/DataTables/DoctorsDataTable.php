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

class DoctorsDataTable extends DataTable
{
    private $user;

    public function __construct()
    {
        $this->user = UserHelper::getLoggedInUser();
    }

    /**
     * Build DataTable class.
     *
     * @param  QueryBuilder  $query  Results from query() method.
     */
    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $user = $this->user;

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($user) {
                $routeId = SecureRouteParameter::encode($row->id);
                $routeParameters = ['doctor' => $routeId];
                $editRoute = $user->can('doctor-edit') ? route('admin.doctors.edit', $routeParameters) : '';
                $deleteRoute = $user->can('doctor-delete') ? route('admin.doctors.destroy', $routeParameters) : '';
                $viewRoute = $user->can('doctor-show') ? route('admin.doctors.show', $routeParameters) : '';

                return view('layouts.partials.dataTable-action-button', compact('editRoute', 'deleteRoute', 'viewRoute'));
            })
            ->editColumn('status', function ($row) {
                return $row->status ? __('labels.'.$row->status) : 'N/A';
            })
            ->editColumn('phone_no', function ($row) {
                return $row->phone_no ? $row->phone_no : 'N/A';
            })
            ->editColumn('qualification', function ($row) {
                return $row->qualification ? $row->qualification : 'N/A';
            })
            ->editColumn('registration_number', function ($row) {
                return $row->registration_number ? $row->registration_number : 'N/A';
            })
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery()->role(config('constants.doctor_role_name'));
    }

    public function html(): HtmlBuilder
    {
        $createDoctor = $this->user->can('doctor-create');

        $dataTable = $this->builder()
            ->setTableId('doctors-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'d-flex justify-content-start mb-2'B><'search-bar-wrapper'lf>r<'table-wrapper yajra-table-custom-class table-responsive'tr><'pagination-wrapper'ip>")
            ->orderBy(1);

        $buttons = [];
        if ($createDoctor) {
            $buttons[] = Button::make('add')
                ->attr(['class' => 'btn text-center btn-primary'])
                ->text(__('buttons.create'));
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
            Column::make('first_name')->title(__('labels.first_name')),
            Column::make('last_name')->title(__('labels.last_name')),
            Column::make('email')->title(__('labels.email')),
            Column::make('phone_no')->title(__('labels.mobile_number')),
            Column::make('qualification')->title('Qualification'),
            Column::make('registration_number')->title('Registration Number'),
            Column::make('status')->title(__('labels.status')),
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
        return 'Doctors_'.date('YmdHis');
    }
}
