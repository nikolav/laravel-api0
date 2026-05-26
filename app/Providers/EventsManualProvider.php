<?php

namespace App\Providers;

use App\Events\EventDemo;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;

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
    Event::listen(function (EventDemo $event) {
      print("@event --demo\n");
    });
  }
}
