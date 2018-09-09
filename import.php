<?php
header('Content-Type: text/html; charset=utf-8');

require_once('config.php');
require_once('functions.php');
require_once('UKM/sql.class.php');
require_once('UKM/monstring.class.php');

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


// IMPORT SYSTEM
$sql = new SQL("SELECT `c`.`id` AS `c_id`,
	   `c`.`firstname` AS `first_name`,
	   `c`.`lastname` AS `last_name`,
	   `c`.`picture`,
	   'UKM' AS `name_prefix`,
	   `pl`.`pl_name` AS `company`,
	   `c`.`tlf` AS `tel`,
	   `c`.`email` AS `email`,
	   `pl`.`pl_id` AS `pl_id`,
	   `pl`.`pl_fylke`,
	   `pl`.`pl_kommune`
				FROM `smartukm_contacts` AS `c`
				JOIN `smartukm_rel_pl_ab` AS `rel`
					ON (`rel`.`ab_id` = `c`.`id`)
				JOIN `smartukm_place` AS `pl`
					ON (`pl`.`pl_id` = `rel`.`pl_id`)
				LEFT JOIN `smartukm_rel_pl_k` AS `k_rel`
					ON (`k_rel`.`pl_id` = `pl`.`pl_id`)
				LEFT JOIN `smartukm_kommune` AS `k`
					ON (`k`.`id` = `k_rel`.`k_id`)
				WHERE `c`.`name` != ''
				GROUP BY `c`.`id`
				ORDER BY `c`.`name`
			");
$res = $sql->run();

while($r = SQL::fetch( $res )) {
	if(empty($r['last_name']))
		continue;	
	foreach($r as $key => $val)
		$r[$key] = utf8_encode($val);
	
	echo '<h2>System: '. $r['first_name'] .' '. $r['last_name'] .'</h2>'; 
	
	$r['company'] = 'UKM '. str_replace(array('UKM i','UKM','lokalmønstring'),'', $r['company']);	
	$r['tel'] = (int) str_replace(' ','', $r['tel']);
	
	$monstring = new monstring($r['pl_id']);	
	$link = $monstring->get('link');
			
	$contact = new StdClass;
	$contact->id_owa		= 'UKM_'. $r['c_id'];
	$contact->country 		= 47;
	$contact->first_name 	= $r['first_name'];
	$contact->last_name		= $r['last_name'];
	$contact->title			= $r['title'];
	$contact->company		= $r['company'];
	$contact->email1		= $r['email'];
	$contact->phone_mobile	= $r['tel'];
	$contact->website		= $link;
	db_store('system', $contact);
}
