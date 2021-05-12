<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';

/** /
$sql  =  <<<SQL
SELECT * FROM iot_rooms
SQL;
$result  =  $database->query( $sql );

$results  =  array();
$readings  =  array();
while ( $row = $result->fetch_array( MYSQLI_ASSOC ) ) {
	$results[]  =  $row;
	if ( !isset( $readings[ $row[ 'room_id' ] ] ) )
		$readings[ $row[ 'room_id' ] ]  =  array();
	if ( !isset( $readings[ $row[ 'room_id' ] ][ $row[ 'point' ] ] ) )
		$readings[ $row[ 'room_id' ] ][ $row[ 'point' ] ]  =  array();
	if ( !isset( $readings[ $row[ 'room_id' ] ][ $row[ 'point' ] ][ $row[ 'mac' ] ] ) )
		$readings[ $row[ 'room_id' ] ][ $row[ 'point' ] ][ $row[ 'mac' ] ]  =  array();
	if ( isset( $readings[ $row[ 'room_id' ] ][ $row[ 'point' ] ][ $row[ 'mac' ] ][ $row[ 'reading' ] ] ) )
		throw new Exception( 'double reading, imposibro' );
	$readings[ $row[ 'room_id' ] ][ $row[ 'point' ] ][ $row[ 'mac' ] ][ $row[ 'reading' ] ]  =  $row[ 'strength' ];
}

$inserts  =  array();
foreach ( $readings as $room_id => $room_points ) {
	foreach ( $room_points as $room_point => $room_accesspoints ) {
		foreach ( $room_accesspoints as $room_accesspoint => $room_accesspoints_strengths ) {
#			foreach ( $room_accesspoints_strengths as $room_accesspoint_reading => $accesspoint_strength ) {
#				echo $room_accesspoint . '-' . $accesspoint_strength . ' ';
#			}
			$amount_of_readings  =  count( $room_accesspoints_strengths );
			$sum_of_strengths  =  array_sum( $room_accesspoints_strengths );
			$mean  =  $sum_of_strengths / $amount_of_readings;
			$strength  =  round( $mean );

			$inserts[] = '( \'' . implode( array(
				$room_id
				, $room_point
				, $room_accesspoint
				, $strength
			) , '\' , \'' ) . '\' )';
		}
	}
}
/**/
$sql  =  'SELECT * FROM iot_room_means'
#. 'WHERE mac IN( \'' . implode( $user_readings , '\', \'' ) . '\' ) '
;
/** /
# Alleen meenemen wat in alle punten voor de ruimte gemeten wordt?
# Daarvan alleen de drie sterkste?
$sql  =  ''
	. 'INSERT INTO iot_room_means'
	. "\n"
	. '( room_id , point , mac , strength ) VALUES'
	. "\n"
	. implode( $inserts , ",\n" )
;
/**/
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
$sql  =  'SELECT * FROM iot_locations '//get location of users
#. 'WHERE uid=\'' . $uid . '\' '//get one row for one user/uid
. 'WHERE mac=\'5c:0a:5b:42:51:a4\' '
. 'GROUP BY uid '//roll-up uid in resultset | fetch one row per user/uid
. 'ORDER BY change_time DESC'//only get most recent entry
;
$result  =  $database->query( $sql );
while ( $user  =  $result->fetch_array( MYSQLI_ASSOC ) )
{
	$users[]  =  $user;
}
#var_dump($users);
$rs  =  explode( ' ' , $users[ 0 ][ 'readings' ] );
$rs  =  array_filter( $rs );
foreach ( $rs as $k => $v ) {
	$t  = explode( '-' , $v );
	$mac  =  strtoupper( implode( '' , explode( ':' , $t[ 0 ] ) ) );
	$strength  =  $t[ 1 ];
	$user_readings[ $mac ]  =  1*$strength;
}

echo ''
	. '<style>body{background-color:#333;color:#CCC;font-family:sans-serif;font-family:monospace;}</style>'
;
#array_unshift( $rooms , array( 'USER' => $user_readings ) );
arsort( $user_readings );
$uks  =  array_keys( $user_readings );
echo 'user';
echo '<br />';
foreach ( $user_readings as $mac => $strength ) {
	echo $mac . ' ' . $strength;
	echo '<br />';
}
echo '<hr />';
$old_diff  =  array( 99 , 99 , 99 , 99 , 99 );
foreach ( $rooms as $room_id => $points ) {
	echo $room_id;
	echo '<br />';
	foreach ( $points as $point => $macs ) {
		arsort( $macs );
		echo $point;
		echo '<br />';
#		var_dump(array_intersect_key($macs,$user_readings));
		$diff  =  array();
		foreach ( $user_readings as $m => $s ) {
			if ( !array_key_exists( $m , $macs ) ) continue;
			$diff[ $m ]  =  $macs[ $m ] - $s < 0 ? -1*($macs[$m]-$s):$macs[$m]-$s;
			echo '<!--'
				. $mac
				. ' u: ' . $s
				. ' ap: ' . $macs[ $m ]
				. ' diff: ' . $diff[ $m ]
				. '<br />'
				. '-->'
			;
		}
		if ( array_sum( $diff ) < array_sum( $old_diff ) ) {
			$old_diff  =  $diff;
			$nearest  =  array( 'room_id' => $room_id , 'point' => $point );
		}
		$i  =  0;
		foreach ( $macs as $mac => $strength ) {
#			if ( ++$i > 3 ) break;
			echo $mac . ' ' . $strength;
			echo '<br />';
		}
	}
	echo '<hr />';
	echo "\n";
}
#echo '' . '<pre>';
#var_dump( $rooms[ $nearest['room_id'] ][ $nearest['point'] ] );
#echo '' . '</pre>';
echo '<h1>' . $nearest[ 'room_id' ] . '</h1>';

