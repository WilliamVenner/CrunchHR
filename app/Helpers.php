<?php

namespace App;

class Helpers
{
	public static function BinarySearch(array $arr, $find)
	{
		$left = 0;
		$right = count($arr) - 1;

		while ($left <= $right) {
			$midpoint = (int) floor(($left + $right) / 2);
			$midpoint_item = $arr[$midpoint];
			if ($midpoint_item < $find) {
				$left = $midpoint + 1;
			} elseif ($midpoint_item > $find) {
				$right = $midpoint - 1;
			} else {
				return $midpoint;
			}
		}

		return null;
	}

	public static function MergeSort(array $arr)
	{
		$len = count($arr);
		if ($len <= 1) {
			return $arr;
		}

		$left = [];
		$right = [];

		foreach ($arr as $k => $v) {
			if ($k < $len / 2) {
				$left[] = $v;
			} else {
				$right[] = $v;
			}
		}

		$left = Helpers::MergeSort($left);
		$right = Helpers::MergeSort($right);

		$merged = [];

		while (true) {
			$left_empty = empty($left);
			$right_empty = empty($right);
			$both_not_empty = !$left_empty && !$right_empty;

			if (($both_not_empty && $left[0] <= $right[0]) || (!$both_not_empty && !$left_empty)) {
				$merged[] = $left[0];
				$left = array_splice($left, 1);
			} elseif (($both_not_empty && $left[0] > $right[0]) || (!$both_not_empty && !$right_empty)) {
				$merged[] = $right[0];
				$right = array_splice($right, 1);
			} else {
				break;
			}
		}

		return $merged;
	}

	public static function BubbleSort(array $arr)
	{
		$sorted = false;
		while (!$sorted) {
			$sorted = true;
			for ($i = 0; $i < count($arr) - 1; $i++) {
				$item1 = $arr[$i];
				$item2 = $arr[$i + 1];
				if ($item1 > $item2) {
					$arr[$i + 1] = $item1;
					$arr[$i] = $item2;
					$sorted = false;
				}
			}
		}

		return $arr;
	}
	
	public static function LinearSearch(array $arr, $find)
	{
		foreach($arr as $i => $item) {
			if ($item == $find) return $i;
		}
		return null;
	}

	public static function JSONResponse($obj)
	{
		return response(json_encode($obj))->header('Content-Type', 'application/json');
	}

	// http://jeffreysambells.com/2012/10/25/human-readable-filesize-php
	public static function FormatFileSize($bytes, $decimals = 2)
	{
		$size = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
		$factor = floor((strlen($bytes) - 1) / 3);

		return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)).' '.@$size[$factor];
	}

	public static function HumanMIMEType($mime_type)
	{
		$machine_mime_types = file(resource_path('data/mime_types/machine.txt'), FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		$mime_type_index = \App\Helpers::BinarySearch($machine_mime_types, $mime_type);
		if ($mime_type_index == null) {
			return $mime_type;
		}

		$human_mime_types = fopen(resource_path('data/mime_types/human.txt'), 'r');

		$i = 0;
		while (!feof($human_mime_types)) {
			$line = fgets($human_mime_types);
			if ($i === $mime_type_index) {
				fclose($human_mime_types);

				return $line;
			}
			$i += 1;
		}

		fclose($human_mime_types);

		return $mime_type;
	}

	public static function GetFileType($str)
	{
		$finfo = new \finfo(FILEINFO_MIME_TYPE);
		$mime_type = $finfo->buffer($str);

		return $mime_type;
	}

	public static function NumberWithOrdinal($int)
	{
		if ($int >= 11 && $int <= 13) {
			return $int.'th';
		}
		switch (substr(strval($int), -1, 1)) {
			case '1':
				return $int.'st';
			case '2':
				return $int.'nd';
			case '3':
				return $int.'rd';
			default:
				return $int.'th';
		}
	}
	
	public static function Verify_reCAPTCHA($response) {
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, [
			'secret'   => env('RECAPTCHA_SECRET_KEY'),
			'response' => $response,
		]);
		
		$result = curl_exec($ch);
		if (curl_errno($ch)) return false;
		curl_close($ch);
		
		$result_decoded = json_decode($result);
		if (!$result_decoded) return false;
		
		return isset($result_decoded->success) && $result_decoded->success;
	}
}
