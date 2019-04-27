/* global $ CrunchHR ViewingEmployee */

$("#employee-details .avatar").change(function() {
	$("#employee-details .avatar-errors > div").removeClass("shake");
	
	if (this.files.length > 0) {
		let allow = true;
		if (this.files[0].size > 2000000) {
			$("#employee-details .avatar-errors .filesize").addClass("shake");
			allow = false;
		}
		if (this.files[0].type !== "image/png" && this.files[0].type !== "image/jpeg") {
			$("#employee-details .avatar-errors .filetype").addClass("shake");
			allow = false;
		}
		if (!allow) {
			$("#employee-details .avatar-remove").click();
			return;
		}
		$("#employee-details .avatar-remove").removeClass("hidden");
		
		let reader = new FileReader();
		reader.onload = function(e) {
			$("#recruitment .avatar img").attr("src", e.target.result);
		};
		reader.readAsDataURL(this.files[0]);
	}
});

$(".messagebox .delete").closest("form").submit(function() {
	if (!confirm("Are you sure you want to delete this?")) {
		return false;
	}
});

$("#keycard-revoke").click(function() {
	if (confirm("Are you sure you want to revoke this keycard?")) {
		$("#keycard-revoke-form").submit();
	}
});

$("#file-upload").change(function(e) {
	$("#max-file-size-warning").css("color", "inherit");
	
	if (e.currentTarget.files.length > 0) {
		if (e.currentTarget.files[0].size <= 5242880) { // 5 Mebibytes
			$("#file-upload-btn").removeClass("disabled").prop("disabled", false);
			return;
		} else {
			$("#max-file-size-warning").css("color", "red");
		}
	}
	$("#file-upload-btn").addClass("disabled").prop("disabled", true);
});

$(".share-file").click(function() {
	CrunchHR.Modals.Show("share-file-modal");
	$("#modals .share-file-modal .file-id").val($(this).closest("tr").data("id"));
});
$("#modals .share-file-modal .btn").click(function() {
	if (!$("#modals .share-file-modal .employee-selector").data("employee-id")) {alert("Please enter an employee."); return;}
	if ($("#modals .share-file-modal .employee-selector").data("employee-id") == CrunchHR.UserEmployee) {alert("You cannot share a file with yourself."); return;}
	
	CrunchHR.LoadingOverlay();
	
	$.post("/employees/me/files/share", {file_id: $("#modals .share-file-modal .file-id").val(), employee_id: $("#modals .share-file-modal .employee-selector").data("employee-id")})
		.always(() => {
			CrunchHR.LoadingOverlay();
		})
		.fail(() => {
			alert("Failed to share file with employee!");
		})
		.then(() => {
			CrunchHR.Modals.Hide();
		});
});