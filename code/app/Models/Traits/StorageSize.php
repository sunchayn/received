<?php

namespace App\Models\Traits;

use App\Models\File;
use Illuminate\Support\Collection;

trait StorageSize
{
    public function getSizeIn(int $size, $unit = 'kb')
    {
        switch ($unit) {
            case 'mb':
                $size = $size / 1024;
                break;
            case 'gb':
                $size = $size / 1024 / 1024;
                break;
        }

        return number_format((float)$size, 2, '.', '');
    }
}
