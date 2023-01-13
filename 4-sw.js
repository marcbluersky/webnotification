
self.addEventListener("install", evt => self.skipWaiting()); // (A) INSTANT WORKER ACTIVATION
self.addEventListener("activate", evt => self.clients.claim()); // (B) CLAIM CONTROL INSTANTLY

var static_data;

self.addEventListener('notificationclick', function(event) {
  console.log('On notification clic: ', event.notification.tag);
  console.log('static_data', static_data);
  event.notification.close();

  event.waitUntil(clients.matchAll({
    type: "window"
  }).then(function(clientList) {
	  
    for (var i = 0; i < clientList.length; i++) {
      var client = clientList[i];
      if (client.url == static_data.onclic_url && 'focus' in client)
        return client.focus();
    }
    if (clients.openWindow) return clients.openWindow(static_data.onclic_url);
  }));
});

 
// (C) LISTEN TO PUSH
self.addEventListener("push", evt => {	
  console.log("evt",evt);
  
  const data = evt.data.json();
  static_data = evt.data.json();
  
  console.log('static_data', static_data);
  console.log("stringify",JSON.stringify(evt.data.json()));
  console.log("Push", data);
  
  self.registration.showNotification(data.title, {
    body: data.body,
    icon: data.icon,
    image: data.image,
	badge: data.badge
  });
  
});