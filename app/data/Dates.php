<?php
namespace app\data;

class Dates {
	public static function concatYearMonthDay(?int $year, ?int $month, ?int $day) : string {
		$dateDay = $day != '' && $month != '' && $year != '' ? $day . '/' : '';
		$dateMonth = $month != '' && $year != '' ? $month . '/' : '';
		return $dateDay . $dateMonth . ($year === null ? '' : $year);
	}
}
