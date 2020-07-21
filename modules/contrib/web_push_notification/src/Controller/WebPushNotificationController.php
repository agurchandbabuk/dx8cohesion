<?php

namespace Drupal\web_push_notification\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\web_push_notification\Entity\Subscription;
use Drupal\web_push_notification\KeysHelper;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;

/**
 * Provides a push notification responses.
 */
class WebPushNotificationController extends ControllerBase {

  /**
   * @var \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * @var \Drupal\web_push_notification\KeysHelper
   */
  protected $keysHelper;

  /**
   * WebPushNotificationController constructor.
   *
   * @param \Drupal\Core\Extension\ModuleHandler $moduleHandler
   *   The module handler service.
   * @param \Drupal\web_push_notification\KeysHelper $keysHelper
   *   The notification keys helper service.
   */
  public function __construct(ModuleHandler $moduleHandler, KeysHelper $keysHelper) {
    $this->moduleHandler = $moduleHandler;
    $this->keysHelper = $keysHelper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('module_handler'),
      $container->get('web_push_notification.keys_helper')
    );
  }

  /**
   * Gets the service worker javascript handler.
   *
   * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
   *  The service worker content.
   */
  public function serviceWorker() {
    $module_path = $this->moduleHandler->getModule('web_push_notification')->getPath();
    $uri = "{$module_path}/js/service_worker.js";

    if (!file_exists($uri)) {
      throw new NotFoundHttpException();
    }

    return BinaryFileResponse::create($uri, 200, [
      'Content-Type' => 'text/javascript',
      'Content-Length' => filesize($uri),
    ]);
  }

  /**
   * Accepts a user confirmation for notifications subscribe.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   The request.
   *
   * @return \Symfony\Component\HttpFoundation\JsonResponse
   *
   * @throws \Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException
   *   When public and private keys are empty.
   * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
   *   When required parameter (key, token, endpoint) is missing.
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  public function subscribe(Request $request) {

    // Cannot accept a user confirmation when push keys are empty.
    if (!$this->keysHelper->isKeysDefined()) {
      throw new ServiceUnavailableHttpException();
    }

    $key = $request->get('key');
    $token = $request->get('token');
    $endpoint = $request->get('endpoint');

    if (!empty($key) && !empty($token) && !empty($endpoint)) {
      $ids = \Drupal::entityQuery('wpn_subscription')
        ->condition('key', $key)
        ->condition('token', $token)
        ->execute();
      if (empty($ids)) {
        $subscription = Subscription::create([
          'key' => $key,
          'token' => $token,
          'endpoint' => $endpoint,
        ]);
        $subscription->save();
      }
    }
    else {
      throw new BadRequestHttpException();
    }

    return new JsonResponse(['status' => true]);
  }

}
