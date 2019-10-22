<?php
include "header.php";
$_SESSION['timeout'] = time();
?>


<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Admin Login</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>

		<div id = "main">
			<?php

			//Create required variables.
			$username = "";
			$password = "";
			$feedback = array();

			$connection = dbConnect();
			if($connection === FALSE)
			{
				array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
			}
			else
			{
				if(isset($_POST['vUsername']))
				{
					//Trim the username and password, then hash the password.
					$username = sanitize($_POST['vUsername']);
					$password = sanitize($_POST['vPassword']);
					//If the username field is empty, tell the user to enter a username and stop there.
					if(empty($username))
						array_push($feedback, '<p style="color: red;">Please enter a username.</p>');
					else
					{
						//If the password is empty, tell the user to enter a password and stop there.
						if(empty($password))
							array_push($feedback, '<p style="color: red;">Please enter a password.</p>');
						else
						{
							//Otherwise, hash the password before comparing it to what's stored in the database.
							$hashedPassword = passHash($password);
							//Create the query.  This code is based off of the login.php page provided by the instructor.
							$procedure = 'Call sp_count_admins("' . $username . '","' . $hashedPassword . '");';
							$procedureResult = $connection->query($procedure);
							if(!$procedureResult)
							{
								//If the procedure isn't valid, return an error.  Keep it vague.
								array_push($feedback, '<p style = "color:red">Query Error.</p>');
							}
							else
							{
								//Get the count of the users of this name & password.
								//(Based on instructor-provided demo files from Ch.19)
								$row = $procedureResult->fetch(PDO::FETCH_OBJ);
								$count = $row->c;
								$procedureResult->closeCursor();
								if($count == 1)
								{
									array_push($feedback, '<p>Logging in...</p>');
									//If we're here, this admin exists.  Let's figure out what their permissions are.
									//Create a procedure string.
									$procedure = 'Call sp_admin_get_permissions("'. $username . '");';
									//Then, perform the query.
									$procedureResult = $connection->query($procedure);
									if(!$procedureResult)
									{
										//If the procedure isn't valid, return an error.
										array_push($feedback, '<p style = "color:red">Query Error.</p>');
									}
									else
									{
										//Otherwise, try to figure out what kind of admin this is.
										$row = $procedureResult->fetch(PDO::FETCH_OBJ);
										$adminLevel = $row->TypeID;
										$procedureResult->closeCursor();
										array_push($feedback, '<p>Success.</p>');
										$_SESSION['admin'] = new admin($adminLevel, $username);
										//$_SESSION['message'] = '<p>Success.</p>';
										Header ("Location:adminLogin.php"); //reload page so new degree shows up on list
									}
								}
								else
								{
									array_push($feedback, '<p>Incorrect username or password</p>');
								}
							}


						}
					}
				}
			}
			?>
        <!-- Form for login Starts Here -->
    <div class="container containerinput" style="margin-top:75px">

    	<!-- Heading Of The Form -->
      <H1>Admin Login:</H1>
      <form action="#" id="form" method="post" name="form">
	      <!-- Form contains a field for login and password -->
				<input name="vUsername" placeholder="Enter Username" type="text" value="<?php echo $username;?>" size = "100"/>
				<input name="vPassword" placeholder="Enter Password" type="password" value="<?php echo $password;?>" size ="100"/>
	      <br />
	      <input id="send" name="submit" type="submit" value="Submit">
      </form>
			<!-- displays error messages if applicable or prints message notifying of successful login -->
			<?php
			for($index = 0; $index < count($feedback); $index++)
			{
				echo $feedback[$index];
			}
			if(isset($_SESSION['admin'])) {
				print "login sucessful";
			}
			?>

    </div>
		</div>
	</body>
</html>
