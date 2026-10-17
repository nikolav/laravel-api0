<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class FormRequestBase extends FormRequest
{
  /**
   * Determine if the user is authorized to make this request.
   */
  function authorize(): bool
  {
    return false;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array<string, ValidationRule|array<mixed>|string>
   */
  function rules(): array
  {
    return [
      //
    ];
  }
}
