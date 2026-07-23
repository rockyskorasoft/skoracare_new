<?php

namespace App\Http\Controllers\Web;

use App\DataTables\PackagesDataTable;
use App\Http\Requests\Package\CreateRequest;
use App\Http\Requests\Package\UpdateRequest;
use App\Repositories\PermissionRepository;
use App\Services\PackageService;
use App\Support\SecureRouteParameter;
use DB;
use Exception;

class PackageController extends WebController
{
    protected $dbObject;

    public function __construct(
        public PackageService $packageService,
        public PermissionRepository $permissionRepository
    ) {
        $this->dbObject = DB::class;
        $this->middleware(['permission:package-list'], ['only' => ['index', 'pricing']]);
        $this->middleware(['permission:package-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:package-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:package-delete'], ['only' => ['destroy']]);
        $this->middleware(['permission:package-show'], ['only' => ['show']]);
    }

    /**
     * Render package datatable.
     */
    public function index(PackagesDataTable $dataTable)
    {
        return $dataTable->render('packages.index');
    }

    /**
     * Render pricing plans preview cards.
     */
    public function pricing()
    {
        $packages = \App\Models\Package::with('permissions')->where('status', 'active')->get();
        return view('packages.pricing', compact('packages'));
    }

    /**
     * Show create package form.
     */
    public function create()
    {
        $permissions = [
            'children' => $this->permissionRepository->getAllData(),
        ];
        return view('packages.create', compact('permissions'));
    }

    /**
     * Store package and sync permissions.
     */
    public function store(CreateRequest $request)
    {
        try {
            $requestData = $this->packageService->getDataFromRequest($request);

            $this->dbObject::beginTransaction();
            $package = $this->packageService->createData($requestData);

            $parents = $request->input('parents', []);
            $children = $request->input('children', []);
            $permissionIds = array_filter(array_merge($parents, $children));

            if (!empty($permissionIds)) {
                $package->permissions()->sync($permissionIds);
            }

            $this->dbObject::commit();

            return $this->successResponse('admin.packages.index', trans('app.data_created', ['action' => 'Package']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Show package details.
     */
    public function show(string $id)
    {
        $packageId = SecureRouteParameter::decodeOrFail($id);
        $package = $this->packageService->getDataById($packageId);
        $package->load('permissions');

        return view('packages.show', compact('package'));
    }

    /**
     * Show edit package form.
     */
    public function edit(string $id)
    {
        $packageId = SecureRouteParameter::decodeOrFail($id);
        $package = $this->packageService->getDataById($packageId);
        $package->load('permissions');

        $permissions = [
            'children' => $this->permissionRepository->getAllData(),
        ];
        $packagePermissionIds = $package->permissions->pluck('id')->toArray();

        return view('packages.edit', compact('package', 'permissions', 'packagePermissionIds'));
    }

    /**
     * Update package data and sync permissions.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $packageId = SecureRouteParameter::decodeOrFail($id);
        $requestData = $this->packageService->getDataFromRequest($request);

        try {
            $this->dbObject::beginTransaction();
            $package = $this->packageService->updateData($packageId, $requestData);

            $parents = $request->input('parents', []);
            $children = $request->input('children', []);
            $permissionIds = array_filter(array_merge($parents, $children));

            $package->permissions()->sync($permissionIds);

            $this->dbObject::commit();

            return $this->successResponse('admin.packages.index', trans('app.data_updated', ['action' => 'Package']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Delete package.
     */
    public function destroy(string $id)
    {
        try {
            $packageId = SecureRouteParameter::decodeOrFail($id);
            $this->packageService->deleteDataById($packageId);

            return $this->successResponse('admin.packages.index', trans('app.data_deleted', ['action' => 'Package']));
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }
}
