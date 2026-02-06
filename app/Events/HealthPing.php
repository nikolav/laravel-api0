<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

// or use anonymous events for quick event dispatch
// # Broadcast::on('foo.happened')->send();
// # Broadcast::on('orders.'.$id)->as('OrderPlaced')->with($order)->send();
// #https://laravel.com/docs/12.x/broadcasting#anonymous-events
class HealthPing implements ShouldBroadcastNow
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
