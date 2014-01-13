<?php
require_once('config.php');
require_once('functions.php');
require_once('UKM/sql.class.php');

$XML_start = '<?xml version="1.0" standalone="yes"?>
<directory>
	<item_list>';
$XML_stop .= '
	</item_list>
</directory>';


foreach($OWNERS as $owner) {
	$sql = new SQL("SELECT * 
					FROM `ukm_kontakter` 
					WHERE `owner` = '#owner'
					OR `owner` = 'system'",
					array('owner' => $owner));
	$res = $sql->run();
	while( $r = mysql_fetch_assoc( $res ) ) {
		$XML_data .= do_xml($r);
	}

	echo '<h2>Writing XML for '. $owner .'</h2>';	
	$file = fopen('data/xml/'.$owner.'.xml', 'w');
	fwrite($file, $XML_start . $XML_data . $XML_stop);
	fclose($file);
	echo 'Done';
}