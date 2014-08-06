<?php

/* 
 * The MIT License
 *
 * Copyright 2014 Alex Vorndran <alex.vorndran@googlemail.com>.
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
	
	class WeatherModel {
		// Number of days in a year
		private $daysPerYear;
		
		// Stores the current temperature
		private $current_temperature;
		
		private $multiplier_year;
		private $season_mean;
		
		private $multiplier_variation;
		private $variation_mean;
		
		private $time_year;
		private $time_variation;

		private $variation_fraction;
		
		// Determine if the model is for northern or southern hemisphere
		private $northern;
		
		public function __construct($daysPerYear,$season_mean,$season_range,
				$variation_mean,$varition_range,$variation_fraction=10,$northern=TRUE) {
			$this->daysPerYear = $daysPerYear;
			// paramaters for the season cycles
			$this->multiplier_year = $season_range/2;
			$this->season_mean = $season_mean;
			// paramteres for the subcycles
			$this->multiplier_variation = $varition_range/2;
			$this->variation_mean = $variation_mean;
			$this->variation_fraction = $variation_fraction;
			// random starting point
			$this->time_year = mt_rand(-10,10);
			$this->time_variation = mt_rand(-10,10);
			// determine if the model is for the southern or the northern
			// hemisphere
			$this->northern = $northern;
		}
		
		public function next_temperature() {
			$this->time_year += 1;
			$this->time_variation += $this->rand_float(-0.5, 1.0);
			// up and down of the temperature caused by the seasons
			// to model this a inverted cos-function is used
			$temp_season = -$this->multiplier_year*cos(2*pi()*$this->time_year/$this->daysPerYear);
			if(!$this->northern) {
				// for the southern hemisphere the model uses a cos-function
				$temp_season *= -1;
			}
			$temp_season += $this->season_mean;
			// up and down of the temperature caused by randomness/weather
			$variation_duration = $this->daysPerYear/$this->variation_fraction;
			if($this->time_year % $this->daysPerYear == 0) {
				// if a new year begings the subcycles may come faster or slower
				$variation_duration += mt_rand(-$this->variation_fraction/2, $this->variation_fraction/2);
			}
			// this is again modeled by a negative cos-function with
			// random sampling instead of adding noise afterwards
			$temp_variation = -$this->multiplier_variation*cos(2*pi()*$this->time_variation/$variation_duration);
			if(!$this->northern) {
				// for the southern hemisphere the model uses a cos-function again
				$temp_variation *= -1;
			} 
			$temp_variation += $this->variation_mean;
			// determine the actual temperature by adding both signals
			// save it as the current state
			$this->current_temperature = $temp_season+$temp_variation;
			// return the calculated value
			return $this->current_temperature;
		}
		
		public function current_temperature() {
			// just return the current temperature
			return $this->current_temperature;
		}
		
		private function rand_float($min,$max,$mul = 1e7) {
			return mt_rand($min*$mul,$max*$mul)/$mul;
		}
	}
?>