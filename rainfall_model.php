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

	class Rainfall_Model {
		
		private $MINIMAL_EVAPORATION_TEMPERATURE = -5;
		
		private $_daysPerYear;
		private $_timeInYear;
		
		private $_northern;
		
		private $_waterTotal;
		private $_waterStored;
		private $_waterInTheAir;
		
		private $_biome;
		
		// emperic coefficients according to evaporation
		// http://www.hydroskript.de/html/_index.html?page=/html/hykp0505.html
		private $_a = 0.5;
		private $_J;
		
		
		// desert, mountain, coast, flatlands
		public function __construct($area,$daysPerYear,$meanTemperature,$northern,$timeInYear = 0) {
			// select a biome
			$this->_biome = $area;
			// check how much water is available in the selected biome
			$this->_waterStored = $this->calculateAvailableWater($area);
			$this->_waterTotal = $this->_waterStored;
			// set a fraction of the available water to be humidity
			$waterShift = mt_rand(-$this->_waterStored/5, 0);
			$this->_waterInTheAir = $waterShift;
			$this->_waterStored -= $waterShift;
			// days per year, e.g. for calculating a sunshine duration
			$this->_daysPerYear = $daysPerYear;
			// very raw approximation of the empiric constant
			$this->_J = pow($meanTemperature,1.514);
			$this->_northern = $northern;
			$this->_timeInYear = $timeInYear;
		}
		
		public function getNextRainfall($temperature) {
			// continue to the next day
			$this->_timeInYear += 1;
			$this->_timeInYear = $this->_timeInYear % $this->_daysPerYear;
			// calculate the expected evaporation given a temperature and a day
			$expectedEvaporation = $this->calculateEvaporation($this->_timeInYear, $temperature);
			$this->_waterStored = max(0,  $this->_waterStored-$expectedEvaporation);
			$this->_waterInTheAir += min($expectedEvaporation, $this->_waterStored);
			// calculate the risk of rain based on assumptions for each biome
			// and taking the amount of water that is present as humidity 
			// into account
			$waterRatio = $this->_waterInTheAir/($this->_waterStored+$this->_waterInTheAir);
			$rainProbalitity = $this->randomRainProbability()*(1+$waterRatio);
			if($this->randFloat(0, 1) < $rainProbalitity) {
				// let it rain
				$rainfall = $this->randFloat(0.05, 0.3)*(1+$waterRatio)*$this->_waterInTheAir;
				$this->_waterStored += $rainfall;
				$this->_waterInTheAir -= $rainfall;
			} else {
				$rainfall = 0;
			}
			$this->_waterStored += $this->_waterTotal-($this->_waterStored+$this->_waterInTheAir);
			return $rainfall;
		}
		
		private function randomRainProbability() {
			if($this->_biome == "desert") {
				$mean = 0.05;
				$randomness = $this->randFloat(-0.05, 0.05);
			} elseif($this->_biome == "mountain") {
				$mean = 0.10;
				$randomness = $this->randFloat( 0.00, 0.10);
			} elseif($this->_biome == "coast") {
				$mean = 0.20;
				$randomness = $this->randFloat(-0.05, 0.10);
			} elseif($this->_biome == "flatlands") {
				$mean = 0.15;
				$randomness = $this->randFloat(-0.10, 0.10);
			}
			
			return $mean+$randomness;
		}
		
		private function calculateAvailableWater($area) {
			// determine how much water is available in the given biome
			switch ($area) {
				case "desert":
					return 100;
				case "mountain":
					return 400;
				case "coast":
					return 600;
				case "flatlands":
					return 300;
				default:
					throw new Exception("Unsupported landscape!");
			}
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
				// scale to fit biomes characteristics a little bit better
				if($this->_biome == "flatlands" || $this->_biome == "mountains") {
					$evaporation = 0.95*$evaporation;
				} else {
					$evaporation = 1.1*$evaporation;
				}
			} else {
				$evaporation = 0;
			}
			
			return $evaporation;
		}
		
		private function randFloat($min,$max,$mul = 1e7) {
			return mt_rand($min*$mul,$max*$mul)/$mul;
		}
	}
?>