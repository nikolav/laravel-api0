<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Graphql\GraphQLHandle;

final class GraphQLServiceProvider extends ServiceProvider
{
  /**
   * Register services.
   */
  public function register(): void
  {
    $this->app->singleton(GraphQLHandle::class, function () {
      return new GraphQLHandle();
    });
  }

  /**
   * Bootstrap services.
   */
  public function boot(): void
  {
    // build schema @boot
    $this->app->make(GraphQLHandle::class);
  }
}
