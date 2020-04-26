<?php

namespace App\Http\Controllers;

use Auth;

class UserData extends Controller
{
    /**
     * GET /me/storage_info
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storageInfo()
    {
        $subscription = Auth::user()->subscription;

        $usedStorage = $subscription ? $subscription->getSizeIn($subscription->used_storage, 'gb') : null;
        $total_storage = $subscription ? $subscription->plan->getSizeIn($subscription->plan->storage_limit, 'gb') : null;

        $data = [
            'used_storage' => (float)$usedStorage,
            'total_storage' =>(float)$total_storage,
        ];

        return $this->jsonData($data);
    }
}
