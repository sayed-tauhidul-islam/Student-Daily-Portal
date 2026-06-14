<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $compiledViews = sys_get_temp_dir().DIRECTORY_SEPARATOR.'student-daily-portal-views'.DIRECTORY_SEPARATOR.str_replace('\\', '_', static::class).'_'.$this->name().'_'.bin2hex(random_bytes(4));
        File::ensureDirectoryExists($compiledViews);
        config(['view.compiled' => $compiledViews]);

        $this->dropMongoDatabase();
    }

    protected function tearDown(): void
    {
        $this->dropMongoDatabase();

        parent::tearDown();
    }

    protected function dropMongoDatabase(): void
    {
        try {
            DB::connection('mongodb')->getDatabase()->drop();
        } catch (\Throwable) {
            // Ignore cleanup errors so failures still surface from the test itself.
        }
    }
}
