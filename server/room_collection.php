<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';

if ( empty( $_POST ) ) {
#	echo 'geen post';
	echo '0';
	return;
}

$keys  =  array(
	'server_rooms_ruimte' ,
	'server_rooms_waardes' ,
);
if ( !isset( $_POST[ 'server_rooms_waardes' ] , $_POST[ 'server_rooms_ruimte' ] ) ) {
#	echo 'mis data';
	echo '0';
	return;
}

/** /
$databse->query( <<<SQL
CREATE TABLE IF NOT EXISTS `iot_rooms` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `room_id` varchar(222) CHARACTER SET ascii NOT NULL,
  `readings` varchar(222) CHARACTER SET ascii NOT NULL,
  `change_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
);/**/

$server_rooms_ruimte  =  $database->real_escape_string( $_POST[ 'server_rooms_ruimte' ] );
$server_rooms_waardes  =  $database->real_escape_string( $_POST[ 'server_rooms_waardes' ] );
$raw  =  $database->real_escape_string( $_POST[ 'server_rooms_waardes' ] );

###
$row[ 'readings' ]  =& $server_rooms_waardes;
	$row[ 'readings' ]  =  explode( ' ' , $row[ 'readings' ] );
	asort( $row[ 'readings' ] );
	foreach ( $row[ 'readings' ] as $k => $v ) {
		if ( FALSE === strpos( $row[ 'readings' ][ $k ] , 'f-' ) )
			continue;
		unset( $row[ 'readings' ][ $k ] );
	}
	$row[ 'readings' ]  =  array_filter( $row[ 'readings' ] );
	$row[ 'readings' ]  =  implode( $row[ 'readings' ] , ' ' );
###
$result  =  $database->query(
	'INSERT INTO iot_rooms ( room_id , readings , change_time , raw ) VALUES ( \''
	. $server_rooms_ruimte . '\' , \'' . $server_rooms_waardes . '\' , NOW() , \'' . $raw . '\' ); '
);
echo TRUE === $result ? '1' : '0';

