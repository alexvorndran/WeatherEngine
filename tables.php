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

	// according to http://www.aso-hamburg.de/de/pdf/aso_absolute_feuchte.pdf
	$saturationHumidity = array(
		-20 => 0.900, -19 => 0.990, -18 => 1.080, -17 => 1.180, -16 => 1.290,
		-15 => 1.405, -14 => 1.530, -13 => 1.670, -12 => 1.820, -11 => 1.980,
		-10 => 2.150, -9 => 2.340, -8 => 2.550, -7 => 2.770, -6 => 3.005, 
		-5 => 3.260, -4 => 3.530, -3 => 3.820, -2 => 4.140, -1 => 4.475,
		0 => 4.840, 1 => 5.205, 2 => 5.590, 3 => 5.985, 4 => 6.395, 5 => 6.825,
		6 => 7.280, 7 => 7.760, 8 => 8.270, 9 => 8.820, 10 => 9.400, 11 => 10.000,
		12 => 10.650, 13 => 11.350, 14 => 12.100, 15 => 12.850, 16 => 13.650,
		17 => 14.500, 18 => 15.400, 19 => 16.300, 20 => 17.300, 21 => 18.350,
		22 => 19.400, 23 => 20.550, 24 => 21.800, 25 => 23.050, 26 => 24.350,
		27 => 25.750, 28 => 27.200, 29 => 28.700, 30 => 30.350, 31 => 32.050,
		32 => 33.850, 33 => 35.700, 34 => 37.650, 35 => 39.600, 36 => 41.700,
		37 => 43.900, 38 => 46.200, 39 => 48.600, 40 => 51.150, 41 => 53.800,
		42 => 56.700, 43 => 59.600, 44 => 62.500, 45 => 65.400, 46 => 68.500,
		47 => 71.800, 48 => 75.300, 49 => 79.000, 50 => 83.000, 51 => 87.000,
		52 => 91.000, 53 => 95.200, 54 => 99.600, 55 => 104.300, 56 => 109.300,
		57 => 114.400, 58 => 119.600, 59 => 124.900, 60 => 130.200, 61 => 135.900,
		62 => 141.900, 63 => 148.100, 64 => 154.500, 65 => 161.100, 66 => 167.900,
		67 => 175.000, 68 => 182.400, 69 => 190.100
	);
	// could be approximated by f(T) = 7.508*exp(0.0477*T)-2 this equitation
	// was found using the cftool from MATLAB

?>
