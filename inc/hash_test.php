<!DOCTYPE HTML>
<?php
	require_once("functions.php");
	$password ="";
	$sanitized = "";
	if(isset($_POST['vPassword']))
	{
		$sanitized = sanitize($_POST['vPassword']);
		$password = passHash(sanitized);
	}
	//Test generate PIN
	echo generatePIN();
	//Test 
?>
<html>
	<head>
	</head>
	
	<body>
		<form action = "#" method = "post">
			<input name="vPassword" placeholder="Enter New Password" type="password" value="" size ="100"/>
			<?php echo '<p>' . $password . '</p>'; ?>
			<?php echo '<p>Sanitized Input:  '.$sanitized.'</p>';?>
		</form>
	</body>

</html>