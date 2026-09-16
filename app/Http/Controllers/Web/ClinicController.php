<?php

namespace App\Http\Controllers\Web;

use App\DataTables\ClinicsDataTable;
use App\Helpers\UserHelper;
use App\Http\Requests\Clinic\CreateRequest;
use App\Http\Requests\Clinic\UpdateRequest;
use App\Models\User;
use App\Services\ClinicService;
use App\Support\SecureRouteParameter;
use DB;
use Exception;

class ClinicController extends WebController
{
    /**
     * Database facade class reference for transaction handling.
     */
    protected $dbObject;

    /**
     * Configure dependencies and permission middleware.
     */
    public function __construct(
        public ClinicService $clinicService
    ) {
        $this->dbObject = DB::class;
        $this->middleware(['permission:clinic-list'], ['only' => ['index']]);
        $this->middleware(['permission:clinic-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:clinic-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:clinic-delete'], ['only' => ['destroy']]);
        $this->middleware(['permission:clinic-show'], ['only' => ['show']]);

        // Restrict clinic management CRUD routes to Super Admin only
        $this->middleware(function ($request, $next) {
            $user = auth()->user();
            if (!$user || !$user->hasRole(config('constants.super_admin_role_name'))) {
                abort(403, 'Unauthorized. Clinic management is restricted to Super Admin.');
            }
            return $next($request);
        })->except(['switchClinic']);
    }

    /**
     * Render clinic listing with server-side datatable.
     */
    public function index(ClinicsDataTable $dataTable)
    {
        return $dataTable->render('clinics.index');
    }

    /**
     * Show create clinic form.
     */
    public function create()
    {
        $user = UserHelper::getLoggedInUser();
        if (!$user->canCreateClinic()) {
            return redirect()->route('admin.clinics.index')->with('error', 'You have reached your maximum clinic creation limit according to your assigned package plan. Please upgrade your package or contact Admin.');
        }

        $doctors = User::role(config('constants.doctor_role_name'))->get();
        return view('clinics.create', compact('doctors'));
    }

