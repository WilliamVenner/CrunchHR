/* global $ */

//############### CSRF TOKEN ###############//

if ($("meta[name='csrf-token']").length > 0) $.ajaxSetup({headers: {"X-CSRF-TOKEN": $("meta[name='csrf-token']").attr("content")}});

var KEYCARD_LOGIN_AUTHENTICATION_TOKEN = null;
function CheckKeycardAuthentication() {
	if (KEYCARD_LOGIN_AUTHENTICATION_TOKEN === null) return;
	$.get("/api/auth/keycard/check/" + KEYCARD_LOGIN_AUTHENTICATION_TOKEN)
		.done(function(result) {
			if (result == true) {
				window.onbeforeunload = null;
				window.location = "/api/auth/keycard/success/" + KEYCARD_LOGIN_AUTHENTICATION_TOKEN + ($("#remember_me_checkbox").prop("checked") ? "1" : "0");
			} else {
				window.setTimeout(CheckKeycardAuthentication, 2500);
			}
		})
		.fail(function(req) {
			if (req.status === 404) {
				$("#auth-container").removeClass("blur");
				alert("Your keycard login request expired due to a conflicting keycard login request or expiration of authentication token.");
			} else {
				window.setTimeout(CheckKeycardAuthentication, 2500);
			}
		});
}

$("#keycard-login-btn").click(function() {
	let email = $("#email").val();
	if (email.length > 0) {
		$("#auth-container").addClass("blur");
		
		$.get("/api/auth/keycard/token/" + email)
			.done(function(token) {
				$("#keycard-login").addClass("active");
				
				window.onbeforeunload = function() {
					$.get("/api/auth/keycard/token/" + KEYCARD_LOGIN_AUTHENTICATION_TOKEN + "/cancel");
					KEYCARD_LOGIN_AUTHENTICATION_TOKEN = null;
				};
				
				KEYCARD_LOGIN_AUTHENTICATION_TOKEN = token;
				CheckKeycardAuthentication();
			})
			.fail(function(req) {
				if (req.status === 404) {
					alert("Your email address is invalid.");
				} else {
					alert("Error! HTTP " + req.status);
				}
				$("#auth-container").removeClass("blur");
			});
	} else {
		alert("Please enter your email address first.");
	}
});

$("#keycard-login-cancel").click(function() {
	$.get("/api/auth/keycard/token/" + KEYCARD_LOGIN_AUTHENTICATION_TOKEN + "/cancel");
	KEYCARD_LOGIN_AUTHENTICATION_TOKEN = null;
	
	$("#auth-container").removeClass("blur");
	$("#keycard-login").removeClass("active");
});

$("#email, #password").on("input propertychange paste", function() {
	$("#login").prop("disabled", $("#email").val().length === 0 || $("#password").val().length === 0);
});

var IsSignupRequired_Timeout;
var IsSignupRequired_Cache = {};
function IsSignupRequired() {
	if (IsSignupRequired_Timeout) {
		clearTimeout(IsSignupRequired_Timeout);
		IsSignupRequired_Timeout = null;
	}
	
	let email = $("#email").val();
	if (email.length === 0) return;
	if (IsSignupRequired_Cache[email] === true) {
		$("#signup-1").addClass("active");
		$("#auth-container").addClass("blur");
	} else if (IsSignupRequired_Cache[email] === false) {
		return;
	}
	
	$.post("/api/auth/signup/1", {email: email}).done(function(data) {
		if (data == "1") {
			IsSignupRequired_Cache[email] = true;
			$("#signup-1").addClass("active");
			$("#auth-container").addClass("blur");
		} else if (data == "0") {
			IsSignupRequired_Cache[email] = false;
		}
	});
}
$("#email").focusout(IsSignupRequired).on("input propertychange paste", function() {
	if (IsSignupRequired_Timeout) {
		clearTimeout(IsSignupRequired_Timeout);
		IsSignupRequired_Timeout = null;
	} else {
		IsSignupRequired_Timeout = window.setTimeout(function() {
			IsSignupRequired_Timeout = null;
			IsSignupRequired();
		}, 1000);
	}
});

var SignupCode = [];
function VerifySignupCode() {
	$("#signup-code input").prop("disabled", true);
	
	$.post("/api/auth/signup/2", {email: $("#email").val(), signup_code: SignupCode.join("")}).done(function(response) {
		if (response == "0") {
			alert("Your signup code appears to be incorrect");
			SignupCode = [];
			$("#signup-code input").val("").prop("disabled", false).eq(0).focus();
		} else if (typeof response === "object") {
			$("#signup-1").removeClass("active");
			$("#signup-2").addClass("active");
			$("#employee-name").text(response.name);
			$("#signup-2 .employee-picture").attr("src", response.picture);
			
			$("#signup-email-input").val($("#email").val());
			$("#signup-code-input").val(SignupCode.join(""));
		} else {
			alert("Failed to verify signup code!");
		}
	}).fail(function() {
		alert("Failed to verify signup code!");
	}).always(function() {
		$("#signup-code input").prop("disabled", false);
	});
}
$("#signup-code input").on("keydown", function(e) {
	if (e.ctrlKey && e.key == "v") return;
	if (e.keyCode === 8 || e.keyCode === 46) {
		if ($(this).val().length === 0) {
			e.preventDefault();
			if ($(this).index() !== 0) {
				delete(SignupCode[$(this).index() - 1]);
				$("#signup-code input").eq($(this).index() - 1).val("").focus();
			}
		} else {
			delete(SignupCode[$(this).index()]);
		}
	} else if (e.key.match(/^[0-9]$/)) {
		e.preventDefault();
		$(this).val(e.key);
		
		SignupCode[$(this).index()] = e.key;
		if (SignupCode.length === 6) {
			VerifySignupCode();
		} else {
			let next_input = $("#signup-code input").eq($(this).index() + 1);
			if (next_input.length > 0 && $(next_input).val().length === 0) {
				$(next_input).focus();
			}
		}
	} else {
		e.preventDefault();
	}
}).on("paste", function(e) {
	var pasteData = e.originalEvent.clipboardData.getData("text").trim();
	if (pasteData.match(/^[0-9]{6}$/)) {
		$("#signup-code input").val("").each(function(k,v) {
			$(v).val(pasteData[k]);
		});
		SignupCode = pasteData.split("");
		VerifySignupCode();
	}
	return false;
});

$(".signup-cancel").click(function() {
	$("#signup-1, #signup-2").removeClass("active");
	$("#auth-container").removeClass("blur");
	$("#signup-code input").val("");
	SignupCode = [];
});

$("#signup-password, #signup-confirm-password").on("input propertychange paste", function() {
	$("#signup-btn").prop("disabled", $("#signup-password").val() != $("#signup-confirm-password").val() || !$("#signup-password").val().match(/\d/) || $("#signup-password").val().length < 8);
});