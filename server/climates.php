<?php if ( isset( $_GET[ 'src' ] ) ) { highlight_file( __FILE__ ); exit; }
require 'init.inc.php';

if ( isset( $_GET[ 'clear' ] ) ) {
	$result  =  $database->query( <<<SQL
TRUNCATE TABLE `iot_sensors`
SQL
	);
	if ( !$result ) {
		echo 'niet ';
	}
	echo 'geleegd.';
	return;
}
/** /
  `room_id` varchar(222) CHARACTER SET ascii NOT NULL,
  `humidity` varchar(222) CHARACTER SET ascii NOT NULL,
  `temperature` varchar(222) CHARACTER SET ascii NOT NULL,
  `reading_id` varchar(222) CHARACTER SET ascii NOT NULL,
  `change_time` datetime DEFAULT NULL,
/**/
$result  =  $database->query( 'SELECT * FROM iot_sensors' );

echo <<<HTM
<table border="1">
<thead>
<tr>
<th>
kamer id
</th>
<th>
luchtvochtigheid
</th>
<th>
temperatuur
</th>
<th>
meting id
</th>
<th>
opslagtijd
</th>
</tr>
</thead>
<tbody>

HTM;
while ( $row = $result->fetch_array(MYSQLI_ASSOC) )
{
echo <<<HTM
<tr>
<td>
{$row['room_id']}
</td>
<td>
{$row['humidity']}
</td>
<td>
{$row['temperature']}
</td>
<td>
{$row['reading_id']}
</td>
<td>
{$row['change_time']}
</td>
</tr>
HTM;
}
echo <<<HTM
</tbody>
</table>

HTM;

