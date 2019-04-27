/* global $ moment tippy navigator PushManager */

const CrunchHR = {};

//############### CSRF TOKEN ###############//

$.ajaxSetup({headers: {"X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")}});

//############### NOTIFICATIONS & SERVICE WORKER ###############//

CrunchHR.Notifications = {};

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

let SW_Callbacks = [];
function Execute_SW_Callbacks() {
	let worked = typeof CrunchHR.Notifications.ServiceWorker != "undefined";
	for (let i = 0; i < SW_Callbacks.length; i++) {
		SW_Callbacks[i](worked, CrunchHR.Notifications.ServiceWorker);
	}
}
CrunchHR.Notifications.GetServiceWorker = function(callback) {
	if (typeof CrunchHR.Notifications.ServiceWorker != "undefined") {
		callback(true, CrunchHR.Notifications.ServiceWorker);
	} else {
		SW_Callbacks.push(callback);
	}
};

CrunchHR.Notifications.Subscribe = function(callback) {
	CrunchHR.LoadingOverlay();
	
	CrunchHR.Notifications.ServiceWorker.pushManager.subscribe({
		userVisibleOnly: true,
		applicationServerKey: urlBase64ToUint8Array(CrunchHR.Notifications.PublicKey)
	}).then(function(subscription) {
		let key = subscription.getKey("p256dh");
		let auth = subscription.getKey("auth");
		$.post("/notifications/subscribe", {subscription_data: JSON.stringify(
			{
				"endpoint": subscription.endpoint,
				"publicKey": key ? window.btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('p256dh')))) : null,
				"authToken": auth ? window.btoa(String.fromCharCode.apply(null, new Uint8Array(subscription.getKey('auth')))) : null,
				"contentEncoding": (PushManager.supportedContentEncodings || ['aesgcm'])[0],
			}
		)})
		.fail(function(err) {
			alert("Failed to update your subscription status on the server.\nPlease try again.");
			console.log(err);
			callback(false);
		})
		.then(function() {
			callback(true);
			alert("Successfully subscribed to push notifications on this device.");
		})
		.always(function() {
			CrunchHR.LoadingOverlay();
		});
	}).catch(function(err) {
		alert("Failed to subscribe to notifications!\n" + err);
		CrunchHR.LoadingOverlay();
		callback(false);
	});
};

CrunchHR.Notifications.Unsubscribe = function(callback) {
	CrunchHR.LoadingOverlay();
	
	CrunchHR.Notifications.ServiceWorker.pushManager.getSubscription()
	.then(function(subscription) {
		if (subscription !== null) {
			$.post("/notifications/unsubscribe", {endpoint: subscription.endpoint})
			.always(function() {
				CrunchHR.LoadingOverlay();
			}).fail(function(err) {
				alert("Failed to update your subscription status on the server.\nPlease try again.");
				console.log(err);
				callback(false);
			}).then(function() {
				callback(true);
				alert("Successfully unsubscribed from push notifications on this device.");
			});
			
			subscription.unsubscribe();
		} else {
			CrunchHR.LoadingOverlay();
			callback(true);
		}
	})
	.catch(function() {
		CrunchHR.LoadingOverlay();
		callback(true);
	});
};

if ('Notification' in window && 'serviceWorker' in navigator && 'PushManager' in window) {
	navigator.serviceWorker.register("/sw.js")
	.then(function(swReg) {
		console.log("Service worker registered successfully");
		CrunchHR.Notifications.ServiceWorker = swReg;
		Execute_SW_Callbacks();
	}).catch(function(err) {
		console.log("Notifications service worker registration error:\n" + err);
		Execute_SW_Callbacks();
	});
} else {
	console.log("Notifications not supported by this browser");
	Execute_SW_Callbacks();
}

//############### LOADING OVERLAY ###############//

CrunchHR.LoadingOverlay = function() {
	$("html").toggleClass("blur");
};

//############### TIMESTAMPS ###############//

CrunchHR.FormatTimestamp = function(timestamp_str) {
	let cur_moment = moment.unix(Number(timestamp_str));
	return [cur_moment.fromNow(), cur_moment.format("ddd Do MMM YYYY h:mm:ssa")];
};

$(".timestamp").each(function() {
	let formatted = CrunchHR.FormatTimestamp($(this).data("timestamp"));
	$(this).text(formatted[0]);
	tippy(this, {
		content: formatted[1],
		arrow: true,
		arrowType: "round",
		inertia: true,
		animation: "perspective"
	});
});

//############### EMPLOYEE SELECTOR ###############//

