<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Add New Administrator</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
<?php include "header.php";
$_SESSION['timeout'] = time();
  require_once "inc/sessionVerifyAdmin.php";
?>

		<div id = "main">
			<?php
			
			//Create required variables.
			$username = "";
			$password = "";
			$type;
			$feedback = array();
			$gotAdminTypes = FALSE;
			$adminTypes;
			$procedureResult;

			//Only attempt to create admins if there's an admin logged in.
			if(adminLoggedIn() and $_SESSION['admin']->isSuperAdmin())
			{
				$connection = dbConnect();
				if($connection === FALSE)
				{
					array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
				}
				else
				{
					//Get the types of admins for later.  I need to know if this was successful before creating any administrators, or else problems can occur.
					$procedure = 'call sp_get_admin_types()';
					//Call the procedure to get the types of administrators.
					$procedureResult = $connection->query($procedure);
					if(!$procedureResult)
					{
						//If the result is invalid, set the marker value to FALSE.  Since that's default, this is redundant, but that doesn't hurt anything.
						$gotAdminTypes = FALSE;
					}
					else
					{
						//otherwise, set the marker to TRUE and get the results as an array.
						$gotAdminTypes = TRUE;
						$adminTypes = $procedureResult->fetchAll(PDO::FETCH_OBJ);
						//Close the cursor.
						$procedureResult->closeCursor();
					}
					if($gotAdminTypes)
					{
						if(isset($_POST['vUsername']))
						{
							//If username is set, so are any dropdown values (ie admin type).  Get It.
							$type = sanitize($_POST['permissions']);
							//Trim the username and password, then hash the password.
							$username = sanitize($_POST['vUsername']);
							$password = sanitize($_POST['vPassword']);
							$confirmPassword = sanitize($_POST['vConfirm']);
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
									//If the password passes the password check, continue.
									$requiredLength = 8;
									if(passwordCheck($password, $confirmPassword, $requiredLength, $feedback))
									{
										//Make sure this admin doesn't already exist.
										$procedure = 'call sp_count_admins_by_username("' . $username . '");';
										$procedureResult = $connection->query($procedure);
										if(!$procedureResult)
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
											if($count > 0)
											{
												//Output an error if we find a user with this name already in the database.
												array_push($feedback, '<p style = "color: red;">An admin with this name already exists</p>');
											}
											else
											{
												//If we're here, everything looks good.
												//Get the hashed version of the password.
												$hashedPassword = passHash($password);
												//Go ahead and try to create the new admin Using the admin object.
												$_SESSION['admin']->addAdmin($feedback, $connection, $username, $hashedPassword, $type);
											}
										}
									}
								}
							}
						}
					}
					else
					{
						//If we're here, the types of admins couldn't be retrieved.
						//Don't let the user try to create an admin.
						array_push($feedback, '<p style = "color: red;">Couldn\'t get list of admin types!</p>');
					}
				}
			}
			else
			{
				//If we're here, there isn't an admin logged in, or the don't have high enough privileges.
				array_push($feedback, '<p style = "color: red;">Insufficient privileges.</p>');
			}
			?>
			            <!-- Form for login Starts Here -->
			        <div class="container containerinput" style="margin-top:75px">
						<div>
						<?php
						if( adminLoggedIn() and $_SESSION['admin']->isSuperAdmin())
							include("inc/createAdminForm.php");

						for($index = 0; $index < count($feedback); $index++)
						{
							echo $feedback[$index];
						}
						?>
						</div>
			        </div>
		</div>

	</body>

</html>
