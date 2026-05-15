<?php

use App\Mail\PasswordResetCodeMail;
use App\Mail\TestMail;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\Mail;

require __DIR__.'/../vendor/autoload.php';

$app = require __DIR__.'/../bootstrap/app.php';
$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$mode = $argv[1] ?? 'config';

if ($mode === 'config') {
    echo json_encode([
        'mailer' => config('mail.default'),
        'transport' => config('mail.mailers.smtp.transport'),
        'host' => config('mail.mailers.smtp.host'),
        'port' => config('mail.mailers.smtp.port'),
        'scheme' => config('mail.mailers.smtp.scheme'),
        'auto_tls' => config('mail.mailers.smtp.auto_tls'),
        'encryption_env' => env('MAIL_ENCRYPTION'),
        'from' => config('mail.from.address'),
        'queue' => config('queue.default'),
    ], JSON_PRETTY_PRINT).PHP_EOL;
    exit(0);
}

if ($mode === 'send') {
    try {
        Mail::to('bna463737@gmail.com')->send(new TestMail);
        echo "MAIL_SENT\n";
    } catch (Throwable $exception) {
        echo 'MAIL_ERROR: '.$exception->getMessage().PHP_EOL;
    }
    exit(0);
}

if ($mode === 'send-reset') {
    try {
        Mail::to('bna463737@gmail.com')->send(new PasswordResetCodeMail('123456'));
        echo "RESET_MAIL_SENT\n";
    } catch (Throwable $exception) {
        echo 'RESET_MAIL_ERROR: '.$exception->getMessage().PHP_EOL;
    }
    exit(0);
}

echo "Unknown mode\n";
