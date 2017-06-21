<?php
/**
 * Source
 * User: Robbe Ingelbrecht
 * Date: 30/10/13 12:42
 * 
 * (C) Copyright Source 2013 - All rights reserved
 */

class PagesHelper extends Helper {

	public function formatCity(MeteoCity $city, $mode = '14d') {
		switch($mode) {
			case '14d':
				$this->_formatCityAs14d($city);
			break;

			case '48u':
				$this->_formatAs48h($city);
			break;
		}
	}

	var $counter = 0;
	private function _formatCityAs14d(MeteoCity $city) {
		$time = strtotime($city->getCurrentDate());
		?>
		<div class="weather-day">
			<div class="row">
				<div class="span1 w-icon">
					<div class="weather-day-icon">
						<img src="/img/icons/Weericonen/300px/<?=$city->symbooldag?>.gif" title="<?=$city->tekstsymbooldagNL?>" /><br />
					</div>
				</div>
				<div class="span8 w-title">
					<div class="weather-day-title">
						<span class="pull-left">
							<span style="font-size:11px"><?=$city->minimumtemperatuur?></span>
							<span style="font-size:11px;">/</span>
							<span style="color:#5f5f5f;font-size:11px;"><?=$city->maximumtemperatuur?></span>
							<span style="font-size:11px;">°C</span>
						</span>
						<?=Functions::translateDay(date("l", $time)).' '.substr($city->getCurrentDate(), 8).'/'.substr($city->getCurrentDate(), 5,2 );?>
					</div>
				</div>
				<div class="span3" style="padding-left: 20px;">
					<div class="weather-day-time" style="">
						<div class="header">
							Ochtend
							<img src="/img/icons/Weericonen/png/<?=$city->symboolochtend?>.png" width="32" title="<?=$city->tekstsymboolochtendNL?>" />
						</div>
						<table class="table table-condensed">
							<!--
							<tr>
								<td class="info-td">Max temp</td>
								<td><?=$city->maximumtemperatuur?>°C</td>
							</tr>
							<tr>
								<td class="info-td">Min temp</td>
								<td><?=$city->minimumtemperatuur?>°C</td>
							</tr>
							-->
							<tr>
								<td class="info-td">Wind</td>
								<td><?=$city->windrichtingochtendNL;?> / <?=$city->addOnePower($city->windkrachtrangeochtend);?>bft</td>
							</tr>
							<tr>
								<td class="info-td">Windstoot</td>
								<td>
									<?php if ($this->_doesWeatherComeFromLand($city->windrichtingochtendNL)): ?>
										Max <?=round($city->windstootochtend*1.15)?> km/u
									<?php else: ?>
										Max <?=round($city->windstootochtend*1.25)?> km/u
									<?php endif;?>
								</td>

							</tr>
							<tr>
								<td class="info-td">Golfhoogte</td>
								<td><?=$city->getWaveHeight($city->windrichtingochtendNL, $city->windkrachtrangeochtend);?>-<?=$city->getWaveHeight($city->windrichtingochtendNL, $city->addOnePower($city->windkrachtrangeochtend));?>m</td>
							</tr>
							<tr>
								<td></td>
								<td><br /></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="span3">
					<div class="weather-day-time">
						<div class="header">
							Middag
							<img src="/img/icons/Weericonen/png/<?=$city->symboolmiddag?>.png" width="32" title="<?=$city->tekstsymboolmiddagNL;?>" />
						</div>
						<table class="table table-condensed">
							<!--
							<tr>
								<td><?=$city->maximumtemperatuur;?>°C</td>
							</tr>
							<tr>
								<td><?=$city->minimumtemperatuur;?>°C</td>
							</tr>
							-->
							<tr>
								<td><?=$city->windrichtingmiddagNL;?> / <?=$city->addOnePower($city->windkrachtrangemiddag);?>bft</td>
							</tr>
							<tr>
								<td>
									<?php if ($this->_doesWeatherComeFromLand($city->windrichtingmiddagNL)): ?>
										Max <?=round($city->windstootmiddag*1.15)?> km/u
									<?php else: ?>
										Max <?=round($city->windstootmiddag*1.25)?> km/u
									<?php endif;?>
								</td>
							</tr>
							<tr>
								<td><?=$city->getWaveHeight($city->windrichtingmiddagNL, $city->windkrachtrangemiddag);?>-<?=$city->getWaveHeight($city->windrichtingmiddagNL, $city->addOnePower($city->windkrachtrangemiddag));?>m</td>
							</tr>
							<tr>
								<td><br /></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="span3">
					<div class="weather-day-time">
						<div class="header">
							Avond
							<img src="/img/icons/Weericonen/png/<?=$city->symboolavond?>.png" width="32" title="<?=$city->tekstsymboolavondNL?>" />
						</div>
						<table class="table table-condensed">
							<!--
							<tr>
								<td><?=$city->maximumtemperatuur;?>°C</td>
							</tr>
							<tr>
								<td><?=$city->minimumtemperatuur;?>°C</td>
							</tr>
							-->
							<tr>
								<td><?=$city->windrichtingavondNL;?> / <?=$city->addOnePower($city->windkrachtrangeavond);?>bft</td>
							</tr>
							<tr>
								<td>
									<?php if ($this->_doesWeatherComeFromLand($city->windrichtingavondNL)): ?>
										Max <?=round($city->windstootavond*1.15)?> km/u
									<?php else: ?>
										Max <?=round($city->windstootavond*1.25)?> km/u
									<?php endif;?>
								</td>
							</tr>
							<tr>
								<td><?=$city->getWaveHeight($city->windrichtingavondNL, $city->windkrachtrangeavond);?>-<?=$city->getWaveHeight($city->windrichtingavondNL, $city->addOnePower($city->windkrachtrangeavond));?>m</td>
							</tr>
							<tr>
								<td><br /></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="span3">
					<div class="weather-day-time">
						<div class="header" style="height: 22px">
							Extra
						</div>
						<table class="table table-condensed">
							<tr>
								<td class="info-td">Zonduur</td>
								<td><?=$city->zonduur;?></td>
							</tr>
							<tr>
								<td class="info-td">UV-index</td>
								<td>
									<?php
										if ($city->uvindex != '')
											echo $city->uvindex.' ('.Functions::getUvIndexStrengthAsString($city->uvindex).')';
										else
											echo 'N/A';	
									?>
								</td>
							</tr>
							<tr>
								<td class="info-td">Besch. fact</td>
								<td><?=Functions::getProtectionFactor($city->uvindex)?></td>
							</tr>
							<tr>
								<td class="info-td">Neersl.</td>
								<td><?=$city->neerslagkans;?>% / <?=number_format($city->neerslagsom, 0)?> l/m²</td>
							</tr>
						</table>
					</div>
				</div>

			</div>
		</div>

		<?php if ($this->counter++ == 0):?>
			<center>
				<div class="hidden-xs" style="height: 90px;width:600px; background: #DDEDFF; margin-bottom: 10px;font-size: 56px;color:#939393;line-height: 85px;">
					Banner 3 (eventueel)
				</div>
			</center>
		<?php endif;
	}

