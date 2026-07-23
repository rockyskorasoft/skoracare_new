<?php

namespace App\Http\Controllers\Web;

use App\DataTables\StaffDataTable;
use App\Helpers\UserHelper;
use App\Http\Requests\Staff\CreateRequest;
use App\Http\Requests\Staff\UpdateRequest;
use App\Models\Clinic;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\UserService;
use App\Support\SecureRouteParameter;
use DB;
use Exception;
use Illuminate\Support\Facades\Hash;

class StaffController extends WebController
{
    protected $dbObject;

    public function __construct(
        public UserService $userService
    ) {
        $this->dbObject = DB::class;
        $this->middleware(['permission:staff-list|user-list'], ['only' => ['index']]);
        $this->middleware(['permission:staff-create|user-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:staff-edit|user-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:staff-delete|user-delete'], ['only' => ['destroy']]);
        $this->middleware(['permission:staff-show|user-show'], ['only' => ['show']]);
    }

    /**
     * Render staff listing.
     */
    public function index(StaffDataTable $dataTable)
    {
        return $dataTable->render('staff.index');
    }

    /**
     * Show create staff form.
     */
    public function create()
    {
        $authUser = UserHelper::getLoggedInUser();
        if (!$authUser->canCreateUser()) {
            return redirect()->route('admin.staff.index')->with('error', 'You have reached your maximum staff creation limit according to your assigned package plan.');
        }

        $doctors = User::role(config('constants.doctor_role_name'))->get();
        
        // Clinics available for selection
        if ($authUser->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')])) {
            $clinics = Clinic::all();
            $roles = Role::whereNotIn('name', [config('constants.super_admin_role_name'), config('constants.doctor_role_name')])->get();
            $availablePermissions = Permission::whereNotNull('parent_id')->with('parent')->get();
        } else {
            $clinics = $authUser->clinics;
            $roles = Role::whereIn('name', ['Staff', 'Receptionist', 'Nurse', 'Accountant'])->get();
            if ($roles->isEmpty()) {
                $roles = Role::where('name', 'Staff')->get();
            }
            // Scoped permissions: Doctor can ONLY assign permissions the doctor holds!
            $doctorPermIds = $authUser->getAllPermissions()->pluck('id')->toArray();
            $availablePermissions = Permission::whereIn('id', $doctorPermIds)->whereNotNull('parent_id')->with('parent')->get();
        }

        // Group permissions by category (parent)
        $groupedPermissions = $availablePermissions->groupBy(function($perm) {
            return $perm->parent ? $perm->parent->name : 'General';
        });

        return view('staff.create', compact('doctors', 'clinics', 'roles', 'groupedPermissions', 'authUser'));
    }

