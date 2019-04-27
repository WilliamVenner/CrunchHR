/* global $ CrunchHR Notification */

CrunchHR.Notifications.GetServiceWorker(function(worked, ServiceWorker) {
	if (!worked) return;
	
	let btn = $("#subscription-toggle");
	
	let isSubscribed = false;
	
	if (Notification.permission === "granted") {
		ServiceWorker.pushManager.getSubscription()
		.then(function(subscription) {
			$("#subscription-toggle").removeClass("disabled");
			isSubscribed = subscription !== null;
			if (isSubscribed) {
				btn.find(".text").text("Unsubscribe");
				btn.find(".icon i").attr("class", "fas fa-bell-slash");
				btn.removeClass("green").addClass("red");
			} else {
				btn.find(".text").text("Subscribe");
				btn.find(".icon i").attr("class", "fas fa-bell");
				btn.removeClass("red").addClass("green");
			}
		});
	} else {
		$("#subscription-toggle").removeClass("disabled");
	}

	$("#subscription-toggle").click(function() {
		if (Notification.permission !== "granted") {
			Notification.requestPermission().then(CrunchHR.Notifications.Subscribe).catch(function() {
				alert("Please grant permission to subscribe to push notifications.");
			});
		} else {
			if (isSubscribed) {
				if (confirm("Are you sure you want to unsubscribe from push notifications on this device?")) {
					CrunchHR.Notifications.Unsubscribe(function(success) {
						if (!success) return;
						
						isSubscribed = false;
							
						btn.find(".text").text("Subscribe");
						btn.find(".icon i").attr("class", "fas fa-bell");
						btn.removeClass("red").addClass("green");
					});
				}
			} else {
				CrunchHR.Notifications.Subscribe(function(success) {
					if (!success) return;
					
					isSubscribed = true;
					
					btn.find(".text").text("Unsubscribe");
					btn.find(".icon i").attr("class", "fas fa-bell-slash");
					btn.removeClass("green").addClass("red");
				});
			}
		}
	});
});

$(".notification .delete").click(function() {
	let notification = $(this).closest(".notification");
	
	notification.addClass("deleting");
	
	$.post("/notifications/delete/" + notification.data("id"))
	.done(function() {
		notification.remove();
		if ($(".notification").length === 0) {
			$("#no-notifications").addClass("active");
			$("#delete-all").addClass("disabled");
		}
	})
	.fail(function() {
		notification.removeClass("deleting");
		alert("Failed to delete notification!");
	});
});

$("#delete-all").click(function() {
	if ($(this).hasClass("disabled")) return;
	
	$(".notification").addClass("deleting");
	
	$.post("/notifications/delete/all")
	.done(function() {
		$(".notification").remove();
		$("#no-notifications").addClass("active");
	})
	.fail(function() {
		$(".notification").removeClass("deleting");
		alert("Failed to delete notifications!");
	});
});