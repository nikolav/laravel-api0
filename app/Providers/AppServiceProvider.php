<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

// use App\Models\User;
use App\Support\Nanoid;

class AppServiceProvider extends ServiceProvider
{
  /**
   * Register any application services.
   */
  public function register(): void
  {
    $this->app->singleton(Nanoid::class, fn() => new Nanoid());
  }

  /**
   * Bootstrap any application services.
   */
  public function boot(): void
  {
    //
    // Gate::define('use-dashboard', fn(User $user) => $user->isAdmin);
    // # Check with: Gate::allows('use-dashboard')
  }
}
