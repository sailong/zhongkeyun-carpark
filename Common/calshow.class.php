<?php
class calendar {
	private $year;
	private $month;
	private $day_week;
	function __construct() {
		$this->year = isset ( $_GET ['year'] ) ? $_GET ['year'] : date ( "Y" );
		$this->month = isset ( $_GET ['month'] ) ? $_GET ['month'] : date ( "m" );
		$this->day_week = date ( "w", mktime ( 0, 0, 0, $this->month, 1, $this->year ) );
	}
	private function xianDate() {
		echo "<tr>";
		echo "<td><a href='" . $this->nextyear ( $this->year, $this->month ) . "'>" . "<<<" . "</a></td>";
		echo "<td><a href='" . $this->nextmonth ( $this->month, $this->year ) . "'>" . "<<" . "</td>";
		echo "<td colspan='3'>" . $this->year . "年" . $this->month . "月</td>";
		echo "<td><a href='" . $this->aftermonth ( $this->month, $this->year ) . "'>" . ">>" . "</td>";
		echo "<td><a href='" . $this->afteryear ( $this->year, $this->month ) . "'>" . ">>>" . "</a></td>";
		echo "</tr>";
	}
	private function weeks() {
		$weeks = array (
				"日",
				"一",
				"二",
				"三",
				"四",
				"五",
				"六" 
		);
		echo "<tr>";
		foreach ( $weeks as $value ) {
			echo "<th>" . $value . "</th>";
		}
		echo "</tr>";
	}
	private function days() {
		echo "<tr>";
		for($i = 0; $i < $this->day_week; $i ++) {
			echo "<td>&nbsp;</td>";
		}
		for($j = 1; $j <= date ( "t", mktime ( 0, 0, 0, $this->month, 1, $this->year ) ); $j ++) {
			$i ++;
			if ($j == date ( "d" )) {
				echo "<td class='fontb'>" . $j . "</td>";
			} else {
				echo "<td>" . $j . "</td>";
			}
			if ($i % 7 == 0) {
				echo "</tr>";
			}
		}
		while ( $i % 7 != 0 ) {
			echo "<td>&nbsp;</td>";
			$i ++;
		}
	}
	private function nextyear($year, $month) {
		if ($year == 1970) {
			$year = 1970;
		} else {
			$year --;
		}
		return "?year=" . $year . "&month=" . $month;
	}
	private function afteryear($year, $month) {
		if ($year == 2038) {
			$year = 2038;
		} else {
			$year ++;
		}
		return "?year=" . $year . "&month=" . $month;
	}
	private function nextmonth($month, $year) {
		if ($month == 1) {
			$year --;
			$month = 12;
		} else {
			$month --;
		}
		return "?year=" . $year . "&month=" . $month;
	}
	private function aftermonth($month, $year) {
		if ($month == 12) {
			$year ++;
			$month = 1;
		} else {
			$month ++;
		}
		return "?year=" . $year . "&month=" . $month;
	}
	public function out() {
		// echo "<table align='center'>";
		// $this->xianDate();
		// $this->weeks();
		// $this->days();
		// echo "</table>";
		
		// $var = "<table align='center'>";
		$var = "<table align='center'>" . $this->xianDate () . $this->weeks () . $this->days () . "</table>";
		echo $var;
	}
}

?> 
