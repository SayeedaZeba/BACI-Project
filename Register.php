<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>User Registration</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>


		<?php
		include "header.php";
		$editMode = false;
		if(isset($_GET['editMode']))
			if($_GET['editMode'] == '1')
				$editMode = true;

		if($editMode == false)
			require_once("inc/RegisterLogic.php");
		else
		{
			//If we're in edit mode and an admin isn't logged in, verify the session.
			$_SESSION['timeout'] = time();
			if(!adminLoggedIn())
				require_once("inc/sessionVerify.php");
			if(adminLoggedIn() and !$_SESSION['admin']->isAtLeastAdmin())
				require_once("inc/sessionVerify.php");
			require_once("inc/editLogic.php");
		}

		?>
		<script>
		function showHint(str, state) {
				if (str.length == 0) {
						document.getElementById("txtHint").innerHTML = "";
						return;
				} else {
						var xmlhttp = new XMLHttpRequest();
						xmlhttp.onreadystatechange = function() {
								if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
										document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
								}
						}
						xmlhttp.open("GET", "inc/stateSelectorAjax.php?q=" + str +"&state=" + state, true);
						xmlhttp.send();
				}
		}
		<?php 
		//If the state and country are filled out, load them.
		
		if(!empty($country) and !empty($state))
			echo "document.onload = showHint(". $country . "," . $state .");";
		
		?>
		</script>

	</head>
	<body>
<div id="main">


	<div class="container containerinput" style="margin-top:15px">
        <!-- Feedback Form Starts Here -->
		<?php
		foreach($feedback as $response)
			echo $response;

		echo '<!--Registration Form Starts Here-->';

		//If the user hasn't yet been created, or we're in adminMode, load the registration form.
		if(!$userCreated or $adminMode)
		{
			if($editMode == false)
				require_once("inc/registerForm.php");
			else
			{
				require_once("inc/editForm.php");
			}
		}
		?>

    </div>
</div>


  </body>
</html>
