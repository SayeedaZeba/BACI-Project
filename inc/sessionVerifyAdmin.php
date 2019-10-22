<?php

	//if a session email isn't set automatically logs user out
	if (!isset($_SESSION['admin'])) Header ("Location:admin_logout.php") ;

	//session time out 60 minutes after login. The timeout variable is set in the login page
	if(!isset($_SESSION['timeout']))  Header ("Location:admin_logout.php") ;
  	else
		if ($_SESSION['timeout'] + 1 * 60 < time())
				 Header ("Location:admin_logout.php") ;
		else 	$_SESSION['timeout'] = time();

?>
