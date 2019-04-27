/* global $ */

$("#recruitment .avatar input").change(function() {
	$("#avatar-errors > div").removeClass("shake");
	
	if (this.files.length > 0) {
		let allow = true;
		if (this.files[0].size > 2000000) {
			$("#avatar-errors .filesize").addClass("shake");
			allow = false;
		}
		if (this.files[0].type !== "image/png" && this.files[0].type !== "image/jpeg") {
			$("#avatar-errors .filetype").addClass("shake");
			allow = false;
		}
		if (!allow) {
			$("#avatar-remove").click();
			return;
		}
		$("#avatar-remove").removeClass("hidden");
		
		let reader = new FileReader();
		reader.onload = function(e) {
			$("#recruitment .avatar img").attr("src", e.target.result);
		};
		reader.readAsDataURL(this.files[0]);
	}
});

$("#avatar-remove").click(function() {
	$("#recruitment .avatar img").attr("src", "/assets/img/employee.png");
	$("#recruitment .avatar input").val("");
	$(this).addClass("hidden");
});