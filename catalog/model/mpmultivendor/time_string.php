<?php
class ModelMpmultivendorTimeString extends Model {

	/*5 years 2 month*/
	public function getTimeString($datestring) {

		$this->load->language('mpmultivendor/time_string');

		$datestring = strtotime($datestring);
		$elapestime = time() - $datestring;

		if ($elapestime < 1) {
			return $this->language->get('text_0second');
		}
		$a = array( 
			365 * 24 * 60 * 60  =>  'year',
			30 * 24 * 60 * 60  =>  'month',
			24 * 60 * 60  =>  'day',
			60 * 60  =>  'hour',
			60  =>  'minute',
			1  =>  'second'
		);
		$a_plural = array( 
			'year'   => $this->language->get('text_year'),
			'month'  => $this->language->get('text_month'),
			'day'    => $this->language->get('text_day'),
			'hour'   => $this->language->get('text_hour'),
			'minute' => $this->language->get('text_minute'),
			'second' => $this->language->get('text_second')
		);

		foreach ($a as $secs => $str) {
			$d = $elapestime / $secs;
			if ($d >= 1) {
				$r = round($d);
				return $this->language->get('text_ago') . ' ' . $r . ' ' . ( $a_plural[$str] );
			}
		}
	}
}