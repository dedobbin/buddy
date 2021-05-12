<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';
/** /
INSERT INTO iot_rooms
SELECT NULL , SUBSTRING(  `room_id` , 1, 4 ) AS room, TRIM( TRAILING  ')'
FROM SUBSTRING(  `room_id` , 6, 1 ) ) AS POINT, CHAR_LENGTH( TRIM( TRAILING  ')'
FROM SUBSTRING(  `room_id` , 6, 6 ) ) ) AS reading, readings, 0, change_time
FROM  `iot_room_scans` -- WHERE `room_id` REGEXP '^E444\\([1-4]+\\)$'
/**/

if ( isset( $_GET[ 'clear' ] ) ) {
	$result  =  $database->query( <<<SQL
TRUNCATE TABLE `iot_rooms`
SQL
);
	if ( !$result ) {
		echo 'niet ';
	}
	echo 'geleegd.';
	return;
}
/** /
$database->query( <<<SQL
CREATE TABLE IF NOT EXISTS `iot_rooms` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` varchar(222) CHARACTER SET ascii NOT NULL,
  `readings` varchar(222) CHARACTER SET ascii NOT NULL,
  `change_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
);/**/

$_results  =  array();
$sql     =  'SELECT * FROM iot_rooms';
#$sql .=  ' WHERE room_id REGEXP \'^E444\\\\([1-4]+\\\\)$\'';
$result  =  $database->query( $sql );
if ( !$result ) {
	echo 'no result';
	var_dump( $database->error );
	die;
} else while ( $row = $result->fetch_array( MYSQLI_ASSOC ) ) {
	$row[ 'readings' ]  =  explode( ' ' , $row[ 'mac' ] );
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
	$room_readings[ $row[ 'room_id' ] ]  =  preg_replace( '/^(\S+)-([0-9]{2})-([0-9]+)/' , '${1}' , $row[ 'readings' ] );
	foreach ( $room_readings[ $row[ 'room_id' ] ] as $r ) {
		$r  =  '/' . $r . '/';
		if ( isset( $room_readings[ 'collection' ][ $r ] ) )
			$room_readings[ 'collection' ][ $r ]++;
		else
			$room_readings[ 'collection' ][ $r ]  =  1;
	}
	$_results[]  =  $row;
}

arsort( $room_readings[ 'collection' ] );
#$top3  =  array_slice( $room_readings[ 'collection' ] , 0 , 3 , TRUE );
#$top3  =  array_keys( $top3 );
#$r3=array('<em>${0}</em>','<em>${0}</em>','<em>${0}</em>',);
#$r3='<em style="color:red;">${0}</em>';
function kc ( $a , $b ) {
	global $room_readings;
$a  =  preg_replace( '/^(\S+)-([0-9]{2})-([0-9]+)/' , '${1}' , $a );
$b  =  preg_replace( '/^(\S+)-([0-9]{2})-([0-9]+)/' , '${1}' , $b );
$a  =  '/' . $a . '/';
$b  =  '/' . $b . '/';
	if ( $room_readings[ 'collection' ][ $a ] === $room_readings[ 'collection' ][ $b ] )
		return 0;
	if ( $room_readings[ 'collection' ][ $a ] < $room_readings[ 'collection' ][ $b ] )
		return 1;
	return -1;
}

$results  =  array();
$i=0;
while ( $row = @$_results[$i++] )
{
#echo count($row['readings'] ) . ', ';
	$row_copy                =  $row;
usort( $row_copy[ 'readings' ] , 'kc' );
$curr_top3  =  array_slice( $row_copy[ 'readings' ] , 0 , 3 );
$total=0;
foreach ( $curr_top3 as $r ) {
	$r  =  explode( '-' , $r );
	$total += $r[ 1 ];
}
foreach ( $curr_top3 as $k => $v ) {
	$r  =  explode( '-' , $v );
	$r[2] .=  ' NT:' . round( 100 * ( $r[ 1 ] / $total ) ) . '%';
#var_dump($r);
	$row_copy[ 'readings' ][ $k ]  =  implode( $r , '-' );
}

	$j=-1;while ( ++$j < 3 )
		if ( isset( $row_copy[ 'readings' ][ $j ] ) )
			$row_copy[ 'readings' ][ $j ]  =  preg_replace(
				'/^(.+)$/' , '<em style="color:red;">${1}</em>' , $row_copy[ 'readings' ][ $j ]
			);
		else
			echo '<!-- error -->';
	$row_copy[ 'readings' ]  =  implode( '<br />' , $row_copy[ 'readings' ] );
	$row_copy[ 'readings' ]  =  preg_replace( '/-([0-9]{2})-([0-9]+)/' , ' <b>${1} ${2}%</b>' , $row_copy[ 'readings' ] );
#var_dump( $row_copy[ 'readings' ] );
	$results[]  =  $row_copy;
}

echo <<<HTM
<table border="1">
<thead>
<tr>
<th>
kamer id
</th>
<th>
waardes
</th>
</tr>
</thead>
<tbody>

HTM;
$i=0;
while ( $row = @$results[$i++] )
{
echo <<<HTM
<tr>
<td>
{$row['room_id']}
{$row['point']}
{$row['reading']}
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

