<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

// use App\Facades\StoreMain;

class DemoCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:demo {foo=bar} {--baz}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Demo command.';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    //
  }
}
