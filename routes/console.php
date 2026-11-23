<?php

use App\Http\Controllers\DashboardController;
use App\Services\FacebookPostExtractor;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('contents:sync-facebook', function (DashboardController $controller, FacebookPostExtractor $extractor) {
    $updated = $controller->performBulkFacebookSync($extractor);
    $this->info("Auto-Sync selesai. {$updated} konten diperbarui dari Facebook.");
})->purpose('Sync likes, comments, and shares from saved Facebook post links');

Schedule::command('contents:sync-facebook')->everyTwoMinutes();
