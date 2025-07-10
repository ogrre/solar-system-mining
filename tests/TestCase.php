<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\WithFaker;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use RefreshDatabase;
    use WithFaker;
    
    protected function setUp(): void
    {
        parent::setUp();
        
        // Disable CSRF verification for tests - try different approaches
        $this->withoutMiddleware([
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            'csrf',
        ]);
    }
}
