<?php

use App\Models\BusinessOwnerPermission;

if (!function_exists('hasOwnerPermission')) {
    /**
     * Check if a given owner has a specific permission.
     *
     * @param int $ownerId
     * @param string $key
     * @return bool
     */
    function hasOwnerPermission($ownerId, $key)
    {
       $permission = BusinessOwnerPermission::where('owner_id', $ownerId)
            ->where('key', $key)
            ->first(); 
            
        if ($permission && $permission['value'] == 1) {
            return true;
        }

        return false;
    }
}