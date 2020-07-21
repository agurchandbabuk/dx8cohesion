<?php

namespace Drupal\web_push_notification\Plugin\QueueWorker;

use Drupal\Core\Annotation\QueueWorker;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\Queue\QueueWorkerBase;

/**
 * @QueueWorker(
 *   id = "web_push_queue",
 *   title = @Translation("Web Push notification sender"),
 * )
 */
class WebPushQueueWorker extends QueueWorkerBase {

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    /** @var \Drupal\web_push_notification\WebPushSender $sender */
    $sender = \Drupal::service('web_push_notification.sender');
    $sender->send($data);
  }

}