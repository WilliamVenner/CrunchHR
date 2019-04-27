/* global $ moment CrunchHR ATTENDANCE_DATA DEPARTMENTS */

$("#clock-time").each(function(i) {
	let self = this;
	let page_date = new moment();
	window.setInterval(function() {
		let date = new moment();
		if (date.diff(page_date, 'days') >= 1) {
			window.location.reload();
		} else {
			$(self).text(date.format("hh:mm:ssa"));
		}
	}, 500);
});

$("#clock-in-btn").click(function() {
	if ($(this).hasClass("disabled")) return;
	if (confirm("Are you sure you would like to clock IN?")) {
		$("#clock-out").remove();
		$("#attendance-form").submit();
	}
});

$("#clock-out-btn").click(function() {
	if ($(this).hasClass("disabled")) return;
	if (confirm("Are you sure you would like to clock OUT?")) {
		$("#clock-in").remove();
		$("#attendance-form").submit();
	}
});

$("#report-absent-btn").click(function() {
	if ($(this).hasClass("disabled")) return;
	CrunchHR.Modals.Show("report-absence");
});

$(".calendar td:not(.buffer)").click(function() {
	$(".calendar td:not(.buffer)").removeClass("active");
	$(this).addClass("active");
	$("#attendance-details .tip").css("display", "none");
	
	$("#attendance-details tbody tr:not(.tip)").remove();
	if (ATTENDANCE_DATA[Number($(this).data("day"))]) {
		let attended_departments = {};
		$.each(ATTENDANCE_DATA[Number($(this).data("day"))], (k, v) => {
			let tr = document.createElement("tr");
			
			let td1 = document.createElement("td");
			let a = document.createElement("a");
			$(a).attr("href", "/management/departments/edit/" + v.department_id).text(DEPARTMENTS[v.department_id].name).appendTo(td1);
			$(td1).appendTo(tr);
			
			let td2 = document.createElement("td");
			$(td2).text(DEPARTMENTS[v.department_id].employee_count.toLocaleString()).appendTo(tr);
			
			let td3 = document.createElement("td");
			$(td3).text(Number(v.attendance_pct).toFixed(2).replace(/\.?0+$/,"") + "%").appendTo(tr);
			
			$(tr).appendTo("#attendance-details tbody");
			
			attended_departments[v.department_id] = true;
		});
		$.each(DEPARTMENTS, (k, v) => {
			if (!attended_departments.hasOwnProperty(v.id)) {
				let tr = document.createElement("tr");
				
				let td1 = document.createElement("td");
				let a = document.createElement("a");
				$(a).attr("href", "/management/departments/edit/" + v.id).text(v.name).appendTo(td1);
				$(td1).appendTo(tr);
				
				let td2 = document.createElement("td");
				$(td2).text(v.employee_count.toLocaleString()).appendTo(tr);
				
				let td3 = document.createElement("td");
				$(td3).text("0%").appendTo(tr);
				
				$(tr).appendTo("#attendance-details tbody");
			}
		});
	} else {
		$.each(DEPARTMENTS, (k, v) => {
			let tr = document.createElement("tr");
			
			let td1 = document.createElement("td");
			let a = document.createElement("a");
			$(a).attr("href", "/management/departments/edit/" + v.id).text(v.name).appendTo(td1);
			$(td1).appendTo(tr);
			
			let td2 = document.createElement("td");
			$(td2).text(v.employee_count.toLocaleString()).appendTo(tr);
			
			let td3 = document.createElement("td");
			$(td3).text("0%").appendTo(tr);
			
			$(tr).appendTo("#attendance-details tbody");
		});
	}
});