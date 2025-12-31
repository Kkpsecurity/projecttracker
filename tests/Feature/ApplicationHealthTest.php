<?php

namespace Tests\Feature;

use Tests\TestCase;

class ApplicationHealthTest extends TestCase
{
    public function test_application_boots_and_homepage_responds(): void
    {
        $response = $this->get('/');

        $this->assertTrue(
            in_array($response->getStatusCode(), [200, 301, 302], true),
            'Expected / to return 200/301/302, got ' . $response->getStatusCode()
        );
    }
}
