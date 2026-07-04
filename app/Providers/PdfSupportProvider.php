<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Support\PDF as PdfSupport;

class PdfSupportProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    //
    $this->app->singleton(PdfSupport::class, fn() => new PdfSupport());
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    // build schema @boot
    $this->app->make(PdfSupport::class);
  }
}
