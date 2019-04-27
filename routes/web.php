<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\HasReadNotification;
use App\Http\Middleware\HumanResourcesOnly;
use App\Http\Middleware\ManagementOnly;
use App\Http\Middleware\Unauthenticated;
use App\Http\Middleware\UserEmployee;

Route::middleware([

	Authenticate::class,
	UserEmployee::class,
	HasReadNotification::class,

])->group(function () {
	Route::get('/auth/logout', 'LoginController@Logout')->name('logout');

	Route::redirect('/', '/announcements');
	Route::get('/announcements', 'AnnouncementController@ShowAnnouncements');

	// Employees

	Route::get('/employees/me', 'EmployeeController@ViewDetails');

	// Details

	Route::get('/employees/{employee_id}', 'EmployeeController@ViewDetails');
	Route::post('/employees/{employee_id}', 'EmployeeController@EditEmployee');

	// 1 to 1s

	Route::get('/employees/me/1to1', 'OneToOneController@ViewEmployee');
	Route::get('/1to1/{_1to1_id}', 'OneToOneController@View');
	Route::post('/1to1/{_1to1_id}', 'OneToOneController@Update');

	// Attendance

	Route::get('/employees/me/attendance', 'AttendanceController@DisplayAttendance');
	Route::post('/employees/me/attendance', 'AttendanceController@UpdateAttendance');

	Route::match(['get', 'post'], '/employees/me/leave', 'LeaveController@RequestLeave')->name('leave.request');
	Route::match(['get', 'post'], '/employees/me/leave/history', 'LeaveController@ViewHistory')->name('leave.history');
	Route::match(['get', 'post'], '/employees/me/leave/calendar', 'LeaveController@ViewCalendar')->name('leave.calendar');

	// Keycard

	Route::get('/employees/me/keycard', 'KeycardController@EmployeeKeycardSettings');
	Route::post('/employees/me/keycard', 'KeycardController@ControlKeycard');

	// Files

	Route::get('/employees/me/files', 'FileController@ViewEmployeeFiles');
	Route::post('/employees/me/files', 'FileController@UploadFile');
	Route::post('/employees/me/files/share', 'FileController@ShareFile');
	
	Route::middleware(HumanResourcesOnly::class)->group(function () {

		////////////////////////////////////////////////

		// Employees

		Route::match(['get', 'post'], '/employees', 'EmployeeController@ViewEmployees');

		// 1 to 1s

		Route::get('/employees/{employee_id}/1to1', 'OneToOneController@ViewEmployee');
		Route::match(['get', 'post'], '/hr/1to1', 'OneToOneController@ViewAll');
		Route::match(['get', 'post'], '/hr/1to1/new', 'OneToOneController@New');

		// Notations

		Route::get('/employees/{employee_id}/notations', 'NotationController@View');
		Route::post('/employees/{employee_id}/notations', 'NotationController@Update');

		// Leave

		Route::get('/employees/{employee_id}/leave', function ($employee_id) {
			return redirect("/employees/$employee_id/leave/history");
		});
		Route::get('/employees/{employee_id}/leave/history', 'LeaveController@ViewHistory')->name('leave.history');
		Route::match(['get', 'post'], '/employees/{employee_id}/leave/calendar', 'LeaveController@ViewCalendar')->name('leave.calendar');

		////////////////////////////////////////////////

		// Attendace

		Route::match(['post', 'get'], '/hr/attendance', 'AttendanceController@ViewCalendar');

		Route::get('/hr/leave', 'LeaveController@ViewLeaveRequests');
		Route::match(['get', 'post'], '/hr/leave/{leave_id}', 'LeaveController@LeaveRequest');

		// Recruitment
		
		Route::post('/hr/recruitment', 'EmployeeController@EditEmployee');
		Route::view('/hr/recruitment', 'recruitment.new');
	});

	Route::middleware(ManagementOnly::class)->group(function () {

		// Departments

		Route::get('/management/departments', 'DepartmentController@ViewDepartments');
		Route::get('/management/departments/new', 'DepartmentController@EditDepartment');
		Route::get('/management/departments/edit/{id}', 'DepartmentController@EditDepartment');

		Route::post('/api/departments/edit', 'DepartmentController@EditDepartment');

		// Keycard

		Route::get('/employees/{employee_id}/keycard', 'KeycardController@EmployeeKeycardSettings');
		Route::post('/employees/{employee_id}/keycard', 'KeycardController@ControlKeycard');

		// Files

		Route::get('/employees/{employee_id}/files', 'FileController@ViewEmployeeFiles');

		// Announcements

		Route::match(['get', 'post'], '/announcements/new', 'AnnouncementController@NewAnnouncement');
	});

	Route::get('/file/{file_id}', 'FileController@DownloadFile');

	// Notifications

	Route::get('/notifications', 'NotificationsController@ViewAll');
	Route::post('/notifications/delete/all', 'NotificationsController@DeleteAllNotifications');
	Route::post('/notifications/delete/{id}', 'NotificationsController@DeleteNotification');
	Route::get('/notifications/{id}', 'NotificationsController@OpenNotification');
	Route::post('/notifications/subscribe', 'NotificationsController@Subscribe');
	Route::post('/notifications/unsubscribe', 'NotificationsController@Unsubscribe');

	// API

	Route::post('/api/employees/search', 'EmployeeController@EmployeeSelector');
	Route::post('/api/employees/leave/request', 'LeaveController@UpdateApproval')->middleware(HumanResourcesOnly::class);
});

Route::middleware(Unauthenticated::class)->group(function () {
	Route::get('/auth/login', 'LoginController@Login')->name('login');
	Route::post('/auth/login', 'LoginController@Authenticate');
	Route::post('/auth/signup', 'LoginController@Signup');

	Route::post('/api/auth/signup/1', 'LoginController@HasSignupCode');
	Route::post('/api/auth/signup/2', 'LoginController@VerifySignupCode');
	Route::post('/api/auth/signup/3', 'LoginController@Signup');

	Route::get('/api/auth/keycard/token/{email}', 'KeycardController@GetToken');
	Route::get('/api/auth/keycard/token/{token}/cancel', 'KeycardController@CancelToken');

	Route::get('/api/auth/keycard/check/{token}', 'KeycardController@CheckAuthentication');

	Route::get('/api/auth/keycard/authenticate/{hash}', 'KeycardController@Authenticate');

	Route::get('/api/auth/keycard/success/{token}', 'KeycardController@LoginKeycardUser');
});

Route::get('/assets/img/employees/{hash}', 'EmployeeController@GetEmployeeAvatar');

Route::view('/algorithms', 'algorithms');