CrunchHR.EmployeeSelector = {};
CrunchHR.EmployeeSelector.Cache = {};
function PopulateEmployeeSelector(employees) {
	if (employees.length === 0) {
		$("#employee-selector").removeClass("loading").removeClass("error").addClass("no-results");
	} else {
		for (let i=0; i < employees.length; i++) {
			let employee = employees[i];
			
			let profile = document.createElement("div");
			$(profile).addClass("profile").data("employee-id", employee.id).appendTo("#employee-selector");
			
			let avatar = document.createElement("img");
			$(avatar).appendTo(profile);
			if (employee.hash) {
				$(avatar).attr("src", "/assets/img/employees/" + employee.hash + ".jpg");
			} else {
				$(avatar).attr("src", "/assets/img/employee.jpg");
			}
			
			let body = document.createElement("div");
			$(body).addClass("body").appendTo(profile);
			
			let name = document.createElement("span");
			
			let full_name = employee.first_name;
			if (employee.middle_name)
				full_name += " " + employee.middle_name;
			if (employee.last_name)
				full_name += " " + employee.last_name;
			
			$(name).addClass("name").text(full_name).appendTo(body);
			
			let relevance = document.createElement("span");
			$(relevance).addClass("relevance").text(employee.relevance + "%").appendTo(body);
		}
	}
}
CrunchHR.EmployeeSelector.Search = function(search_for) {
	$("#employee-selector .profile").remove();
	if (CrunchHR.EmployeeSelector.Timeout) {
		clearTimeout(CrunchHR.EmployeeSelector.Timeout);
		CrunchHR.EmployeeSelector.Timeout = null;
	}
	if (CrunchHR.EmployeeSelector.Cache[search_for]) {
		$("#employee-selector").removeClass("no-results").removeClass("error").removeClass("loading");
		PopulateEmployeeSelector(CrunchHR.EmployeeSelector.Cache[search_for]);
		return;
	}
	$("#employee-selector").removeClass("no-results").removeClass("error").addClass("loading");
	CrunchHR.EmployeeSelector.Timeout = window.setTimeout(function() {
		CrunchHR.EmployeeSelector.Timeout = null;
		$.post("/api/employees/search", {search_for: search_for}).fail(function() {
			$("#employee-selector").addClass("error");
		}).always(function() {
			$("#employee-selector .profile").remove();
			$("#employee-selector").removeClass("loading");
		}).done(function(employees) {
			CrunchHR.EmployeeSelector.Cache[search_for] = employees;
			PopulateEmployeeSelector(employees);
		});
	}, 500);
};
CrunchHR.EmployeeSelector.Init = function(target_elem) {
	let search_for = $(target_elem).val().trim();
	
	if (search_for.length === 0) {
		$("#employee-selector").addClass("hidden");
		return;
	}
	
	let h = $(target_elem).outerHeight();
	let pos = $(target_elem).offset();
	
	$("#employee-selector")
	.removeClass("hidden")
	.css("left", pos.left + "px")
	.css("top", pos.top + h + "px")
	.css("width", $(target_elem).outerWidth() - 2 + "px");
	
	CrunchHR.EmployeeSelector.Search(search_for);
	CrunchHR.EmployeeSelector.LastAttachedElement = target_elem;
};
$(".employee-selector").attr("placeholder", "Type an employee's name...").attr("autocomplete", "off")
.on("input propertychange paste", function() {
	CrunchHR.EmployeeSelector.Init(this);
}).focus(function() {
	if (!$("#employee-selector").hasClass("hidden")) return;
	CrunchHR.EmployeeSelector.Init(this);
	
	if ($(this).data("employee-id")) $(this).data("employee-name", $(this).val());
}).focusout(function(e) {
	if ($("#employee-selector:hover").length > 0) return;
	$("#employee-selector").addClass("hidden");
	if (CrunchHR.EmployeeSelector.Timeout) {
		clearTimeout(CrunchHR.EmployeeSelector.Timeout);
		CrunchHR.EmployeeSelector.Timeout = null;
	}
	if ($(this).val().length === 0) {
		$(this).removeData("employee-id");
		$(this).removeData("employee-name");
	} else if (!$(this).data("employee-id")) {
		$(this).val("");
	} else {
		$(this).val($(this).data("employee-name"));
	}
});

$("#employee-selector").on("click", ".profile", function() {
	$(CrunchHR.EmployeeSelector.LastAttachedElement).data("employee-id", $(this).data("employee-id")).val($(this).find(".name").text()).trigger("employee-selected");
	$("#employee-selector").addClass("hidden");
});

$(".employee-selector").closest("form").submit(function() {
	$(this).find(".employee-selector").each((_, v) => {
		if ($(v).data("employee-id")) $(v).val($(v).data("employee-id"));
	});
});

//############### ADDRESS FORM SANITY ENFORCER ###############//

$(".address-form").on("input propertychange paste", "input", function() {
	let address_form = $(this).closest(".address-form");
	let has_data = $(address_form).hasClass("has-data");
	
	if ($(this).val().length > 0) {
		if (!has_data) $(address_form).addClass("has-data").find("input:not([name='address_2'])").prop("required", true);
	} else if (has_data) {
		let has_data_now = false;
		$(address_form).find("input:not([name='address_2'])").each(function() {
			if ($(this).val().length > 0) {
				has_data_now = true;
				return false;
			}
		});
		if (!has_data_now) {
			$(address_form).removeClass("has-data").find("input:not([name='address_2'])").prop("required", false);
		}
	}
});

//############### SIGN OUT ###############//

$("#signout").click(function() {
	if (confirm("Are you sure you would like to sign out of CrunchHR?")) {
		window.location = "/auth/logout";
	}
});

//############### MODALS ###############//

CrunchHR.Modals = {};

CrunchHR.Modals.Show = function(class_name) {
	CrunchHR.Modals.Hide();
	$("#modals, #modals .modal." + class_name).addClass("active");
};
CrunchHR.Modals.Hide = function() {
	$("#modals, #modals .modal").removeClass("active");
};

$("#modals").click(function(e) {
	if (e.target == $("#modals")[0]) {
		CrunchHR.Modals.Hide();
	}
});