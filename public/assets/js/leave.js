/* global $ moment CrunchHR */

$("#to").focusout(function(e) {
	let from = moment($("#from").val(), 'YYYY-MM-DD');
	let to = moment($(this).val(), 'YYYY-MM-DD');
	if (from > to) {
		alert("The date you are absent from must not be greater than the date you are absent to.");
		$(this).val("");
	}
});

$(".approve").click(function() {
    let id = $(this).closest("tr").data("id");
    CrunchHR.LoadingOverlay();
    
    $.post("/api/employees/leave/request", {id: id, approved: 1})
        .always(() => {
            CrunchHR.LoadingOverlay();
        })
        .then(() => {
            window.location.reload();
        })
        .fail(() => {
            alert("Failed to approve leave");
        });
});

$(".unapprove").click(function() {
    let id = $(this).closest("tr").data("id");
    CrunchHR.LoadingOverlay();
    
    $.post("/api/employees/leave/request", {id: id, approved: 0})
        .always(() => {
            CrunchHR.LoadingOverlay();
        })
        .then(() => {
            window.location.reload();
        })
        .fail(() => {
            alert("Failed to reject leave");
        });
});