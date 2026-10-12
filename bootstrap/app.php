<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Send unauthenticated visitors to the right login screen.
        $middleware->redirectGuestsTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            return route('customer.login');
        });

        // Send already-authenticated visitors away from guest-only pages.
        $middleware->redirectUsersTo(function ($request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.dashboard');
            }

            return route('home');
        });

        // GitHub's webhook POST has no session/CSRF token — it's verified
        // by HMAC signature inside DeployWebhookController instead.
        $middleware->validateCsrfTokens(except: ['deploy-webhook']);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
