<?php

namespace App\Http\Controllers;

use App\Models\Subscription;
use Auth;

class UserData extends Controller
{
    /**
     * GET /me/storage_info.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function storageInfo()
    {
        /**
         * @var Subscription $sub
         */
        $sub = Auth::user()->subscription;

        $usedStorage = $sub ? $sub->getSuitableSizeUnit($sub->used_storage) : null;
        $total_storage = $sub ? $sub->plan->getSuitableSizeUnit($sub->plan->storage_limit) : null;
        $percentage = $sub ? floor($sub->used_storage * 100 / $sub->plan->storage_limit) : 0;

        $data = [
            'used_storage' => $usedStorage,
            'total_storage' => $total_storage,
            'percentage' => $percentage,
        ];

        return $this->jsonData($data);
    }
}
