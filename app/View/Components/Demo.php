<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Demo extends Component
{
  /**
   * Create a new component instance.
   */
  function __construct(
    public string $content,

  ) {}

  /**
   * Get the view / contents that represent the component.
   */
  function render(): View|Closure|string
  {
    return view('components.demo');
  }
}
