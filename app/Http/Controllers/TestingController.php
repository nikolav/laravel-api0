<?php

namespace App\Http\Controllers;

use App\Helpers\AppUtils;

// // use App\Mail\DemoMailMessage;
// use App\Mail\MessagePlainEmail;
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
    //   ->send(new MessagePlainEmail(
    //     view: 'emails.message-plain',
    //     subject: 'Hello from plain email.🍻',
    //     data: [
    //       'message' => 'Giant flower attempt mixture review grandmother opinion sad buy several slipped shaking consider log shelf what tune tobacco firm native shall throat as difficulty.'
    //     ]
    //   ));


    // $auth_firebase = app('firebase.auth');
    // $user = $auth_firebase->getUser('');

    return AppUtils::res([
      'status' => 'ok',
    ], null);
  }
}
