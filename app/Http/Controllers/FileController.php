<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FileController extends Controller
{
	public function ViewEmployeeFiles(Request $request, $employee_id = 'me')
	{
		$Employee;
		if ($employee_id === 'me') {
			$Employee = $request->get('UserEmployee');
		} else {
			$Employee = \App\Models\Employee::where('id', $employee_id)->first();
		}
		if (!$Employee) {
			return abort(404);
		}

		$Files = \App\Models\File::hydrate(DB::table('files')->select('id', 'employee_id', 'file_name', 'file_type', 'file_mime_type', 'file_size', 'created_at')->orderBy('created_at', 'DESC')->where('employee_id', $Employee->id)->get()->toArray());
		$SharedFiles = \App\Models\File::hydrate(DB::select('
			SELECT `id`, files.`employee_id`, `file_name`, `file_type`, `file_mime_type`, `file_size`, `created_at` FROM files_sharing
			INNER JOIN files ON `id`=`file_id`
			WHERE files_sharing.`employee_id`=?
		', [$Employee->id]));

		return view('files.view_employee', ['Employee' => $Employee, 'EmployeeURLID' => $employee_id, 'Files' => $Files, 'SharedFiles' => $SharedFiles]);
	}

	public function DownloadFile(Request $request, $file_id)
	{
		$File = \App\Models\File::where('id', $file_id)->first();
		if (!$File) {
			return abort(404);
		}

		$UserEmployee = $request->get('UserEmployee');
		if (!$UserEmployee->is_management()) {
			$FileShare = \App\Models\FileShare::where('employee_id', $UserEmployee->id)->first();
			if (!$FileShare) {
				return abort(403, 'You don\'t have permission to download this file.');
			}
		}

		return response($File->file_contents)->header('Content-Type', $File->file_mime_type)->header('Content-Length', $File->file_size)->header('Content-Transfer-Encoding', 'Binary')->header('Content-Disposition', 'attachment; filename="'.str_replace('"', '\\"', $File->file_name.($File->file_type !== null ? '.'.$File->file_type : '')).'"');
	}

	public function UploadFile(Request $request)
	{
		$UserEmployee = $request->get('UserEmployee');

		if (!isset($_FILES['employee-file-upload'])) {
			return abort(400);
		}

		// pick out file type from name and strip it using regex
		preg_match('/^.+?(?:(\..*?))*?$/', $_FILES['employee-file-upload']['name'], $file_name_matches);
		$file_name = $file_name_matches !== null && isset($file_name_matches[1]) && !empty($file_name_matches[1]) && $file_name_matches[1] !== '.' ? substr($_FILES['employee-file-upload']['name'], 0, -strlen($file_name_matches[1])) : $_FILES['employee-file-upload']['name'];

		// file type without prefixed fullstop (or null if file has no type)
		$file_type = $file_name_matches !== null && isset($file_name_matches[1]) && !empty($file_name_matches[1]) ? substr($file_name_matches[1], 1) : null;

		// read the temporarily stored file upload for saving it to database and detecting MIME type
		$file_contents = file_get_contents($_FILES['employee-file-upload']['tmp_name']);

		// calculate file size in bytes
		$file_size = strlen($file_contents);

		// get file's mime type
		$mime_type = $_FILES['employee-file-upload']['type'];

		$File = new \App\Models\File();
		$File->employee_id = $UserEmployee->id;
		$File->file_name = $file_name;
		$File->file_type = $file_type;
		$File->file_mime_type = $mime_type;
		$File->file_size = $file_size;
		$File->file_contents = $file_contents;
		$File->save();

		// delete the temporarily stored file upload
		unlink($_FILES['employee-file-upload']['tmp_name']);

		return redirect('/'.$request->path());
	}
	
	public function ShareFile(Request $request) {
		$UserEmployee = $request->get('UserEmployee');
		if (!isset($_POST['file_id'], $_POST['employee_id']) || $UserEmployee->id == $_POST['employee_id']) return abort(400);
		
		$File = \App\Models\File::where('id', $_POST['file_id'])->first();
		if (!$File) return abort(404);
		
		$Employee = \App\Models\Employee::where('id', $_POST['employee_id'])->first();
		if (!$Employee) return abort(404);
		
		$ExistingFileShare = \App\Models\FileShare::where(['file_id' => $_POST['file_id'], 'employee_id' => $_POST['employee_id']])->first();
		if (!$ExistingFileShare) {
			$FileShare = new \App\Models\FileShare;
			$FileShare->file_id = $_POST['file_id'];
			$FileShare->employee_id = $_POST['employee_id'];
			$FileShare->save();
		}
	}
}
