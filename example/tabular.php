<?php

/* 
 * The MIT License
 *
 * Copyright 2014 Alexander Vorndran <alex.vorndran@googlemail.com>.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

	require_once '../temperature_model.php';
	require_once '../rainfall_model.php';

	$years = 5;
	$daysPerYear = 365;
	$daysToSimulate = $years*$daysPerYear;

	$temperatureModel = new Temperature_Model($daysPerYear, 16, 28, -1.5, 10, 10, FALSE);
	$rainfallModel = new Rainfall_Model("flatlands", $daysPerYear, 15, FALSE);
	
	$daysWithRain = 0;
	$amountOfRain = 0;
	echo '<html><body>';
	for ($i = 0; $i < $daysToSimulate; $i++) {
		$temperature = $temperatureModel->getNextTemperature();
		$rainfall = $rainfallModel->getNextRainfall($temperature);
		echo $temperature.'; '.$rainfall.';<br/>';
		if($rainfall > 0) {
			$daysWithRain++;
			$amountOfRain += $rainfall;
		}
	}
	echo "It rained at ".$daysWithRain." days of ".$daysToSimulate." days.<br/>";
	echo "There was a total amount of ".$amountOfRain." units rain in that time.<br/>";
	echo '</html></body>';

?>
