<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';
// tmp, stuff, db.inc, index || iamhere, room_collection, sensors, temps || climates, rooms, whereis
$sql  =  <<<SQL
select i
, room_id
, point
, reading
, change_time
, length(replace(str, ' ', ' .')) - length(str) as cntr
, substring_index(substring_index(str,' ',1),' ',-1) as Loc1
, substring_index(substring_index(str,' ',2),' ',-1) as Loc2
, substring_index(substring_index(str,' ',3),' ',-1) as Loc3
, substring_index(substring_index(str,' ',4),' ',-1) as Loc4
, substring_index(substring_index(str,' ',5),' ',-1) as Loc5
from (
	select NULL i
	, room_id
	, point
	, reading
	, change_time
	, replace(concat(mac,' '),'  ',' ') as str
	from iot_temp_rooms
) normalized
SQL;

$result  =  $database->query( $sql );
if ( !$result ) {
	echo '<pre>' . $database->error . '</pre>';
	die;
} else {
	$sql  =  'INSERT INTO iot_rooms ( room_id, point, reading, mac, strength, change_time ) VALUES ';
	$sql .=  "\n";
	while ( $row = $result->fetch_array( MYSQLI_ASSOC ) ) {
		$macs  =  $row;
		unset( $macs[ 'i' ] , $macs[ 'cntr' ] , $macs[ 'room_id' ] , $macs[ 'point' ] , $macs[ 'reading' ] , $macs[ 'change_time' ] );
#	var_dump( $row );
#	var_dump( $macs );
		foreach ( $macs as $mac ) {
			if ( '' === $mac ) continue;
			$mac  =  explode( '-' , $mac );
			$mac[ 0 ]  =  explode( ':' , $mac[ 0 ] );
			$mac[ 0 ]  =  implode( $mac[ 0 ] , '' );
			$mac[ 0 ]  =  strtoupper( $mac[ 0 ] );
			$values  =  array(
				$row[ 'room_id' ] ,
				$row[ 'point' ] ,
				$row[ 'reading' ] ,
				$mac[ 0 ] ,
				$mac[ 1 ] ,
				$row[ 'change_time' ] ,
			);
			$sql .= '( \'';
			$sql .= implode( $values , '\', \'' );
			$sql .= '\' ), ' . "\n";
		}
	}
#	echo $sql;
	$sql  =  substr( $sql , 0 , -3 ) . ';';
}
#echo '<pre>' . $sql . '</pre>';die( __FILE__ . ':' . __LINE__ );
$result  =  $database->query( $sql );
echo '<br />' . (!$result ? 'niet ' : '' ) . 'gelukt';

