<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';

if ( empty( $_POST ) ) {
#	echo 'geen post';
	echo '0';
	return;
}

$keys  =  array(
	'clients_ik_hier_mac' ,
	'clients_ik_hier_locatie' ,
);
if ( !isset( $_POST[ 'clients_ik_hier_mac' ] , $_POST[ 'clients_ik_hier_locatie' ] ) ) {
#	echo 'mis data';
	echo '0';
	return;
}

/** /
$database->query( <<<SQL
CREATE TABLE IF NOT EXISTS `iot_locations` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `mac` varchar(222) CHARACTER SET ascii NOT NULL,
  `readings` varchar(222) CHARACTER SET ascii NOT NULL,
  `change_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
);/**/

$clients_ik_hier_mac      =  $database->real_escape_string( $_POST[ 'clients_ik_hier_mac'     ] );
$clients_ik_hier_uid      =  $database->real_escape_string( $_POST[ 'clients_ik_hier_uid'     ] ?: 'damm006' );
$clients_ik_hier_locatie  =  $database->real_escape_string( $_POST[ 'clients_ik_hier_locatie' ] );

$locaties  =  strtoupper( $clients_ik_hier_locatie );
#$metingen  =  array();
$locaties  =  explode( ' ' , $locaties );
while ( NULL !== ( $j = key( $locaties ) ) ) {
	$locaties[ $j ]  =  explode( '-' , $locaties[ $j ] );
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
#date_default_timezone_set( 'Europe/Amsterdam' );
$nu  =  '\'' . date( 'Y-m-d H:i:s' , time() ) . '\'';
$sql  =  implode( ''
	. '\' , ' . $nu . ' ) ,' . "\n" . ' ( \''
	. $clients_ik_hier_mac . '\' , \''
	. $clients_ik_hier_uid . '\' , \''
	, $locaties
);
$sql  =  ' ( \''
	. $clients_ik_hier_mac . '\' , \''
	. $clients_ik_hier_uid . '\' , \''
	. $sql
	. '\' , ' . $nu
	. ' ) ' . "\n" . '-- '
;
/** / ?><pre><?php /**/
$sql  =  ''
	. 'INSERT INTO iot_locations ' . "\n"
	. '( mac , uid , readings , strength , change_time ) ' . "\n"
	. 'VALUES ' . "\n"
	. $sql
;
$result  =  $database->query( $sql );
echo TRUE === $result ? '1' : '0';

