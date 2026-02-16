<?php

namespace App\Services;

use Kreait\Firebase\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;

final class CloudMessagingService
{
  private Messaging $messaging;

  function __construct(
    private Firebase $firebase,
  ) {
    $this->messaging = $this->firebase->messaging();
  }

  /**
   * Send notification to a single device token
   */
  function sendToToken(
    string $deviceToken,
    string $title,
    string $body,
    array $data = []
  ) {
    $message = CloudMessage::new()
      ->toToken($deviceToken)
      ->withNotification(Notification::create($title, $body));

    if (!empty($data)) {
      $message = $message->withData($data);
    }

    return $this->messaging->send($message);
  }

  /**
   * Send silent data-only message
   */
  function sendDataToToken(
    string $deviceToken,
    array $data
  ) {
    $message = CloudMessage::new()
      ->toToken($deviceToken)
      ->withData($data);

    return $this->messaging->send($message);
  }

  /**
   * Send notification to a topic (broadcast)
   */
  function sendToTopic(
    string $topic,
    string $title,
    string $body,
    array $data = []
  ) {
    $message = CloudMessage::new()
      ->toTopic($topic)
      ->withNotification(Notification::create($title, $body));

    if (!empty($data)) {
      $message = $message->withData($data);
    }

    return $this->messaging->send($message);
  }

  /**
   * Send message to a condition (advanced targeting)
   */
  function sendToCondition(
    string $condition,
    string $title,
    string $body,
    array $data = []
  ) {
    $message = CloudMessage::new()
      ->toCondition($condition)
      ->withNotification(Notification::create($title, $body));

    if (!empty($data)) {
      $message = $message->withData($data);
    }

    return $this->messaging->send($message);
  }

  /**
   * Subscribe device token to a topic
   */
  function subscribeToTopic(string $topic, string $deviceToken): void
  {
    $this->messaging->subscribeToTopic($topic, $deviceToken);
  }

  /**
   * Unsubscribe device token from a topic
   */
  function unsubscribeFromTopic(string $topic, string $deviceToken): void
  {
    $this->messaging->unsubscribeFromTopic($topic, $deviceToken);
  }
}
