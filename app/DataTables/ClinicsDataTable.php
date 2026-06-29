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
                $routeParameters = ['clinic' => $routeId];
                $editRoute = $user->can('clinic-edit') ? route('admin.clinics.edit', $routeParameters) : '';
                $deleteRoute = $user->can('clinic-delete') ? route('admin.clinics.destroy', $routeParameters) : '';
                $viewRoute = $user->can('clinic-show') ? route('admin.clinics.show', $routeParameters) : '';

                return view('layouts.partials.dataTable-action-button', compact('editRoute', 'deleteRoute', 'viewRoute'));
            })
            ->addColumn('doctor_name', function ($row) {
                return $row->doctor ? $row->doctor->name : 'N/A';
            })
            ->editColumn('consultation_fee', function ($row) {
                return '₹' . number_format($row->consultation_fee, 2);
            })
            ->editColumn('phone_no', function ($row) {
                return $row->phone_no ? $row->phone_no : 'N/A';
            })
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     */
    public function query(Clinic $model): QueryBuilder
    {
        return $model->newQuery()->with('doctor');
    }

    public function html(): HtmlBuilder
    {
        $createClinic = $this->user->can('clinic-create');

        $dataTable = $this->builder()
            ->setTableId('clinics-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'d-flex justify-content-start mb-2'B><'search-bar-wrapper'lf>r<'table-wrapper yajra-table-custom-class table-responsive'tr><'pagination-wrapper'ip>")
            ->orderBy(1);

        $buttons = [];
        if ($createClinic) {
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
            Column::make('name')->title('Clinic Name'),
            Column::computed('doctor_name')->title('Owner (Doctor)'),
            Column::make('phone_no')->title(__('labels.mobile_number')),
            Column::make('consultation_fee')->title('Consultation Fee'),
            Column::make('address')->title(__('labels.address')),
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
        return 'Clinics_'.date('YmdHis');
    }
}
