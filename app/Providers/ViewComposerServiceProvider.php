<?php

namespace App\Providers;

// use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ViewComposerServiceProvider extends ServiceProvider
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
    // View::composer('partials.sidebar', function ($view) {
    //   $view->with('data', collect(['data'])->toArray());
    // });
    // View::share('foo.global', 'bar');
  }
}
