<?php
/**
 * Tiny Mvc 
 * TimeComponent.php
 *                   
 * User: Robbe Ingelbrecht
 * Date: 19/06/13 15:56
 *
 */

class TimeComponent extends Component{

	/**
	 * Returns a timestamp from the given date
	 *
	 * @param $date
	 * @return int|string
	 */
	private function _toTimestamp($date) {
		// Indien $date geen getal is, dan is het in date-format
		if (!is_numeric($date)) {
			return strtotime($date);
		}
		else {
			return $date;
		}
	}

	/**
	 * Returns true if the given date is within the current week's range
	 * Returns false otherwise
	 *
	 * @param $date
	 * @return bool
	 */
	public function isThisWeek($date) {
		$time = $this->_toTimestamp($date);
		$monday = strtotime('last monday');
		$sunday = strtotime('sunday this week');

		if ($time >= $monday && $time <= $sunday) {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * Returns true if the given date is within the current day's range
	 * Returns false otherwise
	 *
	 * @param $date
	 * @return bool
	 */
	public function isToday($date) {
		$time = $this->_toTimestamp($date);
		$dayStart = strtotime(date("m/d/Y"), time());
		$dayEnd = $dayStart + (60*60*24);

		if ($time >= $dayStart && $time < $dayEnd) {
			return true;
		}
		else {
			return false;
		}
	}
}