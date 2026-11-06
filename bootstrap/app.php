<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Database\UniqueConstraintViolationException;
return Application::configure(basePath:dirname(__DIR__))
    ->withRouting(web:__DIR__.'/../routes/web.php',api:__DIR__.'/../routes/api.php',commands:__DIR__.'/../routes/console.php',health:'/up')
    ->withMiddleware(function(Middleware $middleware){
        $middleware->redirectGuestsTo('/admin/login');
    })
    ->withExceptions(function(Exceptions $exceptions){
        $exceptions->shouldRenderJsonWhen(fn($r)=>$r->is('api/*')||$r->expectsJson());
        $exceptions->render(function(UniqueConstraintViolationException $e,$r){
            if($r->is('api/*')) return response()->json(['message'=>'Conflicting identifier.'],409);
        });
    })->create();
