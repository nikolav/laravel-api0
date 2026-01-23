<?php

namespace App\Http\Controllers;

// use App\Mail\DemoMailMessage;
// // use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Mail;

class TestingController extends Controller
{
  //
  function demo()
  {
    //
    // $res =
    //   Mail::to(['admin@nikolav.rs'])
    //   ->send(new DemoMailMessage(
    //     subject: 'Hello from laravel mail!',
    //     data: [
    //       'message' => 'nothing wore port palace wrote hole blood information bill thy how until storm fence throw pack finest available else sweet disease journey plate industry'
    //     ]
    //   ));

    return response()->json('testing:ok');
  }
}
