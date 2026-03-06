<?php

namespace App\Support;

use Throwable;

use Spatie\Browsershot\Browsershot;

use App\Helpers\AppUtils;

class PDF
{
  /**
   * Create a new class instance.
   */
  public function __construct()
  {
    //
  }

  function render_demo(string $pdf_base_path = 'pdf-demo.pdf')
  {
    $error = null;

    try {
      $html = view('pdf.pdf-demo', ['css' => AppUtils::vite_resource()])->render();
      Browsershot::html($html)
        ->setChromePath(config('services.chromium.executable_path'))
        ->noSandbox()
        ->addChromiumArguments(config('services.chromium.pdf_render_arguments', []))
        ->format('A4')
        // ->paperSize(550, 400, 'px')
        ->showBackground()
        ->scale(1.0)
        ->savePdf(base_path($pdf_base_path));
    } catch (Throwable $e) {
      $error = $e;
    }

    return AppUtils::res(null, $error);
  }
}
