<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
ini_set( 'xdebug.var_display_max_depth' , 6 );
require 'init.inc.php';

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
$sql  =  'SELECT * FROM'
	. ' ( SELECT * FROM `iot_locations`';
if ( isset( $_REQUEST[ 'clients_waar_is_mac' ] ) ) {
	$value  =  $database->real_escape_string( $_REQUEST[ 'clients_waar_is_mac' ] );
	$sql .=  ' WHERE mac = \'' . $value . '\'';
}
$sql .=  ' ORDER BY change_time DESC ) AS c';
if ( isset( $_GET[ 'singles' ] ) ) {
	$sql .=  ' GROUP BY mac';
}

$result  =  $database->query( $sql );

$results  =  array();
while ( $row = $result->fetch_array( MYSQLI_ASSOC ) ) {
	$row[ 'readings' ]  =  explode( ' ' , $row[ 'readings' ] );
	$row[ 'readings' ]  =  array_filter( $row[ 'readings' ] );
	foreach ( $row[ 'readings' ] as $k => $v ) {
		if ( 14 > strlen( $row[ 'readings' ][ $k ] ) ) {
			unset( $row[ 'readings' ][ $k ] );
			continue;
		}
		if ( FALSE !== strpos( $row[ 'readings' ][ $k ] , 'f-' ) ) {
			unset( $row[ 'readings' ][ $k ] );
			continue;
		}
	}
	asort( $row[ 'readings' ] );
	$total_signal_strengths  =  0;
	foreach ( $row[ 'readings' ] as $k => $v ) {
		$row[ 'readings' ][ $k ]  =  explode( '-' , $row[ 'readings' ][ $k ] );
		$total_signal_strengths +=  $row[ 'readings' ][ $k ][1];
	}
	$mac  =  $row[ 'mac' ];
	if ( !isset( $results[ $mac ][ 'times' ] ) ) {
		$results[ $mac ][ 'times' ]  =  array();
	}
	if ( !isset( $results[ $mac ][ 'points' ] ) )
		$results[ $mac ][ 'points' ]  =  array();
	foreach ( $row[ 'readings' ] as $k => $v ) {
		$row[ 'readings' ][ $k ][]  =  round( ( $row[ 'readings' ][ $k ][1] / $total_signal_strengths ) * 100 );
		$c  =  count( $row[ 'readings' ][ $k ] ) - 1;
		$ap  =  $row[ 'readings' ][ $k ][ 0 ];

		$change_time  =  preg_replace(
'/([0-9]{4})-([0-9]{2})-([0-9]{2}) ([0-9]{2}):([0-9]{2}):([0-9]{2})/' , '$4$5$6' , $row[ 'change_time' ]
		);
		$results[ $mac ][ 'times' ][ $change_time ][ $ap ]  =  array(
			  'percentual' => str_pad( $row[ 'readings' ][ $k ][ $c ] , 2 , '0' , STR_PAD_LEFT )
			, 'strength' => $row[ 'readings' ][ $k ][ 1 ]
		);

		$ap  =  str_replace( ':' , '' , $ap );
		if ( !isset( $results[ $mac ][ 'points' ][ $ap ] ) )
			$results[ $mac ][ 'points' ][ $ap ]  =  1;
		else
			$results[ $mac ][ 'points' ][ $ap ]++;
	}
	$results[ $mac ][ 'points' ]  =  ( $results[ $mac ][ 'points' ] );
}

echo <<<HTM
<style>
html{color:#CCC;background-color:#333;font-family:sans-serif;}
td{vertical-align:top;}
b{float:right;}
.ap{font-size:small;}
.stable{color:grey;}
.rise{color:green;}
.decline{color:red;}
</style>

HTM;
#var_dump($results);die;
echo <<<HTM
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
$percentual_style  =  $strength_style  =  'stable';
if ( !$results )
	echo '<tr><td colspan="2" style="text-align:center;">' . 'geen data' . '</td></tr>';
else foreach ( $results as $mac => $readings )
{
	echo ''
		. '<tr>' . "\n"
		. '<td>' . "\n"
		. $mac . "\n"
		. '</td>' . "\n"
		. '<td>' . "\n"
	;
	$points  =  array_keys( $readings[ 'points' ] );
	echo ''
		. '<table border="1"><thead><tr>'
		. '<th>AP/Tijd</th>'
		. '<th class="ap">' . implode( '</th><th class="ap">' , $points ) . '</th>'
	;
	$tbody  =  '';
	$prev_change_time  =  0;
	foreach ( $readings[ 'times' ] as $change_time => $ap_readings )
	{
		$tbody .=  ''
			. '<tr><th>'
			. $change_time
			. '</th>'
		;
		foreach ( $points as $ap ) {
			$ap  =  implode( ':' , str_split( $ap , 2 ) );
			if ( isset(
				$readings[ 'times' ][ $prev_change_time ][ $ap ]
				, $readings[ 'times' ][ $change_time ][ $ap ]
			) ) {
				$strength_style  =  $readings[ 'times' ][ $prev_change_time ][ $ap ][ 'strength' ]
					- $readings[ 'times' ][ $change_time ][ $ap ][ 'strength' ];
				$strength_style  =  (
					(
					0 === $strength_style
						? 'stable'
						: (
						0 > $strength_style
							? 'rise'
							: 'decline'
						)
					)
				);

				$percentual_style  =  $readings[ 'times' ][ $prev_change_time ][ $ap ][ 'percentual' ]
					- $readings[ 'times' ][ $change_time ][ $ap ][ 'percentual' ];
				$percentual_style  =  (
					(
					0 === $percentual_style
						? 'stable'
						: (
						0 > $percentual_style
							? 'rise'
							: 'decline'
						)
					)
				);
			}
			if ( isset( $ap_readings[ $ap ] ) ) {
				$tbody .=  ''
					. '<td>'
					. '<strong class="' . $strength_style . '">'
					. $ap_readings[ $ap ][ 'strength' ]
					. '</strong>'
					. ' | '
					. '<em class="' . $percentual_style . '">'
					. $ap_readings[ $ap ][ 'percentual' ]
					. '%'
					. '</em>'
					. '</td>'
				;
			} else {
				$tbody .=  ''
					. '<td>'
					. '&nbsp;'
					. '</td>'
				;
			}
		}
		$tbody .=  ''
			. '</tr>'
		;
		$prev_change_time  =  $change_time;
	}
	echo ''
		. '</tr></thead><tbody>'
		. $tbody
		. '</tbody></table>'
	;
	echo '</td>' . "\n" . '</tr>';
}
echo '</tbody>' . "\n" . '</table>' . "\n";

