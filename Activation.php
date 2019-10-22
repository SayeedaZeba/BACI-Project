<?php  //this must be the very first line on the php page, to register this page to use session variables
  include "header.php";

  require_once "inc/functions.php";


?>

<!DOCTYPE html PUBLIC>
<html lang="EN" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>Activation</title>
  <!-- Bootstrap & Jquery scripts-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

  <!-- Stylesheet link -->
  <link href="style.css" rel="stylesheet">

	</head>

	<body>


		<br/><br/>

		<?php
			//--==Intialize Variables==--//
			$activationCode = "";
			$email = "";
			$validGET = false;
			
			//--==Retrieve values from GET==--//
			if(isset($_GET['code']))	$activationCode = sanitize($_GET['code']);
			if(isset($_GET['email']))	$email = sanitize($_GET['email']);
			
			//--==Validate Data==--//
			if(empty($activationCode))
				echo '<h3 style = "color: red">Error:  Activation code is missing!</h3>';
			else if(empty($email))
				echo '<h3 style = "color: red">Error:  User email is missing!</h3>';
			else
				$validGET = true;
				
			//----====Activation Method====----//
			if($validGET = true)
			{
				//--Database Connection--//
				$connection = dbConnect();
				if($connection === FALSE)
					echo '<h3 style = "color: red">Error connecting to database!</h1>';
				else
				{
					//Get the userdata from the email
					$procedure = 'call sp_get_userTable_data_by_email("'.$email.'");';
					$procedureCall = $connection->query($procedure);
					if(!$procedureCall)
						echo '<h3 style = "color: red">Error:  Could not retrieve user data!</h3>';
					else
					{
						//Get the user data
						$user = $procedureCall->fetch(PDO::FETCH_OBJ);
						$procedureCall->closeCursor();
						//Make sure we're not looking at a user that's already activated.
						if($user->Activated == 1)
							echo '<h3>User is already activated.</h3>';
						else
						{
							//If they're not activated, check if the activation codes in DB vs GET match
							if($activationCode != $user->ActivationCode)
								echo '<h3 style = "color: red">Error:  Activation Code does not match.</h3>';
							else
							{
								//If we're here, everything's peachy.  Proceed with activation.
								$procedure = 'call SP_Activate_User("' . $email . '");';
								$procedureCall = $connection->query($procedure);
								if($procedureCall === FALSE)
									echo '<h3 style = "color: red">Error:  Activation attempt failed!</h3>';
								else
									echo '<h3>Success!  User has been activated.</h3>';
							}
						}
					}
				}
			}
		?>

		</form>
		<br/><br/>
	</body>
</html>
