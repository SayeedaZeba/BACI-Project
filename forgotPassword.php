<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>My Template</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
<?php include "header.php";
 ?>

		<div id = "main">
			<?php

			//calls dependencies
			require_once "inc/functions.php";
			require_once "inc/RegistrationFormPHP.php";
			require_once "inc/mail/mail.class.php";

			//A submission must be sent, and a valid email given, which is not blank.
			//$validate is true when a valid email is given.
			//$eok is true when an email is not blank.
			if(isset($_REQUEST['submit']) and $validate and $eok)
			{
					//Get the user's email address, and also start creating some info for the reset email.
					$email= sanitize($_REQUEST['vEmail']);
					$subject = "Reset Password";
					$connection = dbConnect();
					//Counting the number of users with the input username
					$procedure = 'Call sp_count_users_by_email("' . $email . '")';
					$procedureResult = $connection->query($procedure);
					if(!$procedureResult) //if results don't return an error is displayed
					{
						array_push($feedback, '<p style = "color:red;">Query Error.</p>');
					}
					else
					{
						//get the usercount.
						$row = $procedureResult->fetch(PDO::FETCH_OBJ);
						$count = $row->c;
						//Close the cursor.
						$procedureResult->closeCursor();
					}

					//checking if the user already exists
					if($count==0)
					{

						echo "A user with this email address does not exist.";
					}
					else
					{
						//calls function to generate an alphanumeric code of 6 digits
						//The function avoids characters that look like numbers, like I, L, and O.
						$resetCode = generatePIN();
						
						$sql="UPDATE USER set ResetPIN=? where Email=?";
						$stmt= $connection->prepare($sql);
						$stmt->execute(array($resetCode, $email));
						//sets body for Mail() function
						$body = 'Please <a href="http://corsair.cs.iupui.edu:23081/CourseProject/resetPassword.php">click here</a> to reset your password. <br/>
						Reset PIN ="'.$resetCode.'"<br/>';

						//calls mail function to send out the activation email, shows message if successful
						$mailer = new Mail();
						if (($mailer->sendMail($email,"BACI USER", $subject, $body))==true)
						$message = "<b>An email has been sent with instructions to reset your password..</b>";
						//message displays an error if email does not go through
						else $message = "Email not sent. " . $email.' '. $subject.' '. $body;
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
