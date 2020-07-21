/**
 * @file
 * Serviceworker file for browser push notification.
 */

self.addEventListener('install', function(event) {
  event.waitUntil(self.skipWaiting());
});

self.addEventListener('activate', function(event) {
  event.waitUntil(self.clients.claim());
});

self.addEventListener('push', function (event) {

  if (!(self.Notification && self.Notification.permission === 'granted')) {
    return;
  }

  var data = {
    body: '',
    message: '',
    icon: ''
  };

  if (event.data) {
    data = event.data.json();
    event.waitUntil(self.registration.showNotification(data.title, {
      body: data.body,
      icon: data.icon
    }));
  }
});

self.addEventListener('notificationclick', function (event) {
  event.waitUntil(
    self.clients.matchAll().then(function (clientList) {
      if (clientList.length > 0) {
        return clientList[0].focus();
      }
      if (event.data) {
        var data = event.data.json();
        return self.clients.openWindow(data.url);
      }
    })
  );
});
