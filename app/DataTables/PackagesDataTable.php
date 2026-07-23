<?php

namespace App\DataTables;

use App\Helpers\UserHelper;
use App\Models\Package;
use App\Support\SecureRouteParameter;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class PackagesDataTable extends DataTable
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
                $routeParameters = ['package' => $routeId];
                $editRoute = $user->can('package-edit') ? route('admin.packages.edit', $routeParameters) : '';
                $deleteRoute = $user->can('package-delete') ? route('admin.packages.destroy', $routeParameters) : '';
                $viewRoute = $user->can('package-show') ? route('admin.packages.show', $routeParameters) : '';

                return view('layouts.partials.dataTable-action-button', compact('editRoute', 'deleteRoute', 'viewRoute'));
            })
            ->editColumn('monthly_price', function ($row) {
                return '₹' . number_format($row->monthly_price, 2);
            })
            ->editColumn('yearly_price', function ($row) {
                return '₹' . number_format($row->yearly_price, 2);
            })
            ->editColumn('clinic_limit', function ($row) {
                return $row->clinic_limit == -1 ? 'Unlimited' : $row->clinic_limit;
            })
            ->editColumn('user_limit', function ($row) {
                return $row->user_limit == -1 ? 'Unlimited' : $row->user_limit;
            })
            ->editColumn('status', function ($row) {
                $badgeClass = $row->status === 'active' ? 'bg-success' : 'bg-secondary';
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->editColumn('is_popular', function ($row) {
                return $row->is_popular 
                    ? '<span class="badge bg-info text-dark"><i class="fa-solid fa-star me-1"></i>Most Popular</span>' 
                    : '<span class="badge bg-light text-muted">Standard</span>';
            })
            ->rawColumns(['status', 'is_popular', 'action'])
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     */
    public function query(Package $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Build DataTable HTML and buttons.
     */
    public function html(): HtmlBuilder
    {
        $createPermission = $this->user->can('package-create');

        $dataTable = $this->builder()
            ->setTableId('packages-table')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'d-flex justify-content-start mb-2'B><'search-bar-wrapper'lf>r<'table-wrapper yajra-table-custom-class table-responsive'tr><'pagination-wrapper'ip>")
            ->orderBy(1);

        $buttons = [];
        if ($createPermission) {
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
     * Get columns definition.
     */
    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title(__('labels.id'))
                ->width(50)
                ->addClass('text-center'),
            Column::make('name')->title('Package Name'),
            Column::make('monthly_price')->title('Monthly Price'),
            Column::make('yearly_price')->title('Yearly Price'),
            Column::make('clinic_limit')->title('Clinic Limit'),
            Column::make('user_limit')->title('User/Staff Limit'),
            Column::make('status')->title('Status'),
            Column::make('is_popular')->title('Tag'),
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
        return 'Packages_' . date('YmdHis');
    }
}
