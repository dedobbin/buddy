<?php
require ''
	. dirname( ''
		. substr(
			$_SERVER[ 'SCRIPT_FILENAME' ]
			, 0
			, strrpos(
				dirname( $_SERVER[ 'SCRIPT_FILENAME' ] )
				, '/'
			)
		)
	)
	. DIRECTORY_SEPARATOR
	. 'db.inc.php'
;// quite some stuff because of symlinking.

