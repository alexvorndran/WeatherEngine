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
	
	class Weather_Model {
		// Number of days in a year
		private $daysPerYear;
		
		// Stores the current day temperature
		private $currentTemperature;
		
		private $multiplierYear;
		private $seasonMean;
		
		private $multiplierVariation;
		private $variationMean;
		
		private $timeInYear;    // time unit of the year
		private $timeVariation;

		private $variationFraction;
		
		// Determine if the model is for northern or southern hemisphere
		private $northern;
		
		public function __construct($daysPerYear,$season_mean,$seasonRange,
				$variationMean,$variationRange,$variationFraction=10,$northern=TRUE) {
			$this->daysPerYear = $daysPerYear;
			// parameters for the season cycles
			$this->multiplierYear = $seasonRange/2;
			$this->seasonMean = $season_mean;
			// parameters for the subcycles
			$this->multiplierVariation = $variationRange/2;
			$this->variationMean = $variationMean;
			$this->variationFraction = $variationFraction;
			// random starting point
			$this->timeInYear = mt_rand(-10,10);
			$this->timeVariation = mt_rand(-10,10);
			// determine if the model is for the southern or the northern
			// hemisphere
			$this->northern = $northern;
		}
		
		public function nextTemperature() {
			$this->timeInYear += 1;
			$this->timeVariation += $this->rand_float(-0.5, 1.0);
			// up and down of the temperature caused by the seasons
			// to model this a inverted cos-function is used
			$tempSeason = -$this->multiplierYear*cos(2*pi()*$this->timeInYear/$this->daysPerYear);
			if(!$this->northern) {
				// for the southern hemisphere the model uses a cos-function
				$tempSeason *= -1;
			}
			$tempSeason += $this->seasonMean;
			// up and down of the temperature caused by randomness/weather
			$variationDuration = $this->daysPerYear/$this->variationFraction;
			if($this->timeInYear % $this->daysPerYear == 0) {
				// if a new year begins the subcycles may come faster or slower
				$variationDuration += mt_rand(-$this->variationFraction/2, $this->variationFraction/2);
			}
			// this is again modeled by a negative cos-function with
			// random sampling instead of adding noise afterwards
			$tempVariation = -$this->multiplierVariation*cos(2*pi()*$this->timeVariation/$variationDuration);
			if(!$this->northern) {
				// for the southern hemisphere the model uses a cos-function again
				$tempVariation *= -1;
			} 
			$tempVariation += $this->variationMean;
			// determine the actual temperature by adding both signals
			// save it as the current state
			$this->currentTemperature = $tempSeason+$tempVariation;
			// return the calculated value
			return $this->currentTemperature;
		}
		
		public function currentTemperature() {
			// just return the current temperature
			return $this->currentTemperature;
		}
		
		private function rand_float($min,$max,$mul = 1e7) {
			return mt_rand($min*$mul,$max*$mul)/$mul;
		}
	}
?>