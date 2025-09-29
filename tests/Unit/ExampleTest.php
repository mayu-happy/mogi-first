<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Tests\T\Feature;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

    public function test_example()
    {
        $this->get('/test-home')->assertStatus(200);
    }
}
