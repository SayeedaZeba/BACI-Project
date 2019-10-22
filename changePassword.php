<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Change Password</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
<?php 
include "header.php";

//Verify the session before continuing
$_SESSION['timeout'] = time();
  require_once "inc/sessionVerify.php";
 ?>

		<div id = "main">
			<?php

			//calls dependencies
			require_once "inc/functions.php";
			$message = "";
			$feedback = array();
			if(isset($_REQUEST['submit']))
			{
				//Get the values from POST
				$email = sanitize($_SESSION['uid']);
				$oldpass = sanitize($_POST['oldpass']);
				$pass = sanitize($_POST['pass']);
				$conpass = sanitize($_POST['conpass']);
				

				// Check Password sufficient strength
				$passcheck=passwordCheck($pass,$conpass,8,$feedback);

				if($passcheck==TRUE)
				{
					// check password and confirm password matching
					$pass = passHash($pass);
					$conpass = passHash($conpass);
					$oldpass = passHash($oldpass);
					if($pass==$conpass)
					{
						//From here, we need a database connection to confirm things.
						$connection = dbConnect();

						//Check if this email/password combination exists in the database.
						$procedure = 'call sp_count_user1("' . $email . '","' . $oldpass . '")';
						$procedureCall = $connection->query($procedure);
						//If the procedure call fails, return a generic query error.
						if($procedureCall)
						{
							$result = $procedureCall->fetch(PDO::FETCH_OBJ);
							$count = $result->c;
							$procedureCall->closeCursor();
							if($count == 1)
							{
								//Extra security measure:  Only Set on areas where the email and password match.
								$sql="UPDATE USER set Password=? where Email=? and Password = ?"; // Update password into table
								$stmt= $connection->prepare($sql);
								$success = $stmt->execute(array($pass, $email, $oldpass));
								if($success)
									$message = "<p>Password reset successful.</p>";
								else
									$message = "<p style = \"color: red;\">Error occured while attempting to reset password.</p>";
							}
							else
							{
								$message = "<p>Old password is incorrect.</p>";
							}
						}
						else
						{
							$message = "<p>Error attempting to get user count.</p>";
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
			            <H3>Enter your old and new password</H3>
			            <?php print $message;?>
			              <form action="#" id="form" method="post" name="form">
			                <!-- Form contains a field for login and password -->
			              <label>Old Password</label><input name="oldpass" placeholder="Enter Old Password" type="password" >
			              <label>New Password</label><input name="pass" placeholder="Enter New Password" type="password" >
			              <label>New Password Confirmation</label><input name="conpass" placeholder="Enter Confirm Password" type="password" >
			              <br />
			              <input id="send" name="submit" type="submit" value="Submit">
			            </form>
			          </div>
			        </div>
		</div>

	</body>

</html>
