<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class ResponseMacrosServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */

    public function boot()
    {
        Response::macro('notFound', function ($message = 'Not Found') {
            return response()->json(['message' => $message], 404);
        });

        Response::macro('forbidden', function ($message = 'Forbidden') {
            return response()->json(['message' => $message], 403);
        });
    }

}
