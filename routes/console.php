<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('ops:start-realtime', function () {
    if (DIRECTORY_SEPARATOR === '\\') {
        $this->warn('ops:start-realtime is intended for Linux servers. Run services manually on Windows.');

        return;
    }

    $basePath = escapeshellarg(base_path());
    $phpBinary = escapeshellarg(PHP_BINARY);
    $reverbLog = escapeshellarg(storage_path('logs/reverb.log'));
    $queueLog = escapeshellarg(storage_path('logs/queue-worker.log'));

    $reverbCommand = "cd {$basePath} && nohup {$phpBinary} artisan reverb:start --host=0.0.0.0 --port=8080 >> {$reverbLog} 2>&1 &";
    $queueCommand = "cd {$basePath} && nohup {$phpBinary} artisan queue:work --tries=3 >> {$queueLog} 2>&1 &";

    exec($reverbCommand);
    exec($queueCommand);

    $this->info('Reverb and queue worker start commands were dispatched.');
})->purpose('Start Reverb and queue worker in the background');

Artisan::command('ops:start-reverb', function () {
    if (DIRECTORY_SEPARATOR === '\\') {
        $this->warn('ops:start-reverb is intended for Linux servers. Run reverb manually on Windows.');

        return;
    }

    $basePath = escapeshellarg(base_path());
    $phpBinary = escapeshellarg(PHP_BINARY);
    $reverbLog = escapeshellarg(storage_path('logs/reverb.log'));

    $reverbCommand = "cd {$basePath} && nohup {$phpBinary} artisan reverb:start --host=0.0.0.0 --port=8080 >> {$reverbLog} 2>&1 &";

    exec($reverbCommand);

    $this->info('Reverb start command was dispatched.');
})->purpose('Start Reverb in the background');
