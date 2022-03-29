<?php

namespace Tests\Unit;

use App\Models\Traits\StorageSize;
use PHPUnit\Framework\TestCase;

class StorageSizeTest extends TestCase
{
    /**
     * @dataProvider size_formatting_data_provider
     * @test
     *
     * @param $size
     * @param $expectedOutput
     */
    public function it_properly_format_size($size, $expectedOutput)
    {
        $trait = new class
        {
            use StorageSize;
        };
        $this->assertEquals($expectedOutput, $trait->getSuitableSizeUnit($size));
    }

    // Data providers

    public function size_formatting_data_provider()
    {
        return [
            [0, '0 Bytes'],
            [0.5, '512 Bytes'],
            [1, '1 Kb'],
            [1024 * 5, '5 Mb'],
            [1024 * 1024 * 5, '5 Gb'],
            [1024 * 1024 * 1024 * 5, '5 Tb'],

            // Overflowing unit
            [1024 * 1024 * 1024 * 1024 * 5, '5120 Tb'],
        ];
    }
}
