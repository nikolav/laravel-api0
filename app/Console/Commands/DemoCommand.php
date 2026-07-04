<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DemoCommand extends Command
{
  /**
   * The name and signature of the console command.
   *
   * @var string
   */
  protected $signature = 'app:cmd-demo {name=user}';

  /**
   * The console command description.
   *
   * @var string
   */
  protected $description = 'Demo command; prints demo message.';

  /**
   * Execute the console command.
   */
  public function handle()
  {
    //
    echo 'command demo; Hello, ' . $this->argument('name') . '.';
  }
}
