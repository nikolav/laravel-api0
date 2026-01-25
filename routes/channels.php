<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
  return (int) $user->id === (int) $id;
});

// Broadcast::channel('threads.{threadId}', function ($user, $threadId) {
//   // authorize user access to that thread
//   return Thread::whereKey($threadId)
//     ->whereHas('members', fn($q) => $q->whereKey($user->id))
//     ->exists();
// });
