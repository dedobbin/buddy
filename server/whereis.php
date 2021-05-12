<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';

/*
CREATE TABLE IF NOT EXISTS `iot_locations` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `mac` varchar(222) CHARACTER SET ascii NOT NULL,
  `uid` varchar(222) CHARACTER SET ascii NOT NULL,
  `readings` varchar(222) CHARACTER SET ascii NOT NULL,
  `change_time` datetime DEFAULT NULL,
*/

if ( isset( $_GET[ 'clear' ] ) ) {
	$sql  =   'TRUNCATE TABLE `iot_locations`';
#	$sql  =   'DELETE FROM `iot_locations` WHERE readings = \'\'';
	$result  =  $database->query( $sql );
	if ( !$result ) {
		echo 'niet ';
	}
	echo 'geleegd.';
	return;
}

$isset_mac  =  isset( $_REQUEST[ 'clients_waar_is_mac' ] );
$isset_uid  =  isset( $_REQUEST[ 'clients_waar_is_uid' ] );
if ( $isset_uid ) {//$isset_mac || $isset_uid ) {
	$uid  =  $database->real_escape_string( $_REQUEST[ 'clients_waar_is_uid' ] );
	ob_start();
	require 'tmp_rooms/tmp4.php';
	$location  =  ob_get_contents();
	ob_end_clean();
	$location  =  $nearest[ 'room_id' ];
	$sql  =  'SELECT * FROM iot_sensors '
		. 'WHERE room_id=\'' . $nearest[ 'room_id' ] /*$location */. '\' '
		. 'ORDER BY change_time DESC '
		. 'LIMIT 1'
	;
	$result  =  $database->query( $sql );
	$result  =  $result->fetch_array( MYSQLI_ASSOC );
	$temp  =  $result[ 'temperature' ];// . '|' . $result[ 'humidity' ];
#echo '<pre>';var_dump($result);echo '</pre>';
#var_dump( $users[ 0 ][ 'change_time' ] );
	$present  =  !isset( $user_readings , $user_readings[ 0 ] )
		? false
		: ( $user_readings[ 0 ][ 't' ] > ( time() - ( 1 * 1 * 2 * 60 ) ) );
	$present  =  $present ? 'true' : 'false';
	$location  =  strtolower( $location ) . '' . $nearest[ 'point' ];
	echo implode( ' ' , array( $location , $temp , $present ) );
	return;
}

$sql  =  'SELECT * FROM'
	. ' ( SELECT * FROM `iot_locations`';
if ( $isset_mac ) {
	$value  =  $database->real_escape_string( $_REQUEST[ 'clients_waar_is_mac' ] );
	$sql .=  ' WHERE mac = \'' . $value . '\'';
}
else if ( $isset_uid ) {
	$value  =  $database->real_escape_string( $_REQUEST[ 'clients_waar_is_uid' ] );
	$sql .=  ' WHERE uid = \'' . $value . '\'';
}
$sql .=  ' ORDER BY change_time DESC ) AS c';
if ( isset( $_GET[ 'singles' ] ) ) {
	$sql .=  ' GROUP BY mac';
}

$result  =  $database->query( $sql );
while ( $row = $result->fetch_array( MYSQLI_ASSOC ) ) {
/** /
	$row[ 'readings' ]  =  explode( ' ' , $row[ 'readings' ] );
	$row[ 'readings' ]  =  array_filter( $row[ 'readings' ] );
	foreach ( $row[ 'readings' ] as $k => $v ) {
		if ( 14 > strlen( $row[ 'readings' ][ $k ] ) ) {
			unset( $row[ 'readings' ][ $k ] );
			continue;
		}
#		if ( FALSE !== strpos( $row[ 'readings' ][ $k ] , 'f-' ) ) {
#			unset( $row[ 'readings' ][ $k ] );
#			continue;
#		}
	}
	asort( $row[ 'readings' ] );
	$r=0;
	foreach ( $row[ 'readings' ] as $k => $v ) {
		$row[ 'readings' ][ $k ]  =  explode( '-' , $row[ 'readings' ][ $k ] );
		$r +=  $row[ 'readings' ][ $k ][1];
	}
	foreach ( $row[ 'readings' ] as $k => $v ) {
		$row[ 'readings' ][ $k ][]  =  round( ( $row[ 'readings' ][ $k ][1] / $r ) * 100 );
		$c  =  count( $row[ 'readings' ][ $k ] ) - 1;
		$row[ 'readings' ][ $k ][ $c ]  =  str_pad( $row[ 'readings' ][ $k ][ $c ] , 2 , '0' , STR_PAD_LEFT );
		$row[ 'readings' ][ $k ]  =  implode( '-' , $row[ 'readings' ][ $k ] );
	}

#echo count($row['readings'] ) . ', ';
	$row[ 'readings' ]  =  implode( '<br />' , $row[ 'readings' ] );
	$row[ 'readings' ]  =  preg_replace( '/-([0-9]{2})-([0-9]+)/' , ' <b>${1} ${2}%</b>' , $row[ 'readings' ] );
/**/
	$results[]  =  $row;
}

echo <<<HTM
<style>
td{vertical-align:top;}
b{float:right;}
</style>
<table border="1">
<thead>
<tr>
<th>
MAC adres
</th>
<th>
lezing
</th>
</tr>
</thead>
<tbody>

HTM;
$i  =  0;
while ( $row = @$results[$i++] )
{
echo <<<HTM
<tr>
<td>
{$row['mac']}
<br />
{$row['change_time']}
</td>
<td>
{$row['readings']}
</td>
</tr>
HTM;
}
echo <<<HTM
</tbody>
</table>

HTM;

