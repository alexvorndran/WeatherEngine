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

	header("Content-type: image/png");
	require_once '../temperature_model.php';
	require_once '../rainfall_model.php';

	$years = 4;
	$daysPerYear = 360;
	$img = ImageCreate($years*$daysPerYear, 600); //erzeugen
	$backgroundColor = ImageColorAllocate($img, 0xaa, 0xaa, 0xaa); // Hintergrundfarbe
	$colBlack = ImageColorAllocate($img, 0x00, 0x00, 0x00); // Farbwert schwarz
	$colRed = ImageColorAllocate($img, 0xff, 0x00, 0x00); // und rot
	$colGreen = ImageColorAllocate($img, 0x00, 0xff, 0x00); // und gruen
	$colBlue = ImageColorAllocate($img, 0x00, 0x00, 0xff); // und blau

	$temperatureModel = new Temperature_Model($daysPerYear, 16, 28, -1.5, 10, 10, FALSE);
	$humidityModel = new Rainfall_Model("mountain", $daysPerYear, 15, FALSE);
	for ($i = 0; $i < $years*$daysPerYear; $i++) {
//		echo $model->next_temperature().'<br/>';
		$temperature = $temperatureModel->getNextTemperature();
		imagesetpixel($img, $i, 600-10*(20+$temperature), $colRed);
		$humidity = $humidityModel->getNextRainfall($temperature);
//		imageline($img, $i, 400, $i, 600-10*(20+$humidity), $colBlue);
		imageline($img, $i, 600, $i, 600-10*$humidity, $colBlue);
	}
	
	for($i = $daysPerYear; $i < $years*$daysPerYear; $i+=$daysPerYear) {
		imageline($img, $i, 0, $i, 600, $colBlack);
	}
	imageline($img, 0, 400, $years*$daysPerYear, 400, $colBlue);
	imageline($img, 0, 200, $years*$daysPerYear, 200, $colGreen);
	imageline($img, 0, 500, $years*$daysPerYear, 500, $colBlack);
	
	ImagePNG($img);
	imagedestroy($img);
?>
