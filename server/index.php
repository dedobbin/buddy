<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }

if ( 'localhost' === $_SERVER[ 'SERVER_NAME' ] ) {
	$base_uri  =  'http://localhost/iot/server/';
} else {
	$base_uri  =  'https://oege.ie.hva.nl/~frisw001/iot/';
}
$links  =  array(
// applicatie - server
	'server' => array(
		'rooms' => array(
			'text' => 'Dit zijn de waardes in ruimte' ,
			'form_uri' => $base_uri . 'room_collection.php' ,
			'form' => array(
//				'mac' => 'Gebruiker (MAC)' ,
				'ruimte' => 'Ruimte' ,
				'waardes' => 'Waardes met MAC-adressen en signaalsterktes (e.g.: 68:BD:AB:67:85:5F-79 68:BD:AB:67:85:50-50)' ,
			) ,
		) ,
	) ,
// applicatie - clients
	'clients' => array(
		'rooms' => array(
			'text' => 'Welke signaalsterkte metingen zijn er voor welke ruimtes?' ,
			'get_uri' => $base_uri . 'rooms.php' ,
			'get_uri_hr' => 'Lijst van signaalsterktemetingen in ruimtes' ,
			'truncate_uri' => $base_uri . 'rooms.php?clear' ,
		) ,
		'groep' => array(
			'text' => 'Waar zijn de mensen uit mijn groep?' ,
		) ,

		'klimaat' => array(
			'text' => 'Welke klimaatmetingen zijn er voor welke ruimtes?' ,
			'text' => 'Wat is hier de gevoelstemperatuur?' ,
			'get_uri' => $base_uri . 'climates.php' ,
			'get_uri_hr' => 'Lijst van klimaatmetingen in ruimtes' ,
			'truncate_uri' => $base_uri . 'climates.php?clear' ,
		) ,

		'waar_is' => array(
			'text' => 'Waar is' ,
			'form_uri' => $base_uri . 'whereis.php' ,
			'form' => array(
				'mac' => 'MAC-adres' ,
				'uid' => 'gebruikers-id' ,
			) ,
			'truncate_uri' => $base_uri . 'whereis.php?clear' ,
		) ,
		'ik_hier' => array(
			'text' => 'Ik ben hier' ,
			'form_uri' => $base_uri . 'iamhere.php' ,
			'form' => array(
				'mac' => 'MAC-adres' ,
				'uid' => 'user id' ,
				'locatie' => 'Locatie' ,
			) ,
		) ,
		'stemmen' => array(
			'text' => 'Ik wil dat het zoveel graden word' ,
			'form_uri' => $base_uri . 'temps.php' ,
			'form' => array(
				'mac' => 'MAC-adres' ,
				'temp' => 'Temperatuur' ,
			) ,
		) ,
	) ,
// sensor
	'sensoren' => array(
		'sensor' => array(
			'text' => 'Dit zijn de temperatuur en luchtvochtigheid' ,
			'form_uri' => $base_uri . 'sensors.php' ,
			'form' => array(
				'id' => 'identificerend nummer (voor tijdsbepaling)' ,
				'loc' => 'Ruimte' ,
				'hum' => 'Waarde met luchtvochtigheid' ,
				'temp' => 'Waarde met temperatuur' ,
			) ,
		) ,
	) ,
// airco
	'aircos' => array(
		'airco' => array(
			'text' => 'Jij wordt zoveel graden' ,
		) ,
	) ,
);

?>
<html>
	<head>
		<meta charset="utf-8" />
		<title>Testformulieren</title>
		<style type="text/css">
