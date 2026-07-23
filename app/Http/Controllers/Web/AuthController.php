<?php

namespace App\Http\Controllers\Web;

use App\Enums\CommonStatus;
use App\Helpers\UserHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\User\UpdatePasswordRequest;
use App\Http\Requests\User\UpdateUserProfileRequest;
use App\Models\User;
use App\Services\UserService;
use App\Traits\WebResponseTrait;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use WebResponseTrait;

    /**
     * Inject user service for profile and password related operations.
     */
    public function __construct(public UserService $userService) {}

    /**
     * Render login page for guest users.
     */
    public function loginView()
    {
        return view('auth.login');
    }

    /**
     * Authenticate user and redirect to intended admin dashboard route.
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $remember = $request->boolean('remember_me');
        $users = User::where('email', $request->email)->get();
        $matchedUsers = $users->filter(fn ($user) => $user->password && Hash::check($request->password, $user->password));

        if ($matchedUsers->count() === 1) {
            $user = $matchedUsers->first();
            if ($user->status === CommonStatus::INACTIVE->value) {
                return redirect('/login')->with('error', trans('app.auth.login.inactive_access'));
            }

            auth()->login($user, $remember);
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard.index'));
        }

        if ($matchedUsers->count() > 1) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => trans('app.auth.login.ambiguous_login')]);
        }

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'The provided credentials do not match our records.']);
    }

    /**
     * Logout authenticated user and invalidate session securely.
     */
    public function logout(Request $request): RedirectResponse
    {
        auth()->guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Show logged-in user's profile edit page.
     */
    public function editProfile()
    {
        $loggedInUser = UserHelper::getLoggedInUser()->id;
        $user = $this->userService->getDataById($loggedInUser);

        return view('auth.profile', compact('user'));
    }

    /**
     * Render change password screen for logged-in user.
     */
    public function changePassword()
    {
        return view('auth.change-password');
    }

    /**
     * Update user profile details including optional profile image.
     */
    public function updateProfile(UpdateUserProfileRequest $request, $id)
    {
        try {
            $user = $this->userService->getDataById($id);
            if (! $user) {
                return $this->errorResponse(new Exception(__('labels.user_not_found')));
            }
            $requestData = $this->userService->getDataFromRequest($request);
            if ($request->hasFile('profile_pic')) {
                $user = $this->userService->getDataById($id);
                $destinationPath = 'profile_images';
                $filename = $user->profile_pic;
                if (! empty($user->profile_pic)) {
                    UserHelper::deleteImage($destinationPath, $filename);
                }
                $requestData['profile_pic'] = basename(UserHelper::uploadImage($request->file('profile_pic'), $destinationPath));
            }
            $this->userService->updateData($id, $requestData);

            return $this->successResponse($this->dashboardRouteName(), trans('app.data_updated', ['action' => __('labels.user_profile')]));
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    /**
     * Update logged-in user's password.
     */
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $requestData = $this->userService->getDataFromRequest($request);
        $requestData = ['password' => $requestData['password']];
        try {
            $userId = UserHelper::getLoggedInUser()->id;
            $this->userService->updateData($userId, $requestData);

            return $this->successResponse($this->dashboardRouteName(), trans('app.data_updated', ['action' => __('labels.password')]));
        } catch (Exception $exception) {
            return $this->errorResponse($exception);
        }
    }

    private function dashboardRouteName(): string
    {
        return 'admin.dashboard.index';
    }
}
