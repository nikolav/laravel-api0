<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

use App\Helpers\AppUtils;
use App\Models\Docs;

class DemoJob implements ShouldQueue
{
  use Queueable;

  /**
   * Create a new job instance.
   */
  function __construct()
  {
    //
  }

  /**
   * Execute the job.
   */
  function handle(): void
  {
    //
    Docs::create([
      'key'  => AppUtils::nanoid(),
      'data' => AppUtils::encodeJson(['x' => AppUtils::nanoid()]),
    ]);
  }
}
