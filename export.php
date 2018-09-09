<?php
header('Content-Type: text/html; charset=utf-8');
require_once('config.php');
require_once('functions.php');
require_once('UKM/sql.class.php');


// XML TEMPLATE
$XML_start = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<directory>
	<item_list>';
$XML_stop .= '
	</item_list>
</directory>';

// SYSTEM DATA
$sql = new SQL("SELECT * 
				FROM `ukm_kontakter` 
				WHERE `owner` = 'system'");
$res = $sql->run();
while( $r = SQL::fetch( $res ) ) {
	$XML_system_data .= do_xml($r);
}

// OWNERS DATA
foreach($OWNERS as $owner) {
	$sql = new SQL("SELECT * 
					FROM `ukm_kontakter` 
					WHERE `owner` = '#owner'",
					array('owner' => $owner));
	$res = $sql->run();
	while( $r = SQL::fetch( $res ) ) {
		$XML_data .= do_xml($r);
	}

	echo '<h2>Writing XML for '. $owner .'</h2>';	
	$file = fopen('data/xml/'.$owner.'_only.xml', 'w');
	fwrite($file, $XML_start . $XML_data . $XML_stop);
	fclose($file);
	echo 'Only Jardar-data: done<br />';

	$file = fopen('data/xml/'.$owner.'_with_system.xml', 'w');
	fwrite($file, $XML_start . $XML_system_data . $XML_stop);
	fclose($file);
	echo 'Jardar + system-data: done<br />';
}
