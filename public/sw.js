self.addEventListener("push", function(event) {
	if (!event || !event.data) return;
	
	const data = event.data.json();
	event.waitUntil(self.registration.showNotification(data.title, {
		body: data.body,
		icon: data.icon || null,
		tag: data.tag,
		data: data.data
	}));
});

self.addEventListener("notificationclick", function(event) {
	if (!event || !event.notification || !event.notification.data) return;
	
	if (event.notification) {
		event.notification.close();
	}
	
	if (!clients.openWindow) return;
	event.waitUntil(clients.matchAll({
		type: "window"
	}).then(function(clientList) {
		if (clientList.length > 0) {
			let focus_client = null;
			for (var i = 0; i < clientList.length; i++) {
				var client = clientList[i];
				if (client.focused === true) {
					focus_client = null;
					break;
				}
				if ('focus' in client) {
					focus_client = client;
				}
			}
			if (focus_client !== null) {
				focus_client.focus();
			}
		}
		clients.openWindow(event.notification.data);
	}));
});