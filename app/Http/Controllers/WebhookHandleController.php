<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WebhookHandleController extends Controller
{
    function webhook(Request $req, ?string $key = null)
    {
        // accept 3rd party webhooks
        return response()->json([], 200);
    }
}
