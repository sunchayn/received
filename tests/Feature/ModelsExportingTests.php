<?php

namespace Tests\Feature;

use App\Models\File;
use App\Models\Plan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelsExportingTests extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function it_properly_export_plans()
    {
        $plan = factory(Plan::class)->create();

        $data = $plan->toArray();
        $this->assertArrayHasKey('title', $data);

        $this->assertEquals($plan->created_at->diffForHumans(), $data['created_at']);
        $this->assertEquals($plan->getSuitableSizeUnit($plan->storage_limit), $data['storage_limit']);
    }

    /**
     * @test
     */
    public function it_properly_export_files()
    {
        $file = factory(File::class)->state('with_folder')->create();

        $data = $file->toArray();

        $this->assertArrayHasKey('filename', $data);
        $this->assertArrayHasKey('extension', $data);
        $this->assertArrayHasKey('type', $data);

        $this->assertEquals($file->created_at->diffForHumans(), $data['sent_on']);
        $this->assertEquals($file->getSuitableSizeUnit($file->size), $data['size']);
    }
}
