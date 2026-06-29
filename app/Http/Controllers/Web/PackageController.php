<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Package;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PackageController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:package-list'], ['only' => ['index']]);
        $this->middleware(['permission:package-create'], ['only' => ['create', 'store']]);
        $this->middleware(['permission:package-edit'], ['only' => ['edit', 'update']]);
        $this->middleware(['permission:package-delete'], ['only' => ['destroy']]);
    }

    public function index()
    {
        $packages = Package::withCount('users')->get();
        return view('packages.index', compact('packages'));
    }

    public function create()
    {
        $groupedPermissions = $this->getGroupedPermissions();
        return view('packages.create', compact('groupedPermissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:packages,name',
            'clinic_limit' => 'required|integer|min:-1',
            'user_limit' => 'required|integer|min:-1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();
            $package = Package::create([
                'name' => $request->name,
                'clinic_limit' => $request->clinic_limit,
                'user_limit' => $request->user_limit,
            ]);

            if ($request->has('permissions')) {
                $package->permissions()->sync($request->permissions);
            }
            DB::commit();

            return redirect()->route('admin.packages.index')
                ->with('success', 'Package created successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error creating package: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $package = Package::with('permissions')->findOrFail($id);
        $groupedPermissions = $this->getGroupedPermissions();
        $assignedPermissionIds = $package->permissions->pluck('id')->toArray();

        return view('packages.edit', compact('package', 'groupedPermissions', 'assignedPermissionIds'));
    }

    public function update(Request $request, $id)
    {
        $package = Package::findOrFail($id);
        
        $request->validate([
            'name' => 'required|string|max:255|unique:packages,name,' . $id,
            'clinic_limit' => 'required|integer|min:-1',
            'user_limit' => 'required|integer|min:-1',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();
            $package->update([
                'name' => $request->name,
                'clinic_limit' => $request->clinic_limit,
                'user_limit' => $request->user_limit,
            ]);

            $package->permissions()->sync($request->input('permissions', []));
            DB::commit();

            return redirect()->route('admin.packages.index')
                ->with('success', 'Package updated successfully.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Error updating package: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
        try {
            $package = Package::findOrFail($id);
            $package->delete();

            return redirect()->route('admin.packages.index')
                ->with('success', 'Package deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.packages.index')
                ->with('error', 'Error deleting package: ' . $e->getMessage());
        }
    }

    private function getGroupedPermissions()
    {
        $permissions = Permission::with('parent')->get();
        return $permissions->groupBy(function ($perm) {
            return $perm->parent ? ucwords(str_replace('-', ' ', $perm->parent->name)) : 'General/Other';
        });
    }
}
