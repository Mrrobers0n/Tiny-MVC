<?php
/**
 * Source
 * User: Robbe Ingelbrecht
 * Date: 29/10/13 10:46
 * 
 * (C) Copyright Source 2013 - All rights reserved
 */

class Functions {
	public static function translateDay($day, $lan = 'nl', $timest = null, $dayonly = false) {
		$nl = array(
			'monday'    => 'maandag',
			'tuesday'   => 'dinsdag',
			'wednesday' => 'woensdag',
			'thursday'  => 'donderdag',
			'friday'    => 'vrijdag',
			'saturday'  => 'zaterdag',
			'sunday'    => 'zondag',
			'today'     => 'vandaag'
		);

		$fr = array(
			'monday'    => 'lundi',
			'tuesday'   => 'mardi',
			'wednesday' => 'mercredi',
			'thursday'  => 'jeudi',
			'friday'    => 'vendredi',
			'saturday'  => 'samedi',
			'sunday'    => 'dimanche',
			'today'     => 'aujourd\'hui'
		);

		$arr = null;

		if ($lan === 'nl') {
			$arr = $nl;
		}
		else if ($lan === 'fr') {
			$arr = $fr;
		}

		/*if (date('l') == $day) {
			$day = 'today';
		}*/

		if ($dayonly === false)
			return ucfirst(substr($arr[strtolower($day)],0, 3)).' '.date("d/m", $timest);
		else
			return $arr[strtolower($day)];
	}

	public static function getUvIndexStrengthAsString($uvindex, $coloronly = false) {
		$ret = null;

		if ($uvindex >= 0 && $uvindex <= 4) {
			if ($coloronly === true)
				$ret = 'background:green;';
			else
				$ret = '<span style="color:green;">zwak</span>';
		}
		else if($uvindex >= 5 && $uvindex <= 6) {
			if ($coloronly === true)
				$ret = 'background:orange;';
			else
				$ret = '<span style="color:orange">matig</span>';
		}
		else if($uvindex >= 7 && $uvindex <= 10) {
			if ($coloronly === true)
				$ret = 'background:red';
			else
				$ret = '<span style="color:red">sterk</span>';
		}
		else {
			if ($coloronly === true)
				$ret = 'background: #ff0069';
			else
				return '<span style="color:#ff0069;">extreem</span>';
		}

		if ($uvindex == null || $uvindex == "")
			$ret = '';

		return $ret;
	}

	public static function getProtectionFactor($uvindex) {
		if ($uvindex >= 0 && $uvindex <= 4) {
			return '6-10';
		}
		else if($uvindex >= 5 && $uvindex <= 6) {
			return '15-25';
		}
		else if($uvindex >= 7 && $uvindex <= 10) {
			return '30-50';
		}
		else {
			return '50+';
		}
	}

	public static  function toCharArray($str) {
		$length = strlen($str);
		$ret = array();

		for($i=0;$i<$length;$i++) {
			$ret[] = substr($str, $i, 1);
			echo "added ".substr($str, $i, 1);
		}

		return $ret;
	}

	public static function getActivePage($controller, $page, $action = null) {
		$ret = '';

		if ($page == 'contact') {
			if ($controller == 'Pages' && $action == 'contact')
				return ' class="active"';
		}

		if ($page == 'home') {
			if ($controller == 'Pages' && $action == 'index2')
				return ' class="active"';
		}

		if ($page == 'brands') {
			if ($controller == 'Brands')
				return ' class="active"';
		}

		if ($page == 'promoties') {
			if ($controller == 'Store' && $action == 'promoties')
				return ' class="active"';
		}

		if ($page == 'collecties') {
			if ($controller == 'Store' && $action == 'collecties')
				return ' class="active"';
		}

		return $ret;
	}
}