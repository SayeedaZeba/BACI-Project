<?php  //this must be the very first line on the php page, to register this page to use session variables
  include "header.php";
	//if this is a page that requires login always perform this session verification
  require_once "inc/functions.php";
  $connection = dbConnect();

//Check to see if this is an admin viewing a user's page.
$adminViewing = false;
if($adminLoggedIn)
	$adminViewing = $_SESSION['admin']->isAtLeastCoordinator();
//If not, use session verify to kick the person out unless this is a user logged in.
if($adminViewing = false)
{
	$_SESSION['timeout'] = time();
	require_once "inc/sessionVerify.php";
}
?>

<!DOCTYPE html PUBLIC>
<html lang="EN" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>Homepage</title>

  <!-- Stylesheet link -->
  <link href="style.css" rel="stylesheet">

	</head>

	<body>


		<br/><br/>
		<div class = "container">
		<?php
		if(isset($_GET['email']))
		{	
			//If there's a get value for this user, retrieve their page.
			require_once("inc/user_page.php");
			printUserPage($connection, sanitize($_GET['email']));
		}
		//otherwise, issue a warning.
		else
			echo '<h1 class = "alert alert-danger">No user specified!</h1>';

		?>
		
		</div>
	</body>
</html>
