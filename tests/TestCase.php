<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
