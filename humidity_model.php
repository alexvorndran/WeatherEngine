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
	class Humidity_Model {
		
		private $MINIMAL_EVAPORATION_TEMPERATURE = -5;
		
		private $_daysPerYear;
		private $_timeInYear;
		
		private $_northern;
		
		private $_waterAvailable;
		private $_waterInTheAir;
		
		private $_sunIntensity;
		private $_temperature;
		
		// emperic coefficients according to evaporation
		// http://www.hydroskript.de/html/_index.html?page=/html/hykp0505.html
		private $_a = 0.5;
		private $_J;
		
		
		// desert, mountain, coast, flatlands
		public function __construct($area,$daysPerYear,$meanTemperature,$northern,$timeInYear = 0) {
			$this->_daysPerYear = $daysPerYear;
			// very raw approximation of the empiric constant
			$this->_J = pow($meanTemperature,1.514);
			$this->_northern = $northern;
			$this->_timeInYear = $timeInYear;
		}
		
		public function getNextEvaporation($temperature) {
			$this->_timeInYear += 1;
			return $this->calculateEvaporation($this->_timeInYear, $temperature);
		}
		
		private function calculateEvaporation($day,$temperature) {
			// model the daily possible sunshine time between 8 and 16 hours
			$maximalSunshineTime = -4*cos(2*pi()*$day/$this->_daysPerYear);
			if(!$this->_northern) {
				$maximalSunshineTime *= -1;
			}
			$maximalSunshineTime += 12;
			
			if($temperature > $this->MINIMAL_EVAPORATION_TEMPERATURE) {
				$evaporation = 0.533*$maximalSunshineTime/12*
						pow((10*($temperature-$this->MINIMAL_EVAPORATION_TEMPERATURE))/$this->_J,  $this->_a);
			} else {
				$evaporation = 0;
			}
			
			return $evaporation;
		}
	}
?>