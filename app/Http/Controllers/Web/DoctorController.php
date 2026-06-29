<?php

namespace App\Http\Controllers\Web;

use App\DataTables\DoctorsDataTable;
use App\Enums\CommonStatus;
use App\Helpers\UserHelper;
use App\Http\Requests\Doctor\CreateRequest;
use App\Http\Requests\Doctor\UpdateRequest;
use App\Models\Role;
use App\Models\User;
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
        public UserService $userService
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
        $permissionGroups = $this->getPermissionGroups();
        $packages = \App\Models\Package::all();
        return view('doctors.create', compact('permissionGroups', 'packages'));
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

            $this->dbObject::beginTransaction();
            $user = $this->userService->createData($requestData);
            
            $doctorRole = Role::where('name', config('constants.doctor_role_name'))->first();
            if ($doctorRole) {
                $user->assignRole($doctorRole);
            }
            
            // Sync direct doctor permissions
            $user->syncPermissions($request->input('permissions', []));

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
        $statusData = array_map(fn($status) => [
            'id' => $status->value,
            'label' => __('labels.' . $status->value),
        ], CommonStatus::cases());
        $permissionGroups = $this->getPermissionGroups();
        $packages = \App\Models\Package::all();

        return view('doctors.edit', compact('user', 'statusData', 'permissionGroups', 'packages'));
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
     * Update doctor profile data, image.
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
                if (! empty($user->profile_pic)) {
                    UserHelper::deleteImage($destinationPath, $filename);
                }
                $requestData['profile_pic'] = basename(UserHelper::uploadImage($request->file('profile_pic'), $destinationPath));
            }
            $this->dbObject::beginTransaction();
            $user = $this->userService->updateData($doctorId, $requestData);
            
            // Sync direct doctor permissions
            $user->syncPermissions($request->input('permissions', []));

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
            $this->userService->deleteDataById($doctorId);

            return $this->successResponse('admin.doctors.index', trans('app.data_deleted', ['action' => 'Doctor']));
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    /**
     * Get the defined doctor panel permission groups.
     */
    private function getPermissionGroups(): array
    {
        return [
            'Appointment' => [
                'list' => 'appointment-list',
                'create' => 'appointment-create',
                'edit' => 'appointment-edit',
                'show' => 'appointment-show',
                'delete' => 'appointment-delete',
                'export' => 'appointment-export',
            ],
            'Ask Skoracare' => [
                'list' => 'ask-skoracare-list',
                'create' => 'ask-skoracare-create',
                'edit' => 'ask-skoracare-edit',
                'show' => 'ask-skoracare-show',
                'delete' => 'ask-skoracare-delete',
                'export' => 'ask-skoracare-export',
            ],
            'OPD Billing / Clinics' => [
                'list' => 'clinic-list',
                'create' => 'clinic-create',
                'edit' => 'clinic-edit',
                'show' => 'clinic-show',
                'delete' => 'clinic-delete',
                'export' => 'clinic-export',
            ],
            'All Patients' => [
                'list' => 'patients-list',
                'create' => 'patients-create',
                'edit' => 'patients-edit',
                'show' => 'patients-show',
                'delete' => 'patients-delete',
                'export' => 'patients-export',
            ],
            'Follow Up' => [
                'list' => 'follow-up-list',
                'create' => 'follow-up-create',
                'edit' => 'follow-up-edit',
                'show' => 'follow-up-show',
                'delete' => 'follow-up-delete',
                'export' => 'follow-up-export',
            ],
            'Pharmacy' => [
                'list' => 'pharmacy-list',
                'create' => 'pharmacy-create',
                'edit' => 'pharmacy-edit',
                'show' => 'pharmacy-show',
                'delete' => 'pharmacy-delete',
                'export' => 'pharmacy-export',
            ],
            'Data Analytics' => [
                'list' => 'analytics-list',
                'create' => 'analytics-create',
                'edit' => 'analytics-edit',
                'show' => 'analytics-show',
                'delete' => 'analytics-delete',
                'export' => 'analytics-export',
            ],
            'Messages' => [
                'list' => 'messages-list',
                'create' => 'messages-create',
                'edit' => 'messages-edit',
                'show' => 'messages-show',
                'delete' => 'messages-delete',
                'export' => 'messages-export',
            ],
        ];
    }
}
