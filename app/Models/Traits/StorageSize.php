<?php

namespace App\Models\Traits;

trait StorageSize
{
    private $units = ['Bytes', 'Kb', 'Mb', 'Gb', 'Tb'];

    /**
     * Get the size in the most suitable unit from the given list.
     *
     * @param float $size Size in Kb
     * @return string
     */
    public function getSuitableSizeUnit(float $size)
    {
        return $this->cycleTroughUnits($size * 1024, 0);
    }

    /**
     * Format the size.
     *
     * @param $size
     * @return int|string
     */
    private function formatSize($size)
    {
        return number_format((float) $size, 2, '.', '') + 0;
    }

    /**
     * Cycle through units to find out the appropriate unit to represent te size.
     *
     * @param $size
     * @param $index
     * @return string
     */
    private function cycleTroughUnits($size, $index)
    {
        if ($size < 1024) {
            return $this->formatSize($size).' '.$this->units[$index];
        }

        $index++;
        if (isset($this->units[$index])) {
            return $this->cycleTroughUnits($size / 1024, $index);
        }

        return $this->formatSize($size).' '.$this->units[$index - 1];
    }
}
