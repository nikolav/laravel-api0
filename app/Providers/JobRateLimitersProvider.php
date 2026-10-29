<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;


class JobRateLimitersProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  function boot(): void
  {
    // RateLimiter::for('backups', function (object $job) {
    //   return $job->user->vipCustomer()
    //     ? Limit::none()
    //     : Limit::perHour(1)->by($job->user->id);
    // });
  }
}