    /**
     * Create clinic.
     */
    public function store(CreateRequest $request)
    {
        $user = UserHelper::getLoggedInUser();
        if (!$user->canCreateClinic()) {
            return redirect()->route('admin.clinics.index')->with('error', 'You have reached your maximum clinic creation limit according to your assigned package plan. Please upgrade your package or contact Admin.');
        }

        try {
            $requestData = $this->clinicService->getDataFromRequest($request);
            if (!empty($request->doctor_ids)) {
                $requestData['doctor_id'] = $request->doctor_ids[0];
            }
            if ($request->hasFile('logo')) {
                $destinationPath = 'clinic_logos';
                $requestData['logo'] = basename(UserHelper::uploadImage($request->file('logo'), $destinationPath));
            }

            $this->dbObject::beginTransaction();
            $clinic = $this->clinicService->createData($requestData);

            if ($request->has('doctor_ids') && is_array($request->doctor_ids)) {
                $clinic->assignedUsers()->sync($request->doctor_ids);
            }

            $this->dbObject::commit();

            return $this->successResponse('admin.clinics.index', trans('app.data_created', ['action' => 'Clinic']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Show edit form for a clinic.
     */
    public function edit(string $id)
    {
        $clinicId = SecureRouteParameter::decodeOrFail($id);
        $clinic = $this->clinicService->getDataById($clinicId);
        $doctors = User::role(config('constants.doctor_role_name'))->get();

        $assignedDoctorIds = $clinic->assignedUsers()
            ->role(config('constants.doctor_role_name'))
            ->pluck('users.id')
            ->toArray();

        if (empty($assignedDoctorIds) && $clinic->doctor_id) {
            $assignedDoctorIds = [(int)$clinic->doctor_id];
        }

        return view('clinics.edit', compact('clinic', 'doctors', 'assignedDoctorIds'));
    }

    /**
     * Show clinic details.
     */
    public function show(string $id)
    {
        $clinicId = SecureRouteParameter::decodeOrFail($id);
        $clinic = $this->clinicService->getDataById($clinicId);
        $assignedDoctors = $clinic->assignedUsers()
            ->role(config('constants.doctor_role_name'))
            ->get();

        if ($assignedDoctors->isEmpty() && $clinic->doctor) {
            $assignedDoctors = collect([$clinic->doctor]);
        }

        return view('clinics.show', compact('clinic', 'assignedDoctors'));
    }

    /**
     * Update clinic.
     */
    public function update(UpdateRequest $request, string $id)
    {
        $clinicId = SecureRouteParameter::decodeOrFail($id);
        $requestData = $this->clinicService->getDataFromRequest($request);
        if (!empty($request->doctor_ids)) {
            $requestData['doctor_id'] = $request->doctor_ids[0];
        }
        try {
            $clinic = $this->clinicService->getDataById($clinicId);
            if ($request->hasFile('logo')) {
                $destinationPath = 'clinic_logos';
                $filename = $clinic->logo;
                if (! empty($clinic->logo)) {
                    UserHelper::deleteImage($destinationPath, $filename);
                }
                $requestData['logo'] = basename(UserHelper::uploadImage($request->file('logo'), $destinationPath));
            }
            $this->dbObject::beginTransaction();
            $this->clinicService->updateData($clinicId, $requestData);

            if ($request->has('doctor_ids') && is_array($request->doctor_ids)) {
                // Preserve non-doctor staff assigned to this clinic
                $staffIds = $clinic->assignedUsers()
                    ->whereDoesntHave('roles', fn($q) => $q->where('name', config('constants.doctor_role_name')))
                    ->pluck('users.id')
                    ->toArray();
                $allUserIds = array_unique(array_merge($request->doctor_ids, $staffIds));
                $clinic->assignedUsers()->sync($allUserIds);
            }

            $this->dbObject::commit();

            return $this->successResponse('admin.clinics.index', trans('app.data_updated', ['action' => 'Clinic']));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Delete clinic.
     */
    public function destroy(string $id)
    {
        try {
            $clinicId = SecureRouteParameter::decodeOrFail($id);
            $clinic = $this->clinicService->getDataById($clinicId);
            if (! empty($clinic->logo)) {
                UserHelper::deleteImage('clinic_logos', $clinic->logo);
            }
            $this->clinicService->deleteDataById($clinicId);

            return $this->successResponse('admin.clinics.index', trans('app.data_deleted', ['action' => 'Clinic']));
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    /**
     * Switch active clinic in session for the user.
     */
    public function switchClinic(\Illuminate\Http\Request $request)
    {
        $user = auth()->user();
        $clinicId = $request->input('clinic_id');

        if ($clinicId === 'all') {
            if ($user && $user->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')])) {
                session(['active_clinic_id' => 'all']);
                return response()->json([
                    'success' => true,
                    'message' => 'Switched to All Clinics view',
                    'clinic_name' => 'All Clinics',
                ]);
            }
            return response()->json(['success' => false, 'message' => 'Unauthorized for global view'], 403);
        }

        if ($clinicId) {
            $clinic = \App\Models\Clinic::find($clinicId);
            if (!$clinic) {
                return response()->json(['success' => false, 'message' => 'Clinic not found'], 404);
            }

            // Verify User owns or is assigned to this clinic
            if ($user && !$user->hasRole([config('constants.super_admin_role_name'), config('constants.admin_role_name')])) {
                if ($user->hasRole(config('constants.doctor_role_name'))) {
                    $userClinicIds = $user->clinics()->pluck('id')->toArray();
                } else {
                    $userClinicIds = $user->assignedClinics()->pluck('id')->toArray();
                    if (empty($userClinicIds) && $user->creator) {
                        $userClinicIds = $user->creator->clinics()->pluck('id')->toArray();
                    }
                }

                if (!in_array((int)$clinicId, $userClinicIds)) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized clinic access'], 403);
                }
            }

            session(['active_clinic_id' => (int)$clinicId]);

            return response()->json([
                'success' => true,
                'message' => 'Active clinic updated successfully',
                'clinic_name' => $clinic->name ?? 'Skoracare',
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Invalid clinic ID'], 400);
    }
}