	var $last_day;
	var $first_parent = false;
	var $i;
	var $j;
	private function _formatAs48h(MeteoCity $city) {
		$day = strtotime($city->getCurrentDate());
		$day = date("d", $day);

		$score = $this->_calculateWeatherScore(array(
			'windrichting' => $city->windrichting,
			'windsterkte' => $city->addOnePower($city->windkracht)
		));
		?>
			<div class="weather-day">
				<div class="row">
					<div class="span1 w-icon">
						<div class="weather-day-icon">
							<img src="/img/icons/Weericonen/300px/<?=$city->symbool?>.gif" title="<?=$city->tekstsymbool?>" /><br />
							<?=$city->temperatuur;?>°C
						</div>
					</div>
					<div class="span8 w-title">
						<div class="pull-left cw-score cw-score-small" data-title="Kiteweer" data-content="<?=$score['calculations']?>"><?=$score['total']?></div>
						<div class="pull-left">&nbsp;&nbsp;</div>
						<div class="pull-left cw-score cw-score-small cw-score-surf" data-title="Surfweer" data-content="<?=$score['calculations']?>"><?=$score['total']?></div>
						<div class="weather-day-title">
							<?=Functions::translateDay(date("l", strtotime($city->getCurrentDate())))?> om
							<?=date("G:i", strtotime($city->getCurrentDate()))?>
						</div>
					</div>

					<div class="span2">
						<div class="weather-day-time">
							<div class="header">
								Wind
							</div>
							<div class="content">
								<p align="center">
									<img src="/img/icons/wd_icons/<?=$city->windrichting;?>.gif" width="16" /> <?=$city->windrichting;?> / <?=$city->addOnePower($city->windkracht);?>bft<br />
									<?php if ($this->_doesWeatherComeFromLand($city->windrichting)): ?>
											Max <?=round($city->windstoot*1.15)?>km/u
									<?php else: ?>
										Max <?=round($city->windstoot*1.25)?>km/u
									<?php endif;?>
								</p>
							</div>
						</div>
					</div>


					<div class="span4">
						<div class="weather-day-time">
							<div class="header">
								Weer
							</div>
							<div class="content">
								<?=$city->tekstsymbool;?><br />
								Golfhoogte: <em><?=$city->getWaveHeight($city->windrichting, $city->windkracht);?>-<?=$city->getWaveHeight($city->windrichting, $city->addOnePower($city->windkracht));?>m</em>
							</div>
						</div>
					</div>

					<div class="span3" style="width: 174px">
						<div class="weather-day-time">
							<div class="header">
								Neerslag
							</div>
							<div class="content">
								<?=$city->neerslagsom?>l/m²
								<br /><br />
							</div>
						</div>
					</div>
				</div>
			</div>

		<?php if ($this->counter++ == 1):?>
			<center>
				<div class="hidden-xs" style="height: 90px;width:600px; background: #DDEDFF; margin-bottom: 10px;font-size: 56px;color:#939393;line-height: 85px;">
					Banner 3 (eventueel)
				</div>
			</center>
		<?php endif;?>

		<?php
	}

