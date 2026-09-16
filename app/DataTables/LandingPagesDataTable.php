<?php

namespace App\DataTables;

use App\Helpers\UserHelper;
use App\Models\LandingPage;
use App\Support\SecureRouteParameter;
use Illuminate\Database\Eloquent\Builder as QueryBuilder;
use Yajra\DataTables\EloquentDataTable;
use Yajra\DataTables\Html\Builder as HtmlBuilder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Services\DataTable;

class LandingPagesDataTable extends DataTable
{
    private $user;

    public function __construct()
    {
        $this->user = UserHelper::getLoggedInUser();
    }

    public function dataTable(QueryBuilder $query): EloquentDataTable
    {
        $user = $this->user;

        return (new EloquentDataTable($query))
            ->addIndexColumn()
            ->addColumn('action', function ($row) use ($user) {
                $routeId = SecureRouteParameter::encode($row->id);
                $routeParameters = ['landing_page' => $routeId];
                $editRoute   = $user->can('landing-page-edit')   ? route('admin.landing-pages.edit',    $routeParameters) : '';
                $deleteRoute = $user->can('landing-page-delete') ? route('admin.landing-pages.destroy', $routeParameters) : '';
                $viewRoute   = $user->can('landing-page-show')   ? route('admin.landing-pages.show',    $routeParameters) : '';

                return view('layouts.partials.dataTable-action-button', compact('editRoute', 'deleteRoute', 'viewRoute'));
            })
            ->addColumn('public_url', function ($row) {
                $url = url('/lp/' . $row->slug);
                return '<a href="' . $url . '" target="_blank" class="text-primary text-decoration-none" title="Open landing page">'
                    . '<i class="fa-solid fa-arrow-up-right-from-square me-1"></i>'
                    . e($row->slug)
                    . '</a>';
            })
            ->editColumn('status', function ($row) {
                $badgeClass = $row->status === 'active' ? 'bg-success' : 'bg-secondary';
                return '<span class="badge ' . $badgeClass . '">' . ucfirst($row->status) . '</span>';
            })
            ->editColumn('is_appointment_enabled', function ($row) {
                return $row->is_appointment_enabled
                    ? '<span class="badge bg-success">Enabled</span>'
                    : '<span class="badge bg-secondary">Disabled</span>';
            })
            ->rawColumns(['status', 'action', 'public_url', 'is_appointment_enabled'])
            ->setRowId('id');
    }

    public function query(LandingPage $model): QueryBuilder
    {
        $query = $model->newQuery();

        // Each user sees only their own landing pages (unless Super Admin)
        if ($this->user && !$this->user->hasRole(config('constants.super_admin_role_name'))
            && !$this->user->hasRole(config('constants.admin_role_name'))) {
            $query->where('created_by', $this->user->id);
        }

        return $query;
    }

    public function html(): HtmlBuilder
    {
        $dataTable = $this->builder()
            ->setTableId('landing-pages')
            ->columns($this->getColumns())
            ->minifiedAjax()
            ->dom("<'d-flex justify-content-start mb-2'B><'search-bar-wrapper'lf>r<'table-wrapper yajra-table-custom-class table-responsive'tr><'pagination-wrapper'ip>")
            ->orderBy('1', 'asc');

        $buttons = [];
        if ($this->user->can('landing-page-create')) {
            $buttons[] = Button::make('add')
                ->attr(['class' => 'btn text-center btn-primary my-custom-btn'])
                ->text(__('buttons.create'));
        }

        $dataTable->buttons($buttons);

        return $dataTable->parameters([
            'processing' => false,
            'language'   => [
                'searchPlaceholder' => __('labels.search'),
            ],
        ]);
    }

    public function getColumns(): array
    {
        return [
            Column::computed('DT_RowIndex')
                ->title(__('labels.id'))
                ->width(50)
                ->addClass('text-center'),
            Column::make('clinic_name')->title(__('labels.clinic_name')),
            Column::make('public_url')->title('Public URL / Slug'),
            Column::make('is_appointment_enabled')->title('Booking')->addClass('text-center'),
            Column::make('status')->title('Status')->addClass('text-center'),
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
        return 'LandingPages_' . date('YmdHis');
    }
}
