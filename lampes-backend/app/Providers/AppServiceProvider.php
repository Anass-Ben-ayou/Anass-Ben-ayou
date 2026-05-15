<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->input('email');

            return Limit::perMinute(5)->by($request->ip().'|'.mb_strtolower($email));
        });

        RateLimiter::for('register', function (Request $request) {
            return Limit::perMinute(3)->by($request->ip());
        });

        RateLimiter::for('forgot-password', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email'));

            return [
                Limit::perHour(3)->by('forgot:'.$email),
                Limit::perMinute(5)->by('forgot-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('verify-reset-code', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email'));

            return Limit::perMinute(10)->by('verify-reset:'.$request->ip().'|'.$email);
        });

        RateLimiter::for('reset-password', function (Request $request) {
            $email = mb_strtolower((string) $request->input('email'));

            return Limit::perMinute(5)->by('reset-password:'.$request->ip().'|'.$email);
        });

        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(4)->by('contact:'.$request->ip());
        });

        RateLimiter::for('reviews', function (Request $request) {
            $email = mb_strtolower((string) ($request->input('email') ?: optional($request->user())->email));

            return Limit::perMinute(5)->by('reviews:'.$request->ip().'|'.$email);
        });

        RateLimiter::for('payment', function (Request $request) {
            return Limit::perMinute(10)->by('payment:'.$request->ip().'|'.optional($request->user())->id_client);
        });
    }
}
