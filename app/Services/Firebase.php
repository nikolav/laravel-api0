<?php

namespace App\Services;

class Firebase
{
  public function auth()
  {
    return app('firebase.auth');
  }

  public function firestore()
  {
    return app('firebase.firestore')->database();
  }

  public function messaging()
  {
    return app('firebase.messaging');
  }
}
