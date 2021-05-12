<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';

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

echo ''
	. '<style>body{background-color:#333;color:#CCC;font-family:sans-serif;font-family:monospace;}</style>'
;
foreach ( $readings as $room_id => $room_points ) {
	foreach ( $room_points as $room_point => $room_accesspoints ) {
		echo ''
			. 'Op punt '
			. $room_point
			. ' is '
			. ' de gemiddelde signaalsterkte'
			. '<br />'
		;
		foreach ( $room_accesspoints as $room_accesspoint => $room_accesspoints_strengths ) {
#			foreach ( $room_accesspoints_strengths as $room_accesspoint_reading => $accesspoint_strength ) {
#				echo $room_accesspoint . '-' . $accesspoint_strength . ' ';
#			}
			$amount_of_readings  =  count( $room_accesspoints_strengths );
			$sum_of_strengths  =  array_sum( $room_accesspoints_strengths );
			$mean  =  $sum_of_strengths / $amount_of_readings;
			$strength  =  round( $mean );
			echo ''
				. ' voor accesspoint '
				. $room_accesspoint
				. ' '
				. $strength
				. '<br />'
				. ' omdat dat de afgeronde uitkomst is van '
				. $sum_of_strengths
				. ' gedeeld door '
				. $amount_of_readings . ' '
				. '<br />'
				. ' en '
				. $sum_of_strengths
				. ' de uitkomst is van de optelling van de signaalsterktes; '
				. ( implode( $room_accesspoints_strengths , ' + ' ) )
				. ''
				. '<br />'
			;

		}
		echo '<hr />';
	}
}
echo ''
	. '<pre>'
;

echo ''
	. '</pre>'
;
