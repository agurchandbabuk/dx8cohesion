<?php

namespace Drupal\web_push_notification;

/**
 * Invalid TTL value.
 */
class InvalidTTLException extends \Exception {

  /**
   * InvalidTTLException constructor.
   *
   * @param string $value
   *   The invalid value.
   */
  public function __construct($value) {
    parent::__construct(sprintf('Invalid TTL value: %s', $value));
  }

}