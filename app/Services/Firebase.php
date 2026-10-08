<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

use Kreait\Firebase\Factory;
// use Kreait\Laravel\Firebase\Facades\Firebase as F;

class Firebase
{

  protected Factory $factory;

  function __construct()
  {
    try {
      $this->factory = (new Factory)
        ->withServiceAccount(base_path(config('services.firebase.credentials')))
        ->withFirestoreClientConfig([
          // 'transport' => 'rest',
          'credentials' => base_path(config('services.firebase.credentials')),
        ]);
    } catch (\Throwable $e) {
      Log::error('Firebase initialization failed: ' . $e->getMessage());
      throw $e;
    }
  }

  function auth()
  {
    return $this->factory->createAuth();
  }

  function firestore()
  {
    return $this->factory->createFirestore();
  }

  function db()
  {
    return $this->firestore()->database();
  }

  function messaging()
  {
    return $this->factory->createMessaging();
  }
}