html { font-family:sans-serif; }
dt , dd { float:left; }
dt { clear:left; }
dt { width:80ex; }
dl:after { content:""; display:block; clear:left; }
label[for]:after { content:" [name=" attr(for) "]"; }
<?php if ( !isset( $_GET[ 'wf' ] ) ) { ?>
body {
width: 720px;
box-shadow: 0px 0px 10px black;
padding: 2em;	margin: auto;
background: none repeat scroll 0% 0% rgba(255, 255, 255, 0.94);
}
html{
background: url("http://www.hvatoneel.nl/templates/jsn_mico_pro/images/colors/grey/bg-master.png") repeat fixed 0% 0% transparent;
background: url("http://1-background.com/images/citrus/citrus-slices-seamless-background-image.jpg") repeat fixed 0% 0% transparent;
}
<?php } else { ?>
body{ background-color  :  #333; color  :  #CCC; }
<?php } ?>
		</style>
	</head>
	<body>
		<h1>Testformulieren</h1>
<ul>
<?php foreach ( $links as $object => $_links ) { ?>
	<li>
		<?php echo $object; ?>
		<ul>
<?php foreach ( $_links as $part => $link ) { $object_part = implode( array( $object , $part ) , '_' ); ?>
			<li>
				<a href="#<?php echo $object_part; ?>">
					<?php echo $link[ 'text' ]; ?>

				</a>
			</li>
<?php } ?>
		</ul>
	</li>
<?php } ?>
</ul>

<?php if(0){exit;} ?>
<?php if ( isset( $_GET[ 'wf' ] ) ) {
	foreach ( $links as $object => $__links ) {
		foreach ( $__links as $part => $_links ) {
			$_links[ 'form_uri' ]  =  (@$_links[ 'form_uri' ]
				? 'form: ' . $_links[ 'form_uri' ]
				: ''
			);
			$_links[ 'get_uri' ]   =  (@$_links[ 'get_uri' ]
				? 'get: ' . $_links[ 'get_uri' ]
				: ''
			);
			echo $_links[ 'form_uri' ] . $_links[ 'get_uri' ] . '<br />' ;
		}
	}
}
?>

<?php
foreach ( $links as $object => $__links ) {
	echo '<hr />';
	echo '<h2>' . $object . '</h2>' . "\n";
	foreach ( $__links as $part => $_links ) {
		$object_part  =  implode( array( $object , $part ) , '_' );
		echo '<h3 id="'
			. $object_part
			. '">' . $_links[ 'text' ] . '</h3>' . "\n"
		;
		if ( isset( $_links[ 'get_uri' ] ) ) {
			echo "\t" . '<p><a href="'
				. $_links[ 'get_uri' ] . '">'
				. ( $_links[ 'get_uri_hr' ] ? $_links[ 'get_uri_hr' ] : $_links[ 'get_uri' ] )
				. '</a></p>' . "\n";
		}
		if ( isset( $_links[ 'truncate_uri' ] ) ) {
			echo "\t" . '<p><a onclick="return confirm(\'u sure?\');" href="' . $_links[ 'truncate_uri' ] . '">Tabel leegmaken</a></p>' . "\n";
		}
		if ( !isset( $_links[ 'form' ] ) ) {
			echo "\t" . '<p>Formulier is nog niet geïmplementeeerd.</p>' . "\n";
		} else {
			echo ''
				. '<form method="POST"'
				. ( isset( $_links[ 'form_uri' ] ) ? ' action="' . $_links[ 'form_uri' ] . '"' : '' )
				. '>' . "\n"
				. '	<dl>' . "\n"
			;
			foreach ( $_links[ 'form' ] as $object_part_input_name => $object_part_input_pretty ) {
				$object_part_input_name  =  implode(
					array(
						  $object
						, $part
						, $object_part_input_name
					)
					, '_'
				)
				;
				echo "\t\t" . '<dt>' . "\n"
					. "\t\t" . '<label for="'
					. $object_part_input_name
					. '">' . "\n"
					. $object_part_input_pretty
					. "\t\t" . '</label>' . "\n"
					. "\t\t" . '</dt>' . "\n"
					. "\t\t" . '<dd>' . "\n"
					. "\t\t" . '<input name="'
					. $object_part_input_name
					. '" id="'
					. $object_part_input_name
					. '" />' . "\n"
					. "\t\t" . '</dd>' . "\n"
				;
			}
			echo "\t" . '</dl>' . "\n"
				. "\t" . '<input type="submit" />' . "\n"
				. '</form>' . "\n"
			;
		}
	}
}

