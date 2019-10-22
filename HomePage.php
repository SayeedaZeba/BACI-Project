<?php  //this must be the very first line on the php page, to register this page to use session variables
  include "header.php";
	//if this is a page that requires login always perform this session verification
  require_once "inc/functions.php";
  $connection = dbConnect();

$_SESSION['timeout'] = time();
  require_once "inc/sessionVerify.php";
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
		require_once("inc/user_page.php");
		printUserPage($connection, $_SESSION['uid']);
		
		require_once("inc/get_mentorships.php");
		if($retrievalSuccess == TRUE)
		{
			require_once("inc/display_mentorships.php");
		}
		?>
		<a class = "btn btn-warning" href="changePassword.php">Change Password</a>
		&nbsp
		<a class = "btn btn-danger" href="UserLogOut.php">Log Out</a>
		</div>
	</body>
</html>
