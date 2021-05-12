<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';

if ( empty( $_POST ) ) {
#	echo 'geen post';
	echo '0';
	return;
}

$keys  =  array(
	'clients_stemmen_mac' ,
	'clients_stemmen_temp' ,
);
if ( !isset( $_POST[ 'clients_stemmen_mac' ] , $_POST[ 'clients_stemmen_temp' ] ) ) {
#	echo 'mis data';var_dump($_POST);echo '<br />';
	echo '0';
	return;
}

/** /
$database->query( <<<SQL
CREATE TABLE IF NOT EXISTS `iot_temps` (
  `id` int(3) unsigned NOT NULL AUTO_INCREMENT,
  `uid` varchar(222) CHARACTER SET ascii NOT NULL,
  `readings` varchar(222) CHARACTER SET ascii NOT NULL,
  `change_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
SQL
);/**/

$clients_stemmen_mac      =  $database->real_escape_string( $_POST[ 'clients_stemmen_mac'     ] );
$clients_stemmen_temp  =  $database->real_escape_string( $_POST[ 'clients_stemmen_temp' ] );

$result  =  $database->query(
	'INSERT INTO iot_temps ( uid , readings , change_time ) VALUES ( \''
	. $clients_stemmen_mac . '\' , \'' . $clients_stemmen_temp . '\' , NOW() ); '
);
echo TRUE === $result ? '1' : '0';
#if(!$result){echo '<pre>';var_dump($database->error);echo '</pre>';}

