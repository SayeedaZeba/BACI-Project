<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>My Template</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
<?php include "header.php"; ?>

		<div id = "main">
			<?php

			//calls dependencies
			require_once "inc/functions.php";
			require_once "inc/RegistrationFormPHP.php";

			if(isset($_REQUEST['submit']) and $validate and $eok)
			{
				//Get the values from POST
				$email=sanitize($_POST['vEmail']);
				$resetpin = sanitize($_POST['resetpin']);
				$pass = sanitize($_POST['pass']);
				$conpass = sanitize($_POST['conpass']);
				$feedback = array();

				// Check Password sufficient strength
				$passcheck=passwordCheck($pass,$conpass,8,$feedback);

				if($passcheck==TRUE)
				{
					// check password and confirm password matching
					$pass = passHash($_POST['pass']);
					$conpass = passHash($_POST['conpass']);
					if($pass==$conpass)
					{
						//From here, we need a database connection to confirm things.
						$connection = dbConnect();

						//Check if this email/resetPIN combination exists within the database.
						$procedure = 'call sp_count_users_by_email_resetPIN("' . $email . '","' . $resetpin . '")';
						$procedureCall = $connection->query($procedure);
						//If the procedure call fails, return a generic query error.
						if($procedureCall)
						{
							$result = $procedureCall->fetch(PDO::FETCH_OBJ);
							$count = $result->c;
							$procedureCall->closeCursor();
							if($count == 1)
							{
								$sql="UPDATE USER set Password=? where Email=?"; // Update password into table
								$stmt= $connection->prepare($sql);
								$stmt->execute(array($pass, $email));
								
								$message = "<p>Password reset successful.</p>";
								//After the passwordh as been reset, set the reset PIN to NULL,
								//De-activating the reset PIN.
		
								$sql="UPDATE USER set ResetPIN= NULL where Email=?";
								$stmt= $connection->prepare($sql);
								$stmt->execute(array( $email));
							}
							else
							{
								$message = "<p>Reset PIN is incorrect.</p>";
							}
						}
						else
						{
							$message = "<p>Query Error.</p>";
						}
					}
					else
					{
						$message = "<p>Password and confirm password do not match</p>";
					}
				}
				else
				{
					$message = "<p>Password should contain at least 8 characters including both letters and numbers</p>";
				}

				
			}


			?>
			            <!-- Form for login Starts Here -->
			        <div class="container containerinput" style="margin-top:75px">
			          <div>
			          <!-- Heading Of The Form -->
			            <H3>Enter your email to reset password</H3>
			            <?php print $message;?>
			              <form action="#" id="form" method="post" name="form">
			                <!-- Form contains a field for login and password -->
			              <?php print $emailBlank . $validateBlank;?><input name="vEmail" placeholder="Your Email" type="text" value="">
			              <input name="resetpin" placeholder="Enter Reset PIN" type="text" >
			              <input name="pass" placeholder="Enter New Password" type="password" >
			              <input name="conpass" placeholder="Enter Confirm Password" type="password" >
			              <br />
			              <input id="send" name="submit" type="submit" value="Submit">
			            </form>
			          </div>
			        </div>
		</div>


		<div>
			<a href="adminLogin.php">Admin Login</a>
		</div>

	</body>

</html>
