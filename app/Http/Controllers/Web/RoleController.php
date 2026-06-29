<?php

namespace App\Http\Controllers\Web;

use App\DataTables\RolesDataTable;
use App\Http\Requests\Admin\Role\CreateRequest;
use App\Http\Requests\Admin\Role\UpdateRequest;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Support\SecureRouteParameter;
use DB;
use Exception;
use Illuminate\Http\Response;

class RoleController extends WebController
{
    protected $roleRepository;

    protected $permissionRepository;

    protected $dbObject;

    public function __construct(RoleRepository $roleRepository, PermissionRepository $permissionRepository)
    {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->dbObject = DB::class;
        $this->indexRouteName = 'admin.roles.index';
        $this->middleware(['permission:role-list'], ['only' => ['index']]);
        $this->middleware(['permission:role-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:role-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:role-delete'], ['only' => ['destroy']]);
        $this->middleware(['permission:role-show'], ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index(RolesDataTable $dataTable)
    {
        return $dataTable->render('role.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $permissions = [
            'children' => $this->permissionRepository->getAllData(),
        ];

        return view('role.create', compact('permissions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(CreateRequest $request)
    {
        try {
            $requestData = $this->roleRepository->getDataFromRequest($request);
            $permissions = array_merge($requestData['parents'], $requestData['children']);
            unset($requestData['parents'], $requestData['children']);
            $this->dbObject::beginTransaction();
            $requestData['guard_name'] = config('constants.guard_name');
            $roleData = $this->roleRepository->createData($requestData);
            if (! empty($permissions)) {
                $roleData->permissions()->sync($permissions);
            }
            $this->dbObject::commit();

            return $this->successResponse($this->indexRouteName, trans('app.data_created', ['action' => __('labels.role_permission')]));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        $role = $this->roleRepository->getDataById($this->routeRoleId($id));
        $permissions = [
            'children' => $this->permissionRepository->getAllData(),
        ];
        $rolePermissionIds = $role->permissions->pluck('id')->toArray();

        return view('role.edit', compact('role', 'permissions', 'rolePermissionIds'));
    }

    /**
     * Show the details of a roles by their encrypted ID.
 
     *
     * @param  string  $id  The encrypted role ID
     * @return Response
     */
    public function show($id)
    {
        $role = $this->roleRepository->getDataById($this->routeRoleId($id));

        return view('role.show', compact('role'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(UpdateRequest $request, $id)
    {
        try {
            $id = $this->routeRoleId($id);
            $requestData = $this->roleRepository->getDataFromRequest($request);
            $permissions = array_merge($requestData['parents'], $requestData['children']);
            unset($requestData['parents'], $requestData['children']);
            $this->dbObject::beginTransaction();
            $role = $this->roleRepository->updateData($id, $requestData);
            $role->permissions()->sync($permissions);
            $this->dbObject::commit();

            return $this->successResponse($this->indexRouteName, trans('app.data_updated', ['action' => __('labels.role_permission')]));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        try {
            $role = $this->roleRepository->deleteDataById($this->routeRoleId($id));
            if ($role) {
                $role->permissions()->detach();
            }

            return $this->successAjaxResponse($this->indexRouteName, trans('app.data_deleted', ['action' => __('labels.role_permission')]));
        } catch (Exception $exception) {
            return $this->errorAjaxResponse($exception);
        }
    }

    private function routeRoleId(string $id): string
    {
        $roleId = SecureRouteParameter::decode($id);

        return $roleId ?? abort(404);
    }
}
