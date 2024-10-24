<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('email:send')->name('Send_Email')
                                ->withoutOverlapping(25)
                                ->timezone('Asia/Jakarta')
                                ->between('6:00', '23:00')
                                ->after(function () {
                                    Artisan::command('cache:clear');
                                });
