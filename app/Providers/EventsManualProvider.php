<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventsManualProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    //
    // Event::listen(..)
  }
}