	public function getIntroText($mode, $city) {
		switch($mode) {
			case '14d':
				echo 'Hieronder zie je het kite-weer voor de volgende 14 dagen op en aan zee, te '.$city->getCityName();
			break;

			case '48u':
				echo 'Hieronder zie je het kite-weer voor de volgende 48u op en aan zee, te '.$city->getCityName();
			break;
		}
	}

	public function getCurrentWeather($city) {
		if (!$city instanceof MeteoCity)
			return;

		$score = $this->_calculateWeatherScore(array(
			'windrichting' => $city->windrichting,
			'windsterkte' => $city->addOnePower($city->windkracht)
		));
		?>

		<!-- Kite Weather -->
		<div class="current-weather">
			<div class="span3">
				<div class="row">
					<div class="span3">
						<div class="cw-title">
							Het huidig weer in <?=$city->getCityName();?>
						</div>
					</div>
					<div class="span1">
						<div class="cw-icon">
							<img src="/img/icons/Weericonen/300px/<?=$city->symbool?>.gif" title="<?=$city->tekstsymbool?>" />
						</div>
					</div>
					<div class="span1">
						<div class="cw-temperature">
							<?=$city->temperatuur;?>°C
						</div>
					</div>
					<div class="span1">
						<div class="cw-score" data-content="<?=$score['calculations']?>">
							<?=$score['total'];?>
						</div>
					</div>
					<div class="span3">

					</div>
				</div>
			</div>
		</div>
		<?php
	}

