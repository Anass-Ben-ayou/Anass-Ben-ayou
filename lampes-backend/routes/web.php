<?php

use App\Mail\TestMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

if (app()->environment('local')) {
    Route::get('/mail-debug', function () {
        return response()->json([
            'default_mailer' => config('mail.default'),
            'transport' => config('mail.mailers.smtp.transport'),
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'encryption' => env('MAIL_ENCRYPTION'),
            'scheme' => config('mail.mailers.smtp.scheme'),
            'auto_tls' => config('mail.mailers.smtp.auto_tls'),
            'from_address' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
            'queue_connection' => config('queue.default'),
        ]);
    });

    Route::get('/test-mail', function () {
        try {
            Mail::to('bna463737@gmail.com')->send(new TestMail);

            Log::info('Test mail sent successfully.', [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from_address' => config('mail.from.address'),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test email sent successfully.',
            ]);
        } catch (Throwable $exception) {
            Log::error('Test mail failed.', [
                'mailer' => config('mail.default'),
                'host' => config('mail.mailers.smtp.host'),
                'port' => config('mail.mailers.smtp.port'),
                'from_address' => config('mail.from.address'),
                'error' => $exception->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $exception->getMessage(),
            ], 500);
        }
    });
}
