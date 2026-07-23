<?php

namespace App\Http\Controllers\Web;

use App\DataTables\DoctorsDataTable;
use App\Enums\CommonStatus;
use App\Helpers\UserHelper;
use App\Http\Requests\Doctor\CreateRequest;
use App\Http\Requests\Doctor\UpdateRequest;
use App\Models\Package;
use App\Models\Role;
use App\Models\User;
use App\Repositories\PermissionRepository;
use App\Services\UserService;
use App\Support\SecureRouteParameter;
use DB;
use Exception;
use Illuminate\Support\Facades\Password;

class DoctorController extends WebController
{
    /**
     * Database facade class reference for transaction handling.
     */
    protected $dbObject;

    /**
     * Configure dependencies and permission middleware.
     */
    public function __construct(
        public UserService $userService,
        public PermissionRepository $permissionRepository
    ) {
        $this->dbObject = DB::class;
        $this->middleware(['permission:doctor-list'], ['only' => ['index']]);
        $this->middleware(['permission:doctor-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:doctor-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:doctor-delete'], ['only' => ['destroy']]);
        $this->middleware(['permission:doctor-show'], ['only' => ['show']]);
    }

    /**
     * Render doctor listing with server-side datatable.
     */
    public function index(DoctorsDataTable $dataTable)
    {
        return $dataTable->render('doctors.index');
    }

    /**
     * Show create doctor form.
     */
    public function create()
    {
        $permissions = [
            'children' => $this->permissionRepository->getAllData(),
        ];
        $packages = Package::with('permissions')->where('status', 'active')->get();

        $packagePermissionsMap = $packages->mapWithKeys(function ($pkg) {
            return [$pkg->id => [
                'permission_ids' => $pkg->permissions->pluck('id')->toArray(),
                'clinic_limit' => $pkg->clinic_limit,
                'user_limit' => $pkg->user_limit,
            ]];
        });

        return view('doctors.create', compact('permissions', 'packages', 'packagePermissionsMap'));
    }

    /**
     * Create doctor user, assign doctor role, and send password reset setup email.
     */
    public function store(CreateRequest $request)
    {
        try {
            $requestData = $this->userService->getDataFromRequest($request);
            if ($request->hasFile('profile_pic')) {
                $destinationPath = 'profile_images';
                $requestData['profile_pic'] = basename(UserHelper::uploadImage($request->file('profile_pic'), $destinationPath));
            }

            $statusId = CommonStatus::ACTIVE->value;
            $requestData['status'] = $statusId;
            $requestData['created_by'] = auth()->id();

            $this->dbObject::beginTransaction();
            $user = $this->userService->createData($requestData);

            $doctorRole = Role::where('name', config('constants.doctor_role_name'))->first();
            if ($doctorRole) {
                $user->assignRole($doctorRole);
            }

            // Sync permissions from tree
            $parents = $request->input('parents', []);
            $children = $request->input('children', []);
            $permissionIds = array_filter(array_merge($parents, $children));

            // Also check direct package sync if package selected
            if ($request->filled('package_id')) {
                $pkg = Package::find($request->package_id);
                if ($pkg) {
                    $packagePermIds = $pkg->permissions->pluck('id')->toArray();
                    $permissionIds = array_unique(array_merge($permissionIds, $packagePermIds));
                }
            }

            if (!empty($permissionIds)) {
                $user->permissions()->sync($permissionIds);
            }

            $this->dbObject::commit();
            Password::sendResetLink(['email' => $user->email]);

            return $this->successResponse('admin.doctors.index', trans('app.data_created', ['action' => 'Doctor']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Show edit form for a doctor.
     */
    public function edit(string $id)
    {
        $doctorId = SecureRouteParameter::decodeOrFail($id);
        $user = $this->userService->getDataById($doctorId);
        $user->load('permissions');

        $statusData = array_map(fn($status) => [
            'id' => $status->value,
            'label' => __('labels.' . $status->value),
        ], CommonStatus::cases());

        $permissions = [
            'children' => $this->permissionRepository->getAllData(),
        ];
        $userPermissionIds = $user->permissions->pluck('id')->toArray();

        $packages = Package::with('permissions')->get();
        $packagePermissionsMap = $packages->mapWithKeys(function ($pkg) {
            return [$pkg->id => [
                'permission_ids' => $pkg->permissions->pluck('id')->toArray(),
                'clinic_limit' => $pkg->clinic_limit,
                'user_limit' => $pkg->user_limit,
            ]];
        });

        return view('doctors.edit', compact('user', 'statusData', 'permissions', 'userPermissionIds', 'packages', 'packagePermissionsMap'));
    }

    /**
     * Show doctor details.
     */
    public function show(string $id)
    {
        $doctorId = SecureRouteParameter::decodeOrFail($id);
        $user = $this->userService->getDataById($doctorId);

        return view('doctors.show', compact('user'));
    }

    /**
     * Update doctor profile data, image, package & permissions.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $doctorId = SecureRouteParameter::decodeOrFail($id);
        $requestData = $this->userService->getDataFromRequest($request);

        try {
            if ($request->hasFile('profile_pic')) {
                $user = $this->userService->getDataById($doctorId);
                $destinationPath = 'profile_images';
                $filename = $user->profile_pic;
                if (!empty($user->profile_pic)) {
                    UserHelper::deleteImage($destinationPath, $filename);
                }
                $requestData['profile_pic'] = basename(UserHelper::uploadImage($request->file('profile_pic'), $destinationPath));
            }

            $this->dbObject::beginTransaction();
            $user = $this->userService->updateData($doctorId, $requestData);

            // Sync direct permissions from tree
            $parents = $request->input('parents', []);
            $children = $request->input('children', []);
            $permissionIds = array_filter(array_merge($parents, $children));

            // Also include package permissions if package selected
            if ($request->filled('package_id')) {
                $pkg = Package::find($request->package_id);
                if ($pkg) {
                    $packagePermIds = $pkg->permissions->pluck('id')->toArray();
                    $permissionIds = array_unique(array_merge($permissionIds, $packagePermIds));
                }
            }

            $user->permissions()->sync($permissionIds);

            $this->dbObject::commit();

            return $this->successResponse('admin.doctors.index', trans('app.data_updated', ['action' => 'Doctor']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Delete doctor user.
     */
    public function destroy(string $id)
    {
        try {
            $doctorId = SecureRouteParameter::decodeOrFail($id);
            $user = $this->userService->getDataById($doctorId);
            if (!empty($user->profile_pic)) {
                UserHelper::deleteImage('profile_images', $user->profile_pic);
            }
            $this->userService->deleteDataById($doctorId);

            return $this->successResponse('admin.doctors.index', trans('app.data_deleted', ['action' => 'Doctor']));
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }
}