	public function getCalculatorForm() {
		ob_start();
		?>
		<div class="calculator-form">
			<h3>Surf- en kiteweer calculator</h3>
			<div class="info">
				Optimaliseer het weercijfer <strong>voor jouw sport</strong>
				d.m.v. eigen cijfers
			</div>
			<div class="form">
				<div class="row">
					<div class="span3">
						<table>
							<tr>
								<!-- Windirection N(orth) -->
								<td>
									<span style="position: relative;top:-5px;padding-right: 10px;">N</span>
								</td>
								<td>
									<input type="text" class="span2" name="n" />
								</td>
								<td>
									<span style="position: relative;top:-5px;padding-left: 10px;">/5</span>
								</td>
							</tr>
							<!-- Windirection N(orth)O(ost) -->
							<tr>
								<td>
									<span style="position: relative;top:-5px;padding-right: 10px;">NO</span>
								</td>
								<td>
									<input type="text" class="span2" name="no" />
								</td>
								<td>
									<span style="position: relative;top:-5px;padding-left: 10px;">/5</span>
								</td>
							</tr>
							<!-- Windirection O(ost) -->
							<tr>
								<td>
									<span style="position: relative;top:-5px;padding-right: 10px;">O</span>
								</td>
								<td>
									<input type="text" class="span2" name="O" />
								</td>
								<td>
									<span style="position: relative;top:-5px;padding-left: 10px;">/5</span>
								</td>
							</tr>
							<!-- Winddirection Z(uid)O(ost) -->
							<tr>
								<td>
									<span style="position: relative;top:-5px;padding-right: 10px;">ZO</span>
								</td>
								<td>
									<input type="text" class="span2" name="zo" />
								</td>
								<td>
									<span style="position: relative;top:-5px;padding-left: 10px;">/5</span>
								</td>
							</tr>
						</table>
					</div>

					<div class="span3">
						<table>
							<tr>
								<!-- Winddirection Z(uid) -->
								<td>
									<span style="position: relative;top:-5px;padding-right: 10px;">Z</span>
								</td>
								<td>
									<input type="text" class="span2" name="z" />
								</td>
								<td>
									<span style="position: relative;top:-5px;padding-left: 10px;">/5</span>
								</td>
							</tr>
							<tr>
								<!-- Winddirection Z(uid)W(est) -->
								<td>
									<span style="position: relative;top:-5px;padding-right: 10px;">ZW</span>
								</td>
								<td>
									<input type="text" class="span2" name="zw" />
								</td>
								<td>
									<span style="position: relative;top:-5px;padding-left: 10px;">/5</span>
								</td>
							</tr>
							<tr>
								<!-- Winddirection W(est) -->
								<td>
									<span style="position: relative;top:-5px;padding-right: 10px;">W</span>
								</td>
								<td>
									<input type="text" class="span2" name="w" />
								</td>
								<td>
									<span style="position: relative;top:-5px;padding-left: 10px;">/5</span>
								</td>
							</tr>
							<tr>
								<!-- Winddirection N(oord)W(est) -->
								<td>
									<span style="position: relative;top:-5px;padding-right: 10px;">NW</span>
								</td>
								<td>
									<input type="text" class="span2" name="n" />
								</td>
								<td>
									<span style="position: relative;top:-5px;padding-left: 10px;">/5</span>
								</td>
							</tr>
						</table>
					</div>

					<div class="span3">
						<table>
							<tr>
								<td>fs</td>
							</tr>
						</table>
					</div>

					<div class="span9">
						<div class="form-actions">
							<input type="submit" class="btn btn-primary" value="Inzenden" />
						</div>
					</div>

				</div>
			</div>
		</div>
		<?php
		$ret = ob_get_contents(); ob_get_clean();

		return trim($ret);
	}

	private function _calculateWeatherScore($data) {
		$score = 0;

		// Indien de windsterkte in 2 getallen komt (bv. 4-5bft)
		// Gebruik dan de grootste (bv. 4-5bft -> 5
		if (!is_numeric($data['windsterkte'])) {
			$data['windsterkte'] = (int) substr($data['windsterkte'], 2, 1);
		}

		// Windriching doet er veel toe
		switch($data['windrichting']) {
			case 'N':
				$score += 5;
				break;

			case 'NO':
				$score += 3;
				break;

			case 'NW':
				$score += 4;
				break;

			case 'O':
				$score += 2;
				break;

			case 'W':
				$score += 4;
				break;

			case 'Z':
				$score += 1;
				break;

			case 'ZO':
				$score += 0;
				break;

			case 'ZW':
				$score += 3;
				break;
		}

		$score = array('windrichting' => $score);

		$score['windkracht'] = ($data['windsterkte']*0.5);
		$score['total'] = $score['windrichting'] + $score['windkracht'];

		$html = '<table class=\'table table-condense\'>';
			$html .= '<tr>';
				$html .= '<td>Windrichting&nbsp;('.$data['windrichting'].')&nbsp;&nbsp;&nbsp;</td><td>'.$score['windrichting'].'/5</td>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<td>Windkracht&nbsp;&nbsp;&nbsp;('.$data['windsterkte'].'bft)</td><td>'.$score['windkracht'].'/5</td>';
			$html .= '</tr>';

			$html .= '<tr>';
				$html .= '<td><strong>Totaal</strong></td><td><strong>'.$score['total'].'/10</strong></td>';
			$html .= '</tr>';
		$html .= '</table>';

		$score['calculations'] = trim(addslashes($html));
		return $score;
	}

	/**
	 * Check whether the weather comes from inland or from the sea
	 * Returns a boolean or null
	 *
	 * Used because multiply-calculations differ if the wind comes from inland
	 *
	 * @param $wdirection
	 * @return bool|null
	 */
	private function _doesWeatherComeFromLand($wdirection) {
		$ret = null;

		switch($wdirection) {
			case 'N':
				$ret = false;
				break;

			case 'NO':
				$ret = false;
				break;

			case 'O':
				$ret = false;
				break;

			case 'ZO':
				$ret = true;
				break;

			case 'Z':
				$ret = true;
				break;

			case 'ZW':
				$ret = true;
				break;

			case 'W':
				$ret = false;
				break;

			case 'NW':
				$ret = false;
				break;
		}

		return $ret;
	}
}