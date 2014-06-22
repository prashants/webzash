<?php
/**
 * The MIT License (MIT)
 *
 * Webzash - Easy to use web based double entry accounting software
 *
 * Copyright (c) 2014 Prashant Shah <pshah.mumbai@gmail.com>
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

/**
* Webzash Plugin Timezone Helper
*
* @package Webzash
* @subpackage Webzash.View
*/
class TimezoneHelper extends AppHelper {

	public $helpers = array('Form');

	function show() {
		$timezones = array(
			'Pacific/Apia' => 'Apia, Upolu, Samoa', // UTC-11:00
			'US/Hawaii' => 'Honolulu, Oahu, Hawaii, United States', // UTC-10:00
			'US/Alaska' => 'Anchorage, Alaska, United States', // UTC-09:00
			'US/Pacific' => 'Los Angeles, California, United States', // UTC-08:00
			'US/Mountain' => 'Phoenix, Arizona, United States', // UTC-07:00
			'US/Central' => 'Chicago, Illinois, United States', // UTC-06:00
			'US/Eastern' => 'New York City, United States', // UTC-05:00
			'America/Santiago' => 'Santiago, Chile', // UTC-04:00
			'America/Sao_Paulo' => 'São Paulo, Brazil', // UTC-03:00
			'Atlantic/South_Georgia' => 'South Georgia, S. Sandwich Islands', // UTC-02:00
			'Atlantic/Cape_Verde' => 'Praia, Cape Verde', // UTC-01:00
			'Europe/London' => 'London, United Kingdom', // UTC+00:00
			'UTC' => 'Universal Coordinated Time (UTC)', // UTC+00:00
			'Europe/Paris' => 'Paris, France', // UTC+01:00
			'Africa/Cairo' => 'Cairo, Egypt', // UTC+02:00
			'Europe/Moscow' => 'Moscow, Russia', // UTC+03:00
			'Asia/Dubai' => 'Dubai, United Arab Emirates', // UTC+04:00
			'Asia/Karachi' => 'Karachi, Pakistan', // UTC+05:00
			'Asia/Dhaka' => 'Dhaka, Bangladesh', // UTC+06:00
			'Asia/Jakarta' => 'Jakarta, Indonesia', // UTC+07:00
			'Asia/Hong_Kong' => 'Hong Kong, China', // UTC+08:00
			'Asia/Tokyo' => 'Tokyo, Japan', // UTC+09:00
			'Australia/Sydney' => 'Sydney, Australia', // UTC+10:00
			'Pacific/Noumea' => 'Nouméa, New Caledonia, France', // UTC+11:00e)'
		);

		$dateTime = new DateTime('now');
		foreach($timezones as $zone => $name) {
			$zoneObject = new DateTimeZone($zone);
			$dateTime->setTimezone($zoneObject);
			$timezones[$zone] = $name . ' ' . $dateTime->format('[g:i A]');
		}

		return $timezones;
	}
}
