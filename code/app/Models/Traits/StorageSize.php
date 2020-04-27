<?php

namespace App\Models\Traits;

use App\Models\File;
use Illuminate\Support\Collection;

trait StorageSize
{
    private $units = ['Bytes', 'Kb', 'Mb', 'Gb', 'Tb'];

    /**
     * Get size in the given unit
     *
     * @param int $size Size in Kb
     * @param string $unit
     * @return string
     */
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

        return number_format((float)$size, 3, '.', '');
    }

    /**
     * Get the size in the most suitable unit from the given list.
     *
     * @param int $size Size in Kb
     * @return string
     */
    public function getSuitableSizeUnit(int $size) {
        return $this->cycleTroughUnits($size * 1024, 0);
    }

    /**
     * Format the size
     *
     * @param $size
     * @return int|string
     */
    private function formatSize($size) {
        return number_format((float)$size, 2, '.', '') + 0;
    }

    /**
     * Cycle through units to find out the appropriate unit to represent te size.
     *
     * @param $size
     * @param $index
     * @return string
     */
    private function cycleTroughUnits($size, $index) {
        if ($size < 1024) {
            return $this->formatSize($size) . ' ' . $this->units[$index];
        }

        $index++;
        if (isset($this->units[$index])) {
            return $this->cycleTroughUnits($size / 1024, $index);
        }

        return $this->formatSize($size)  . ' ' . $this->units[$index - 1];
    }
}
