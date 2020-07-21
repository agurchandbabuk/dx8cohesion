<?php

use Drupal\web_push_notification\NotificationItem;

/**
 * @file
 * Hooks and API provided by the Web Push Notification module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Alter a web push notification item.
 *
 * This hook is invoked when the notification item is scheduled to put to
 * the sending queue.
 *
 * @param \Drupal\web_push_notification\NotificationItem $item
 *   The notification item prepared for adding to the sending queue. The body
 *   of the item is trimmed.
 * @param string $full_body
 *   The source message body.
 *
 */
function hook_web_push_notification_item_alter(NotificationItem $item, $full_body) {

}

/**
 * @} End of "addtogroup hooks".
 */
