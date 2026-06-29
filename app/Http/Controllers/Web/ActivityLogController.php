<?php

namespace App\Http\Controllers\Web;

use App\DataTables\ActivityLogDataTable;
use App\Http\Controllers\Controller;
use App\Services\ActivityLogService;
use App\Services\UserService;

class ActivityLogController extends Controller
{
    /**
     * Initialize dashboard controller.
     */
    public function __construct(Public UserService $userService, Public ActivityLogService $logRepository)
    {
        $this->middleware(['permission:activity-log-list'], ['only' => ['index']]);
        $this->middleware(['permission:activity-log-show'], ['only' => ['show']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(ActivityLogDataTable $activityDataTable)
    {
        $userData = [];

        if ($this->canViewAllActivity()) {
            $userData =  $this->userService->getData();
        }

        return $activityDataTable->render('activity-log.index', compact('userData'));
    }

    /**
     * Show user details for a decrypted user id.
     */
    public function show($id)
    {
        $logData = $this->logRepository->getDataById(decrypt($id));
        $logData->load(['createdBy', 'causer', 'subject']);
        abort_unless($this->canViewActivity($logData), 403);

        if (request()->ajax()) {
            $createdBy = $this->subjectCreatedBy($logData) ?: $logData->createdBy ?: $logData->causer;
            $changes = $logData->changeLines();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'log_id' => $logData->id,
                    'name' => $logData->log_name ?? 'N/A',
                    'description' => $logData->description ?? 'N/A',
                    'event' => $logData->event ?? 'N/A',
                    'subject' => $this->subjectName($logData->subject),
                    'causer' => $this->userName($logData->causer),
                    'ip_address' => $logData->ip_address ?? 'N/A',
                    'created_by' => $this->userName($createdBy),
                    'created_at' => $logData->created_at ?? 'N/A',
                    'changes' => empty($changes) ? 'N/A' : implode("\n", $changes),
                ],
            ]);
        }

        return view('activity-log.show', compact('logData'));
    }

    /**
     * Get the user's display name.
     */
    private function userName($user): string
    {
        if (! $user) {
            return 'N/A';
        }

        return trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: $user->email ?? 'N/A';
    }

    /**
     * Get a readable activity subject name.
     */
    private function subjectName($subject): string
    {
        if (! $subject) {
            return 'N/A';
        }

        return trim(($subject->first_name ?? '').' '.($subject->last_name ?? ''))
            ?: ($subject->name ?? null)
            ?: ($subject->product_name ?? null)
            ?: ($subject->email ?? null)
            ?: class_basename($subject);
    }

    /**
     * Get the creator from the activity subject when available.
     */
    private function subjectCreatedBy($activity)
    {
        $subject = $activity->subject;

        if (! $subject || ! method_exists($subject, 'createdBy')) {
            return null;
        }

        return $subject->createdBy;
    }

    /**
     * Determine whether the current user can view every user's activity.
     */
    private function canViewAllActivity(): bool
    {
        return auth()->user()?->hasRole(config('constants.super_admin_role_name')) ?? false;
    }

    /**
     * Determine whether the current user can view one activity row.
     */
    private function canViewActivity($activity): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        if ($this->canViewAllActivity()) {
            return true;
        }

        return (int) $activity->causer_id === (int) $user->id
            || (int) $activity->created_by === (int) $user->id;
    }
}
