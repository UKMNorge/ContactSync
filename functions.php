<?php
// IMPORT
function db_store($owner, $contact) {
	$sqlDel = new SQLdel('ukm_kontakter', array('id_owa' => $contact->id_owa));
	$sqlDel->run();
		
	$sql = new SQLins('ukm_kontakter');
	$sql->add('owner', $owner);
	foreach( $contact as $field => $value)
		$sql->add($field, $value);
	$sql->run();
	echo $contact->first_name .' '. $contact->last_name .': Lagret!';
}

function map_keys($contact) {
	$keys = new StdClass();
	$keys->id_owa		= array_search('Id', $contact);
	$keys->first_name 	= array_search('GivenName', $contact);
	$keys->last_name 	= array_search('Surname', $contact);
	$keys->company	 	= array_search('CompanyName', $contact);
	$keys->email1 		= array_search('EmailAddress1', $contact);
	$keys->email2 		= array_search('EmailAddress2', $contact);
	$keys->email3 		= array_search('EmailAddress3', $contact);
	$keys->phone_mobile	= array_search('MobilePhone', $contact);
	$keys->phone_home	= array_search('HomePhone', $contact);
	$keys->phone_work	= array_search('BusinessPhone', $contact);
	$keys->phone_company= array_search('CompanyMainPhone', $contact);
	return $keys;
}

// EXPORT
function do_xml( $contact ) {
	global $PHONE_TYPES, $PHONE_LABELS,$PHONE_SUFFIX;
	
	foreach( $PHONE_TYPES as $type ) {
		if(empty( $contact['phone_'.$type] ))
			continue;
		foreach($PHONE_SUFFIX as $suffix) {
			$return .='
			<item>
				<lb>'.$PHONE_LABELS[$type].'</lb>
				<fn>'.$contact['first_name'].'</fn>
				<ln>'.$contact['last_name'].'</ln>
				<ct>'.$contact['phone_'.$type].$suffix.'</ct>
			</item>';
		}
	}
	return $return;
}