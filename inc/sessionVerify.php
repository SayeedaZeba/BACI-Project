<?php

	//if a session email isn't set automatically logs user out
	if (!isset($_SESSION['uid'])) Header ("Location:UserLogOut.php") ;

	//session time out 60 minutes after login. The timeout variable is set in the login page
	if(!isset($_SESSION['timeout']))  Header ("Location:UserLogOut.php") ;
  	else
		if ($_SESSION['timeout'] + 1 * 60 < time())
				 Header ("Location:Lab5LogOut.php") ;
		else 	$_SESSION['timeout'] = time();

?>
