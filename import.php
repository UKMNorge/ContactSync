<?php
header('Content-Type: text/html; charset=utf-8');

require_once('config.php');
require_once('functions.php');
require_once('UKM/sql.class.php');

foreach($OWNERS as $owner) {
	$contacts = file('data/csv/'.$owner.'.csv');
	$count	= -1;
	foreach($contacts as $contact_csv) {
		$count++;
		if($count > 0)
			echo '<h2>Contact '. $count .'</h2>';
		$contact = str_getcsv ( $contact_csv, ',');
	
		if($contact_csv == $contacts[0]) {
			$keys = map_keys($contact);
		} else {
			$contactinfos = new StdClass();
			$country = 47;
			// Add relevant info to object
			foreach( $keys as $key => $index ) {
				$value = $contact[$index];
				if( ( strpos($key, 'email1') === 0 ) && ( strpos($value, '@') == false ) ) {
					$value = '';
				} elseif( strpos($key, 'phone_') === 0 ) {
					// If contains country code
					if( strpos($value, '+') !== false) {
						$phone = explode(' ', $value);
						$country = (int) $phone[0];
						$value = (int) $phone[1];
					}
					$value = (int) $value;
				}
				$contactinfos->$key = $value;
			}
			// Clean email
			if( empty( $contactinfos->email1 ) && !empty( $contactinfos->email2) ) {
				$contactinfos->email1 = $contactinfos->email2;
				$contactinfos->email2 = '';
			}
			// Add country
			$contactinfos->country = $country;
			db_store($owner, $contactinfos);
		}
	}
}