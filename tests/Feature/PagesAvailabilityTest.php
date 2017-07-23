<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;

class PagesAvailabilityTest extends TestCase
{

    public function testMainPageAvailable()
    {
        $this->get('/')->assertStatus(200);
    }

    public function testRehabilitationPageAvailable()
    {
        $this->get('/warranty')->assertStatus(200);
    }




    protected function disableExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, new class extends Handler {
            public function __construct() {}

            public function report(\Exception $e)
            {
                // no-op
            }

            public function render($request, \Exception $e) {
                throw $e;
            }
        });
    }
}
