<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
// use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class HealthPing implements ShouldBroadcast
{
  use Dispatchable, SerializesModels;

  public string $broadcastQueue = 'broadcasts';

  // events channel
  // Channel:          public, no auth
  // PrivateChannel:   per-user/per-room security, auth required
  // PresenceChannel:  like private, +whos subscribed
  public function broadcastOn(): array
  {
    return [new Channel('health')];
  }

  // event name
  public function broadcastAs(): string
  {
    return 'health.ping';
  }

  // payload
  // by default laravel serializes public event props
  // define .broadcastWith to send optimal package
  public function broadcastWith(): array
  {
    return ['status' => 'ok'];
  }
}
