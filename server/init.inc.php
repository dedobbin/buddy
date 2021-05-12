<?php
require ''
	. dirname(
		str_replace(
			basename( $_SERVER[ 'SCRIPT_FILENAME' ] )
			, ''
			, $_SERVER[ 'SCRIPT_FILENAME' ]
		)
	)
	. DIRECTORY_SEPARATOR
	. 'db.inc.php'
;// quite some stuff because of symlinking.

