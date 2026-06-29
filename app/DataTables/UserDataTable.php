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

class UserDataTable extends DataTable
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
                $routeParameters = ['user' => $routeId];
                $editRoute = $user->can('user-edit') ? route($this->userRouteName('edit'), $routeParameters) : '';
                $deleteRoute = $user->can('user-delete') ? route($this->userRouteName('destroy'), $routeParameters) : '';
                $viewRoute = $user->can('user-show') ? route($this->userRouteName('show'), $routeParameters) : '';

                return view('layouts.partials.dataTable-action-button', compact('editRoute', 'deleteRoute', 'viewRoute'));
            })
            ->editColumn('status', function ($row) {
                return $row->status ? __('labels.'.$row->status) : 'N/A';
            })
            ->editColumn('phone_no', function ($row) {
                return $row->phone_no ? $row->phone_no : 'N/A';
            })
            ->editColumn('first_name', function ($row) {
                return $row->first_name ? $row->first_name : 'N/A';
            })
            ->editColumn('last_name', function ($row) {
                return $row->last_name ? $row->last_name : 'N/A';
            })
            ->setRowId('id');
    }

    /**
     * Get query source of dataTable.
     */
    public function query(User $model): QueryBuilder
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     */
    public function html(): HtmlBuilder
    {
        $userCreate = $this->user->can('user-create');
        $dataTable = $this->builder()
            ->setTableId('users')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'d-flex justify-content-start mb-2'B><'search-bar-wrapper'lf>r<'table-wrapper yajra-table-custom-class table-responsive'tr><'pagination-wrapper'ip>")
            // ->dom('Bfrtip')
            ->orderBy('3', 'desc');

        $buttons = [];
        if ($userCreate) {
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
                ->title(__('labels.sno'))
                ->width(50)
                ->addClass('text-center'),
            Column::make('first_name')->title(__('labels.first_name'))->addClass('text-center'),
            Column::make('last_name')->title(__('labels.last_name'))->addClass('text-center'),
            Column::make('phone_no')->title(__('labels.mobile_number'))->addClass('text-center'),
            Column::make('email')->title(__('labels.email'))->addClass('text-center'),
            Column::make('status')->title(__('labels.status'))->addClass('text-center'),
            Column::make('created_at')->title(__('labels.created_at'))->addClass('text-center'),
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
        return 'User_'.date('YmdHis');
    }

    private function userRouteName(string $action): string
    {
        return "admin.users.{$action}";
    }
}
