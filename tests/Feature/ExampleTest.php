<?php

use Illuminate\Foundation\Testing\DatabaseMigrations;

class ExampleTest extends TestCase
{
    use DatabaseMigrations;

    public function test_example()
    {
        $this->get('/test-home')->assertStatus(200);
    }
}
