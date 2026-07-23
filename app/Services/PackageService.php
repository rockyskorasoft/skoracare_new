<?php

namespace App\Services;

use App\Models\Package;
use Illuminate\Http\Request;

class PackageService
{
    /**
     * Get data array from request.
     */
    public function getDataFromRequest(Request $request): array
    {
        return [
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'monthly_price' => $request->input('monthly_price', 0),
            'yearly_price' => $request->input('yearly_price', 0),
            'clinic_limit' => $request->input('clinic_limit', -1),
            'user_limit' => $request->input('user_limit', -1),
            'status' => $request->input('status', 'active'),
            'is_popular' => $request->has('is_popular') ? (bool)$request->input('is_popular') : false,
        ];
    }

    /**
     * Create package record.
     */
    public function createData(array $data): Package
    {
        return Package::create($data);
    }

    /**
     * Get package by ID.
     */
    public function getDataById(int $id): Package
    {
        return Package::findOrFail($id);
    }

    /**
     * Update package data.
     */
    public function updateData(int $id, array $data): Package
    {
        $package = $this->getDataById($id);
        $package->update($data);
        return $package;
    }

    /**
     * Delete package data.
     */
    public function deleteDataById(int $id): bool
    {
        $package = $this->getDataById($id);
        return $package->delete();
    }
}
