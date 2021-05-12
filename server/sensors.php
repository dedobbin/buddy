<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';

if ( empty( $_POST ) ) {
#	echo 'geen post';
	echo '0';
	return;
}

$keys  =  array_keys(array(
	'sensoren_sensor_id' => 'identificerend nummer (voor tijdsbepaling)' ,
	'sensoren_sensor_loc' => 'Ruimte' ,
	'sensoren_sensor_hum' => 'Waarde met luchtvochtigheid' ,
	'sensoren_sensor_temp' => 'Waarde met temperatuur' ,
));
if ( !isset( $_POST[ 'sensoren_sensor_id' ] , $_POST[ 'sensoren_sensor_loc' ] , $_POST[ 'sensoren_sensor_hum' ] , $_POST[ 'sensoren_sensor_temp' ] ) ) {
#	echo 'mis data';
	echo '0';
	return;
}

/** /
$database->query( <<<SQL
CREATE TABLE IF NOT EXISTS `iot_sensors` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` varchar(222) CHARACTER SET ascii NOT NULL,
  `humidity` varchar(222) CHARACTER SET ascii NOT NULL,
  `temperature` varchar(222) CHARACTER SET ascii NOT NULL,
  `reading_id` varchar(222) CHARACTER SET ascii NOT NULL,
  `change_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
);/**/

$sensoren_sensor_id   = $database->real_escape_string( $_POST[ 'sensoren_sensor_id' ] );
$sensoren_sensor_loc  = $database->real_escape_string( $_POST[ 'sensoren_sensor_loc' ] );
$sensoren_sensor_hum  = $database->real_escape_string( $_POST[ 'sensoren_sensor_hum' ] );
$sensoren_sensor_temp = $database->real_escape_string( $_POST[ 'sensoren_sensor_temp' ] );

$result  =  $database->query(
	'INSERT INTO iot_sensors ( room_id , humidity , temperature , reading_id , change_time ) VALUES ( \''
	. $sensoren_sensor_loc . '\' , \'' . $sensoren_sensor_hum . '\' , \'' . $sensoren_sensor_temp
	. '\' '
	. ', \'' . $sensoren_sensor_id . '\''
#	. ', ( SELECT '
#		. 'IF(reading_id<=>\'\' OR reading_id<=>NULL,1,(reading_id+1)) AS rid '
#		. 'FROM `iot_sensors` AS `sens` ORDER BY reading_id DESC LIMIT 1 ) '
	. ', NOW() ); '
);

echo TRUE === $result ? '1' : '0';

#echo '<pre>' . $database->error . '</pre>';