    /**
     * Store new staff user and attach assigned clinics & scoped permissions.
     */
    public function store(CreateRequest $request)
    {
        $authUser = UserHelper::getLoggedInUser();
        if (!$authUser->canCreateUser()) {
            return redirect()->route('admin.staff.index')->with('error', 'You have reached your maximum staff creation limit according to your assigned package plan.');
        }

        try {
            $requestData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'password' => Hash::make($request->password),
                'status' => $request->status,
                'created_by' => $request->doctor_id ?? auth()->id(),
            ];

            $this->dbObject::beginTransaction();
            $staffUser = User::create($requestData);

            // Assign Selected Role (Default: Staff)
            $roleName = $request->role ?? 'Staff';
            $staffRole = Role::where('name', $roleName)->first() ?? Role::where('name', 'Staff')->first();
            if ($staffRole) {
                $staffUser->assignRole($staffRole);
            }

            // Sync Custom Scoped Permissions if provided
            if ($request->has('permissions')) {
                $requestedPerms = (array)$request->permissions;
                if (!$authUser->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')])) {
                    // Filter strictly to doctor's own permissions
                    $doctorPermNames = $authUser->getAllPermissions()->pluck('name')->toArray();
                    $requestedPerms = array_intersect($requestedPerms, $doctorPermNames);
                }
                $staffUser->syncPermissions($requestedPerms);
            }

            // Attach assigned clinics
            if ($request->has('clinic_ids')) {
                $staffUser->assignedClinics()->sync($request->clinic_ids);
            }

            $this->dbObject::commit();

            return $this->successResponse('admin.staff.index', trans('app.data_created', ['action' => 'Staff Member']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Show staff details.
     */
    public function show(string $id)
    {
        $staffId = SecureRouteParameter::decodeOrFail($id);
        $staff = User::with(['creator', 'assignedClinics', 'roles', 'permissions'])->findOrFail($staffId);

        return view('staff.show', compact('staff'));
    }

    /**
     * Show edit staff form.
     */
    public function edit(string $id)
    {
        $staffId = SecureRouteParameter::decodeOrFail($id);
        $staff = User::with(['assignedClinics', 'roles', 'permissions'])->findOrFail($staffId);
        $authUser = UserHelper::getLoggedInUser();

        $doctors = User::role(config('constants.doctor_role_name'))->get();

        if ($authUser->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')])) {
            $clinics = Clinic::all();
            $roles = Role::whereNotIn('name', [config('constants.super_admin_role_name'), config('constants.doctor_role_name')])->get();
            $availablePermissions = Permission::whereNotNull('parent_id')->with('parent')->get();
        } else {
            $clinics = $authUser->clinics;
            $roles = Role::whereIn('name', ['Staff', 'Receptionist', 'Nurse', 'Accountant'])->get();
            if ($roles->isEmpty()) {
                $roles = Role::where('name', 'Staff')->get();
            }
            $doctorPermIds = $authUser->getAllPermissions()->pluck('id')->toArray();
            $availablePermissions = Permission::whereIn('id', $doctorPermIds)->whereNotNull('parent_id')->with('parent')->get();
        }

        $groupedPermissions = $availablePermissions->groupBy(function($perm) {
            return $perm->parent ? $perm->parent->name : 'General';
        });

        $assignedClinicIds = $staff->assignedClinics->pluck('id')->toArray();
        $assignedPermissionNames = $staff->permissions->pluck('name')->toArray();
        $staffRoleName = $staff->roles->first()?->name ?? 'Staff';

        return view('staff.edit', compact('staff', 'doctors', 'clinics', 'roles', 'groupedPermissions', 'assignedClinicIds', 'assignedPermissionNames', 'staffRoleName', 'authUser'));
    }

    /**
     * Update staff profile, role, scoped permissions & clinic assignments.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $staffId = SecureRouteParameter::decodeOrFail($id);
        $staff = User::findOrFail($staffId);
        $authUser = UserHelper::getLoggedInUser();

        try {
            $requestData = [
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'phone_no' => $request->phone_no,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $requestData['password'] = Hash::make($request->password);
            }

            if ($request->filled('doctor_id')) {
                $requestData['created_by'] = $request->doctor_id;
            }

            $this->dbObject::beginTransaction();
            $staff->update($requestData);

            // Sync Role
            if ($request->filled('role')) {
                $staff->syncRoles([$request->role]);
            }

            // Sync Permissions
            if ($request->has('permissions')) {
                $requestedPerms = (array)$request->permissions;
                if (!$authUser->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')])) {
                    $doctorPermNames = $authUser->getAllPermissions()->pluck('name')->toArray();
                    $requestedPerms = array_intersect($requestedPerms, $doctorPermNames);
                }
                $staff->syncPermissions($requestedPerms);
            }

            // Sync Clinics
            if ($request->has('clinic_ids')) {
                $staff->assignedClinics()->sync($request->clinic_ids);
            } else {
                $staff->assignedClinics()->detach();
            }

            $this->dbObject::commit();

            return $this->successResponse('admin.staff.index', trans('app.data_updated', ['action' => 'Staff Member']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Delete staff user.
     */
    public function destroy(string $id)
    {
        try {
            $staffId = SecureRouteParameter::decodeOrFail($id);
            $staff = User::findOrFail($staffId);
            $staff->assignedClinics()->detach();
            $staff->delete();

            return $this->successResponse('admin.staff.index', trans('app.data_deleted', ['action' => 'Staff Member']));
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }
}
