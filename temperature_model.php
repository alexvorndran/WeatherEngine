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
	
	class Temperature_Model {
		// Number of days in a year
		private $_daysPerYear;
		
		// Stores the current day temperature
		private $_currentTemperature;
		
		private $_multiplierYear;
		private $_seasonMean;
		
		private $_multiplierVariation;
		private $_variationMean;
		
		private $_timeInYear;    // time unit of the year
		private $_timeVariation;

		private $_variationFraction;
		
		// Determine if the model is for northern or southern hemisphere
		private $_northern;
		
		public function __construct($daysPerYear,$season_mean,$seasonRange,
				$variationMean,$variationRange,$variationFraction=10,$northern=TRUE) {
			$this->_daysPerYear = $daysPerYear;
			// parameters for the season cycles
			$this->_multiplierYear = $seasonRange/2;
			$this->_seasonMean = $season_mean;
			// parameters for the subcycles
			$this->_multiplierVariation = $variationRange/2;
			$this->_variationMean = $variationMean;
			$this->_variationFraction = $variationFraction;
			// random starting point
			$this->_timeInYear = mt_rand(-10,10);
			$this->_timeVariation = mt_rand(-10,10);
			// determine if the model is for the southern or the northern
			// hemisphere
			$this->_northern = $northern;
		}
		
		public function getNextTemperature() {
			$this->_timeInYear += 1;
			$this->_timeInYear = $this->_timeInYear % $this->_daysPerYear;
			$this->_timeVariation += $this->randFloat(-0.5, 1.0);
			// up and down of the temperature caused by the seasons
			// to model this a inverted cos-function is used
			$tempSeason = -$this->_multiplierYear*cos(2*pi()*$this->_timeInYear/$this->_daysPerYear);
			if(!$this->_northern) {
				// for the southern hemisphere the model uses a cos-function
				$tempSeason *= -1;
			}
			$tempSeason += $this->_seasonMean;
			// up and down of the temperature caused by randomness/weather
			$variationDuration = $this->_daysPerYear/$this->_variationFraction;
			if($this->_timeInYear % $this->_daysPerYear == 0) {
				// if a new year begins the subcycles may come faster or slower
				$variationDuration += mt_rand(-$this->_variationFraction/2, $this->_variationFraction/2);
			}
			// this is again modeled by a negative cos-function with
			// random sampling instead of adding noise afterwards
			$tempVariation = -$this->_multiplierVariation*cos(2*pi()*$this->_timeVariation/$variationDuration);
			if(!$this->_northern) {
				// for the southern hemisphere the model uses a cos-function again
				$tempVariation *= -1;
			} 
			$tempVariation += $this->_variationMean;
			// determine the actual temperature by adding both signals
			// save it as the current state
			$this->_currentTemperature = $tempSeason+$tempVariation;
			// return the calculated value
			return $this->_currentTemperature;
		}
		
		public function getCurrentTemperature() {
			// just return the current temperature
			return $this->_currentTemperature;
		}
		
		private function randFloat($min,$max,$mul = 1e7) {
			return mt_rand($min*$mul,$max*$mul)/$mul;
		}
	}
?>