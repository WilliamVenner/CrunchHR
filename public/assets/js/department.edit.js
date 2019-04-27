/* global $ CrunchHR */

function ValidateForm() {
	$("#save-btn").toggleClass("disabled", $("#department_name").val().trim().length == 0);
}

$("#department_name").on("input", ValidateForm);
ValidateForm();

let job_index = 0;
let jobs = [];
let jobs_dictionary = {};

$(".job").each(function() {
	jobs.push({
		new: false,
		id: $(this).data("job-id"),
		name: $(this).find("td:first-child").text()
	});
	jobs_dictionary[$(this).find("td:first-child").text().toLowerCase()] = true;
});

$("#add-new-job").click(function() {
	let job_name = prompt("Enter the name of this job");
	if (job_name) {
		job_name = job_name.trim();
		if (job_name.length > 0) {
			if (jobs_dictionary[job_name.toLowerCase()]) {
				alert("A job with that name already exists in this department!");
			} else {
				jobs_dictionary[job_name.toLowerCase()] = true;
				
				jobs.push({new: true, index: job_index, name: job_name});
				
				let row = $("#jobs-table .row-template").clone();
				$(row).data("job-index", job_index).data("new", true).removeClass("row-template").addClass("job").appendTo("#jobs-table tbody").find("td:first-child").text(job_name);
				
				$("#jobs-table").addClass("has-results");
				job_index += 1;
			}
		}
	}
});

$("#jobs-table").on("click", ".delete-job", function() {
	let yes = confirm("Are you sure?");
	if (yes) {
		let job = $(this).closest(".job");
		
		delete(jobs_dictionary[job.find("td:first-child").text().toLowerCase()]);
		
		if (job.data("new") == true) {
			let job_index = Number(job.data("job-index"));
			for (let i = 0; i < jobs.length; i++) {
				let job = jobs[i];
				if (job.index === job_index) {
					jobs.splice(i, 1);
					break;
				}
			}
		} else {
			let job_id = Number(job.data("job-id"));
			for (let i = 0; i < jobs.length; i++) {
				let job = jobs[i];
				if (job.id === job_id) {
					jobs.splice(i, 1);
					break;
				}
			}
		}
		
		job.remove();
		
		if (jobs.length === 0) {
			$("#jobs-table").removeClass("has-results");
		}
	}
});

$("#jobs-table").on("click", ".edit-job", function() {
	let job = $(this).closest(".job");
	
	let new_name = prompt("New name for this job", job.find("td:first-child").text()).trim();
	if (new_name.length > 0) {
		if (jobs_dictionary[new_name.toLowerCase()]) {
			alert("A job with that name already exists in this department!");
		} else {
			
			if (job.data("new") == true) {
				let job_index = Number(job.data("job-index"));
				for (let i = 0; i < jobs.length; i++) {
					let job = jobs[i];
					if (job.index === job_index) {
						jobs[i].name = new_name;
						break;
					}
				}
			} else {
				let job_id = Number(job.data("job-id"));
				for (let i = 0; i < jobs.length; i++) {
					let job = jobs[i];
					if (job.id === job_id) {
						jobs[i].name = new_name;
						break;
					}
				}
			}
			
			jobs_dictionary[new_name.toLowerCase()] = true;
			delete(jobs_dictionary[job.find("td:first-child").text().toLowerCase()]);
			
			job.find("td:first-child").text(new_name);
		}
	}
});

$("#save-btn").click(function() {
	if ($(this).hasClass("disabled")) return;
	
	CrunchHR.LoadingOverlay();
	
	let form_data = {};
	
	if ($("#edit_department_id").length > 0) {
		form_data["department_id"] = $("#edit_department_id").val();
	}
	form_data["department_name"] = $("#department_name").val();
	if ($("#head_of_department").data("employee-id") && $("#head_of_department").val().length > 0) form_data["head_of_department"] = $("#head_of_department").data("employee-id");
	form_data["jobs"] = JSON.stringify(jobs);
	
	$.post("/api/departments/edit", form_data).fail(function() {
		alert("Failed to save department (unknown error)");
		CrunchHR.LoadingOverlay();
	}).done(function(response) {
		if (response) {
			if (response.success) {
				if (response.department_id) {
					window.location = "/management/departments/edit/" + response.department_id;
				} else {
					window.location.reload();
				}
				return;
			} else if (response.error) {
				alert(response.error);
			} else {
				alert("Failed to save department (unknown error)");
			}
		} else {
			alert("Failed to save department (unknown error)");
		}
		CrunchHR.LoadingOverlay();
	});
});