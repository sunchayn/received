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

        $usedStorage = $subscription ? $subscription->getSuitableSizeUnit($subscription->used_storage) : null;
        $total_storage = $subscription ? $subscription->plan->getSuitableSizeUnit($subscription->plan->storage_limit) : null;
        $percentage = $subscription ? floor($subscription->used_storage * 100 / $subscription->plan->storage_limit): 0;

        $data = [
            'used_storage' => $usedStorage,
            'total_storage' => $total_storage,
            'percentage' => $percentage,
        ];

        return $this->jsonData($data);
    }
}
