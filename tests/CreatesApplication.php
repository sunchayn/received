<?php

namespace Tests;

use Illuminate\Contracts\Console\Kernel;

trait CreatesApplication
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        $app = require __DIR__.'/../bootstrap/app.php';

        $app->make(Kernel::class)->bootstrap();

        $middleware = \App\Http\Middleware\VerifyCsrfToken::class;

        $app->instance($middleware, new class
        {
            public function handle($request, $next)
            {
                return $next($request);
            }
        });

        return $app;
    }
}
