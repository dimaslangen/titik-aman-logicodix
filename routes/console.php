<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use App\Models\Report;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('reports:expire', function () {
    $count = Report::whereIn('status', ['UNVERIFIED', 'VERIFIED', 'PENDING_MODERATION'])
        ->whereNotNull('expires_at')
        ->where('expires_at', '<=', now())
        ->update(['status' => 'EXPIRED']);

    $this->info("Expired reports updated: {$count}");
})->purpose('Mark expired reports as EXPIRED');
