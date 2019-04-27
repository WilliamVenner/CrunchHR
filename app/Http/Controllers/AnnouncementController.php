<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
	public function ShowAnnouncements()
	{
		$Announcements = \App\Models\Announcement::orderBy('created_at', 'DESC')->get();

		return view('announcements.view', ['Announcements' => $Announcements]);
	}

	public function NewAnnouncement(Request $request)
	{
		$UserEmployee = $request->get('UserEmployee');

		if (isset($_POST['announce'])) {
			if (!isset($_POST['title'], $_POST['body']) || empty($_POST['title']) || empty($_POST['body'])) {
				return abort(400);
			}
			
			$is_important = isset($_POST['important']) && $_POST['important'] === 'on';

			$Announcement = new \App\Models\Announcement();
			$Announcement->employee_id = $UserEmployee->id;
			$Announcement->important = $is_important;
			$Announcement->title = $_POST['title'];
			$Announcement->contents = $_POST['body'];
			$Announcement->save();

			if ($is_important) {
				\App\Notifications::NotifyAll('Important Announcement: ' . $_POST['title'], 'A new important announcement has been posted', '/announcements', $UserEmployee->id);
			}

			return redirect('/announcements');
		} else {
			return view('announcements.new');
		}
	}
}
