<?php

namespace App\Http\Controllers\Web;

use App\DataTables\UserDataTable;
use App\Enums\CommonStatus;
use App\Helpers\UserHelper;
use App\Http\Requests\User\CreateRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Services\RoleService;
use App\Services\UserService;
use App\Support\SecureRouteParameter;
use DB;
use Exception;
use Illuminate\Support\Facades\Password;

class UserController extends WebController
{
    /**
     * Database facade class reference for transaction handling.
     */
    protected $dbObject;

    /**
     * Configure dependencies, base route name, and permission middleware.
     */
    public function __construct(
        public UserService $userService,
        public RoleService $roleService
    ) {
        $this->dbObject = DB::class;
        $this->middleware(['permission:user-list'], ['only' => ['index']]);
        $this->middleware(['permission:user-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:user-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:user-delete'], ['only' => ['destroy']]);
        $this->middleware(['permission:user-show'], ['only' => ['show']]);
    }

    /**
     * Render user listing with server-side datatable.
     */
    public function index(UserDataTable $dataTable)
    {
        return $dataTable->render('users.index');
    }

    /**
     * Show create user form with available roles.
     */
    public function create()
    {
        $user = UserHelper::getLoggedInUser();
        if (!$user->canCreateUser()) {
            return redirect()->route('admin.users.index')->with('error', 'You have reached your maximum user/staff creation limit according to your assigned package plan.');
        }

        $roles = $this->roleService->getData();

        return view('users.create', compact('roles'));
    }

    /**
     * Create user, assign role, and send password reset setup email.
     */
    public function store(CreateRequest $request)
    {
        $user = UserHelper::getLoggedInUser();
        if (!$user->canCreateUser()) {
            return redirect()->route('admin.users.index')->with('error', 'You have reached your maximum user/staff creation limit according to your assigned package plan.');
        }

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
            $userRecord = $this->userService->createData($requestData);
            $roleId = (int) $requestData['role'];
            if ($roleId) {
                $userRecord->assignRole($roleId);
            }
            $this->dbObject::commit();
            Password::sendResetLink(['email' => $user->email]);

            return $this->successResponse('admin.users.index', trans('app.data_created', ['action' => __('labels.user')]));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Show edit form for a user id from the current user scope.
     */
    public function edit(...$ignoredRouteParameters)
    {
        $roles = $this->roleService->getData();
        $user = $this->userService->getDataById(SecureRouteParameter::decodeOrFail(request()->route('user')));
        $userRole = $user->roles->first();
        $statusData = array_map(fn($status) => [
            'id' => $status->value,
            'label' => __('labels.' . $status->value),
        ], CommonStatus::cases());

        return view('users.edit', compact('user', 'roles', 'userRole', 'statusData'));
    }

    /**
     * Show user details for a user id from the current user scope.
     */
    public function show(...$ignoredRouteParameters)
    {
        $user = $this->userService->getDataById(SecureRouteParameter::decodeOrFail(request()->route('user')));

        return view('users.show', compact('user'));
    }

    /**
     * Update user profile data, image, and role mapping.
     */
    public function update(UpdateRequest $request, ...$ignoredRouteParameters)
    {
        $id = SecureRouteParameter::decodeOrFail(request()->route('user'));
        $requestData = $this->userService->getDataFromRequest($request);
        try {
            if ($request->hasFile('profile_pic')) {
                $user = $this->userService->getDataById($id);
                $destinationPath = 'profile_images';
                $filename = $user->profile_pic;
                if (! empty($user->profile_pic)) {
                    UserHelper::deleteImage($destinationPath, $filename);
                }
                $requestData['profile_pic'] = basename(UserHelper::uploadImage($request->file('profile_pic'), $destinationPath));
            }
            $this->dbObject::beginTransaction();
            $user = $this->userService->updateData($id, $requestData);
            $userRole = $user->roles->first();
            if (! empty($userRole)) {
                $user->removeRole($userRole);
            }
            $roleId = (int) $requestData['role'];
            $user->assignRole($roleId);
            $this->dbObject::commit();

            return $this->successResponse('admin.users.index', trans('app.data_updated', ['action' => __('labels.user')]));
        } catch (Exception $exception) {
            $this->dbObject::rollBack();

            return $this->errorResponse($exception);
        }
    }

    /**
     * Delete user record for a user id from the current user scope.
     */
    public function destroy(...$ignoredRouteParameters)
    {
        try {
            $this->userService->deleteDataById(SecureRouteParameter::decodeOrFail(request()->route('user')));

            return $this->successResponse('admin.users.index', trans('app.data_deleted', ['action' => __('labels.user')]));
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }
}