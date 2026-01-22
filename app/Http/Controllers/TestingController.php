<?php

namespace App\Http\Controllers;

// use App\Mail\DemoMailMessage;
// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Mail;

class TestingController extends Controller
{
  //
  function demo()
  {
    //
    // $res =
    //   Mail::to('admin@nikolav.rs')
    //   ->send(new DemoMailMessage());

    return response()->json('testing:ok');
  }
}
