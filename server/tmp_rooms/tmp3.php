<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
if (!$database) require 'init.inc.php';
$uid  =  $database->real_escape_string( $_REQUEST[ 'clients_waar_is_uid' ] );

$sql  =  'SELECT * FROM iot_room_means'
#. 'WHERE mac IN( \'' . implode( $user_readings , '\', \'' ) . '\' ) '
;
$result  =  $database->query( $sql );
$rooms  =  array();
while ( $row = $result->fetch_array( MYSQLI_ASSOC ) ) {
	$rooms
		[ $row[ 'room_id' ] ]
		[ $row[ 'point' ] ]
		[ $row[ 'mac' ] ]
		= 1*$row[ 'strength' ]
	;
}
$sql  =  'SELECT *,UNIX_TIMESTAMP(change_time) t FROM iot_locations '//get location of users
#. 'JOIN iot_sensors ON iot_locations.room_id=iot_sensors.room_id '//kannie wan'k heb hier (nog) geen room_id :s :/ :'(
. 'WHERE uid=\'' . $uid . '\' '//get one row for one user/uid
//. 'WHERE mac=\'5c:0a:5b:42:51:a4\' '
#. 'GROUP BY uid '//roll-up uid in resultset | fetch one row per user/uid
. 'ORDER BY change_time DESC '//only get most recent entry
. 'LIMIT 1 '
;
$result  =  $database->query( $sql );
while ( $user  =  $result->fetch_array( MYSQLI_ASSOC ) ) {
	$users[]  =  $user;
}
$rs  =  explode( ' ' , $users[ 0 ][ 'readings' ] );
$rs  =  array_filter( $rs );
foreach ( $rs as $k => $v ) {
	$t  = explode( '-' , $v );
	$mac  =  strtoupper( implode( '' , explode( ':' , $t[ 0 ] ) ) );
	$strength  =  $t[ 1 ];
	$user_readings[ $mac ]  =  1*$strength;
}

arsort( $user_readings );
$uks  =  array_keys( $user_readings );
$old_diff  =  array( 99 , 99 , 99 , 99 , 99 );
foreach ( $rooms as $room_id => $points ) {
	foreach ( $points as $point => $macs ) {
		arsort( $macs );
		$diff  =  array();
		foreach ( $user_readings as $m => $s ) {
			if ( !array_key_exists( $m , $macs ) ) continue;
			$diff[ $m ]  =  $macs[ $m ] - $s < 0 ? -1*($macs[$m]-$s):$macs[$m]-$s;
		}
		if ( array_sum( $diff ) < array_sum( $old_diff ) ) {
			$old_diff  =  $diff;
			$nearest  =  array( 'room_id' => $room_id , 'point' => $point );
		}
	}
}
echo $nearest[ 'room_id' ] . '-' . $point;

