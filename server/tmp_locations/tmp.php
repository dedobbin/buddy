<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';

$sql  =  'SELECT * FROM iot_temp_locations';
$result  =  $database->query( $sql );
$results  =  array();
$sqls  =  '';
if ( !$result ) {
	var_dump( $database );
} else
while ( $row = $result->fetch_array( MYSQLI_ASSOC ) ) {
	$locaties  =  explode( ' ' , $row[ 'readings' ] );
	$locaties  =  array_filter( $locaties );
	if ( empty( $locaties ) ) continue;
	while ( NULL !== ( $j = key( $locaties ) ) ) {
		$locaties[ $j ]  =  explode( '-' , $locaties[ $j ] );
		$locaties[ $j ][ 0 ]  =  strtoupper( $locaties[ $j ][ 0 ] );
		$locaties[ $j ][ 0 ]  =  explode( ':' , $locaties[ $j ][ 0 ] );
		$locaties[ $j ][ 0 ]  =  implode( '' , $locaties[ $j ][ 0 ] );
#	$metingen[ $j ]  =  $locaties[ $j ][ 1 ];
		$locaties[ $j ]  =  $locaties[ $j ][ 0 ]
			. '\' , \''
			. $locaties[ $j ][ 1 ]
		;
#	$locaties[ $j ][ 0 ]  =  strtoupper( $locaties[ $j ][ 0 ] );
		next( $locaties );
	}
	$row[ 'mac' ]  =  strtoupper( $row[ 'mac' ] );
	$row[ 'mac' ]  =  explode( ':' , $row[ 'mac' ] );
	if ( 1 === count( $row[ 'mac' ] ) ) {
		$row[ 'mac' ][ 0 ]  =  strtolower( $row[ 'mac' ][ 0 ] );
	}
	$row[ 'mac' ]  =  implode( '' , $row[ 'mac' ] );
	$sql  =  implode( ''
		. '\' , \''
		. $row[ 'change_time' ]
		. '\' ) ,' . "\n" . ' ( \''
		. $row[ 'mac' ] . '\' , \''
		. $row[ 'uid' ] . '\' , \''
		, $locaties
	);
	$sql  =  ''
		. ' ( \''
		. $row[ 'mac' ] . '\' , \''
		. $row[ 'uid' ] . '\' , \''
		. $sql
		. '\' , \''
		. $row[ 'change_time' ]
		. '\' ) ,'
	;
	$sqls .= $sql . ' -- << ' . "\n";
}
$sqls  =  ''
	. 'INSERT INTO iot_locations ' . "\n"
	. '( mac , uid , readings , strength , change_time ) ' . "\n"
	. 'VALUES ' . "\n"
	. $sqls
;
$sqls  =  substr( $sqls , 0 , -10 );
$result  =  $database->query( $sqls );
echo TRUE === $result ? '1' : '0';
if ( !$result ) {
	echo '<pre>';
	var_dump( $database );
	echo( $sqls );
	echo '</pre>';
}

