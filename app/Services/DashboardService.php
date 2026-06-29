<?php

namespace App\Services;

use App\Models\Activity;
use App\Models\User;
use App\Models\Role;
use App\Models\Clinic;
use Illuminate\Database\Eloquent\Builder;

class DashboardService
{
    /**
     * Get the dashboard data.
     *
     * @return array
     */
    public function getDashboardData(): array
    {
        $canViewActivities = auth()->user()?->can('dashboard-latest-activities-card') ?? false;

        return [
            'stats' => $this->globalStats(),
            'latestActivities' => $canViewActivities ? $this->latestActivities() : collect(),
            'showLatestActivities' => $canViewActivities,
            'activityLogUrl' => $canViewActivities
                ? $this->routeIfCan('activity-log-list', 'admin.activity-log.index')
                : null,
        ];
    }

    /**
     * Build dashboard cards for admins.
     */
    private function globalStats(): array
    {
        return $this->visibleCards([
            $this->statCard('dashboard-users-card', __('labels.users'), User::count(), 'fa-solid fa-users', $this->routeIfCan('user-list', 'admin.users.index')),
            $this->statCard('doctor-list', 'Doctors', User::role(config('constants.doctor_role_name'))->count(), 'fa-solid fa-user-doctor', $this->routeIfCan('doctor-list', 'admin.doctors.index')),
            $this->statCard('clinic-list', __('labels.clinics'), Clinic::count(), 'fa-solid fa-hospital', $this->routeIfCan('clinic-list', 'admin.clinics.index')),
            $this->statCard('role-list', __('labels.roles'), Role::count(), 'fa-solid fa-users-gear', $this->routeIfCan('role-list', 'admin.roles.index')),
        ]);
    }

    /**
     * Create a normalized stat card payload.
     */
    private function statCard(string $permission, string $label, int $value, string $icon, ?string $url = null): array
    {
        return compact('permission', 'label', 'value', 'icon', 'url');
    }

    /**
     * Keep only dashboard cards the current user is allowed to see.
     */
    private function visibleCards(array $cards): array
    {
        $user = auth()->user();

        if (! $user) {
            return [];
        }

        return array_values(array_filter(
            $cards,
            fn (array $card) => $user->can($card['permission'])
        ));
    }

    /**
     * Return a route only when the current user can access that listing.
     */
    private function routeIfCan(string $ability, string $routeName): ?string
    {
        return auth()->user()?->can($ability) ? route($routeName) : null;
    }

    /**
     * Fetch the latest activity rows for the dashboard table.
     */
    private function latestActivities()
    {
        $query = Activity::query();

        $this->scopeActivityToCurrentUser($query);

        return $query
            ->with(['createdBy', 'causer', 'subject'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn (Activity $activity) => [
                'name' => $activity->log_name ?? 'N/A',
                'description' => $activity->description ?? 'N/A',
                'changes' => $this->changesPreview($activity),
                'ip_address' => $activity->ip_address ?? 'N/A',
                'created_by' => $this->userName($activity->createdBy ?: $activity->causer),
                'created_at' => $activity->created_at ?? 'N/A',
                'show_url' => $this->activityShowRoute($activity),
            ]);
    }

    /**
     * Limit activity rows to the current user unless the user is Super Admin.
     */
    private function scopeActivityToCurrentUser(Builder $query): void
    {
        $user = auth()->user();

        if (! $user || $user->hasRole(config('constants.super_admin_role_name'))) {
            return;
        }

        $query->where(function (Builder $activityQuery) use ($user) {
            $activityQuery->where('causer_id', $user->id)
                ->orWhere('created_by', $user->id);
        });
    }

    /**
     * Return the activity modal URL when available to the current user.
     */
    private function activityShowRoute(Activity $activity): ?string
    {
        if (! auth()->user()?->can('activity-log-show')) {
            return null;
        }

        return route('admin.activity-log.show', encrypt($activity->id));
    }

    /**
     * Get a readable user name for activity display.
     */
    private function userName($user): string
    {
        if (! $user) {
            return 'N/A';
        }

        return trim(($user->first_name ?? '').' '.($user->last_name ?? '')) ?: $user->email ?? 'N/A';
    }

    /**
     * Get a compact changes preview like the activity log listing.
     */
    private function changesPreview(Activity $activity): string
    {
        $changes = $activity->changeLines();

        if (empty($changes)) {
            return 'N/A';
        }

        $visibleChanges = array_slice($changes, 0, 2);
        $remainingCount = count($changes) - count($visibleChanges);

        if ($remainingCount > 0) {
            $visibleChanges[] = 'and '.$remainingCount.' more...';
        }

        return implode("\n", $visibleChanges);
    }
}
