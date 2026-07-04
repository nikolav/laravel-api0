<?php

namespace App\Support;

use Throwable;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Process;

use Spatie\Browsershot\Browsershot;

use App\Helpers\AppUtils;

class PDF
{
  function template_render(string $template_name, ?array $data = [])
  {
    return view('pdf.' . $template_name . '.index', compact('data'))->render();
  }

  function template_inlined(string $template_name, ?array $data = [])
  {
    $temp_dir = storage_path(implode('/', ['app/temp/pdf', Str::uuid()]));
    if (!File::exists($temp_dir)) {
      File::makeDirectory($temp_dir, 0755, true);
    }

    $input_path  = $temp_dir . '/input.html';
    $output_path = $temp_dir . '/inlined.html';

    File::put(
      $input_path,
      $this->template_render($template_name, $data)
    );

    Process::timeout(122)
      ->run([
        'node',
        base_path(implode('/', [
          config('services.external.scripts_base_path'),
          config('services.external.scripts.html_inliner')
        ])),

        '--input',
        $input_path,

        '--output',
        $output_path,

        '--assets-relative-to',
        resource_path(implode('/', ['views/pdf', $template_name, 'index.blade.php'])),
      ])->throw();

    $html_inlined = File::get($output_path);

    File::deleteDirectory($temp_dir);

    return $html_inlined;
  }

  function build(
    string $template_name,
    array $data = [],
    float $width = 794,
    float $height = 1123,
    string $unit = 'px'

  ) {
    return Browsershot::html(
      $this->template_inlined($template_name, $data)
    )
      ->setChromePath(config('services.chromium.executable_path'))
      ->noSandbox()
      ->addChromiumArguments(config('services.chromium.pdf_render_arguments', []))
      ->newHeadless()
      ->scale(1.0)
      ->showBackground()
      // ->transparentBackground()
      // ->landscape()
      ->paperSize($width, $height, $unit);
  }

  function save(
    string $path,
    string $template_name,
    array $data = [],
    float $width = 794,
    float $height = 1123,
    string $unit = 'px'
  ) {
    $error = null;

    try {
      $this->build($template_name, $data, $width, $height, $unit)
        ->savePdf($path);
    } catch (Throwable $e) {
      $error = $e;
    }

    return AppUtils::res(null, $error);
  }

  function save_demo(string $pdf_storage_path = 'app/out/demo.pdf', array $data = [])
  {
    return $this->save(storage_path($pdf_storage_path), 'demo-a4', $data);
  }
}
