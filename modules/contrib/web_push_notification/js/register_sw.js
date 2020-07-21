/**
 * @file
 * Register service worker.
 */

(function ($, Drupal) {

  function urlBase64ToUint8Array(base64String) {
    var padding = '='.repeat((4 - base64String.length % 4) % 4);
    var base64 = (base64String + padding)
        .replace(/\-/g, '+')
        .replace(/_/g, '/');

    var rawData = window.atob(base64);
    var outputArray = new Uint8Array(rawData.length);

    for (var i = 0; i < rawData.length; ++i) {
      outputArray[i] = rawData.charCodeAt(i);
    }
    return outputArray;
  }

  Drupal.behaviors.webPushNotification = {

    attach: function (context, settings) {

      $('body').once('web_push_notification').each(function () {
        if (!('serviceWorker' in navigator)) {
          return;
        }
        navigator
          .serviceWorker
          .register(settings.webPushNotification.serviceWorkerUrl)
          .then(function (registration) {

            return registration.pushManager.getSubscription()
                .then(function (subscription) {

                  if (subscription) {
                    return subscription;
                  }

                  var publicKey = settings.webPushNotification.publicKey;
                  var vapidKey = urlBase64ToUint8Array(publicKey);

                  return registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: vapidKey
                  })

                });
          })
          .then(function (subscription) {

            var key = subscription.getKey('p256dh');
            var token = subscription.getKey('auth');

            $.post(settings.webPushNotification.subscribeUrl, {
              key: key ? btoa(String.fromCharCode.apply(null, new Uint8Array(key))) : null,
              token: token ? btoa(String.fromCharCode.apply(null, new Uint8Array(token))) : null,
              endpoint: subscription.endpoint
            });

          });
      });

    } // attach

  }

})(jQuery, Drupal);
