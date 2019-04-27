<?php

namespace App\Enums;

abstract class LeaveReason
{
	const Other = 0;
	const Illness = 1;
	const PhysicalHealth = 2;
	const MentalHealth = 3;
	const WorkEvent = 4;
	const NonWorkEvent = 5;
	const MaternityPaternity = 6;
	const Carer = 7;
	const Holiday = 8;

	public static function IsValidReason($reason)
	{
		return gettype($reason) === 'integer' && $reason >= 0 && $reason <= 8;
	}

	public static function GetReasonName($reason)
	{
		switch ($reason) {
			case LeaveReason::Other:
				return 'Other';
			case LeaveReason::Illness:
				return 'Illness';
			case LeaveReason::PhysicalHealth:
				return 'Physical Health';
			case LeaveReason::MentalHealth:
				return 'Mental Health';
			case LeaveReason::WorkEvent:
				return 'Work Event';
			case LeaveReason::NonWorkEvent:
				return 'Non-Work Event';
			case LeaveReason::MaternityPaternity:
				return 'Maternity/Paternity';
			case LeaveReason::Carer:
				return 'Childcare/Eldercare/Carer';
			case LeaveReason::Holiday:
				return 'Holiday';
			default:
				return 'Unknown';
		}
	}
}
