<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Edit Existing Administrator</title>
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
			$permissions;
			$feedback = array();
			$gotAdminTypes = FALSE;
			$userExists = FALSE;
			$adminTypes;


			//Only attempt to edit an admin if there's a SuperAdmin logged in.
			if(adminLoggedIn() and $_SESSION['admin']->isSuperAdmin())
			{
				//Get the username from GET if it exists.
				if(isset($_GET['username']))
					$username = sanitize($_GET['username']);

				//Create a connection to the database.
				$connection = dbConnect();
				if($connection === FALSE)
				{
					array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
				}
				else
				{
					//Make sure we're looking at an admin that really exists.
					$procedure = 'call sp_count_admins_by_username("' . $username . '");';
					$procedureResult = $connection->query($procedure);
					if(!$procedureResult)
					{
						array_push($feedback,'<p style="color:red;">Query Error</p>');
					}
					else
					{
						//Based on instructor provided demo files.
						$row = $procedureResult->fetch(PDO::FETCH_OBJ);
						$count = $row->c;
						$procedureResult->closeCursor();
						if($count == 1)
						{
							//Mark that the user does actually exist.
							$userExists = TRUE;
							//Get the types of admins for later.  I need to know if this was successful before editing any administrators, or else problems can occur.
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
								//Figure out what the permissions are for this user.
								$procedure = 'call sp_admin_get_permissions("' . $username . '");';
								$procedureResult = $connection->query($procedure);
								if(!$procedureResult)
								{
									array_push($feedback, '<p style = "color: red;">Query Error</p>');
								}
								else
								{
									$row = $procedureResult->fetch(PDO::FETCH_OBJ);
									$type = $row->TypeID;
									$procedureResult->closeCursor();
								}
								if(isset($_POST['vPassword']))
								{
									//Only try to change the password if it's not empty.
									if(!empty($_POST['vPassword']))
									{
										//Get the entered password as a variable.
										$password = sanitize($_POST['vPassword']);
										//Make sure the confirmation password matches.
										if($password == $_POST['vConfirm'])
										{
											$hashedPassword = passHash($password);
											$procedure = 'call sp_change_admin_password("' . $username . '","' . $hashedPassword . '");';
											//When doing procedures that don't return anything, use a try/catch block.
											try
											{
												$connection->query($procedure);
												array_push($feedback, '<p>Password Changed Successfully</p>');
											}
											catch(PDOException $e)
											{
												array_push($feedback, '<p style = "color: red;">Query Error</p>');
											}
										}
										else
										{
											array_push($feedback, '<p style = "color: red;">Could not change password:  Confirmation Password does not match.</p>');
										}
									}

								}
								if(isset($_POST['permissions']))
								{
									//If the permissions don't match what's on file,
									//Try to change them.
									if($_POST['permissions'] != $type)
									{
										$permissions = sanitize($_POST['permissions']);
										$procedure = 'call sp_change_admin_permissions("' . $username . '","' . $permissions . '");';
											//When doing procedures that don't return anything, use a try/catch block.
											try
											{
												$connection->query($procedure);
												array_push($feedback, '<p>Permissions Changed Successfully</p>');
											}
											catch(PDOException $e)
											{
												array_push($feedback, '<p style = "color: red;">Query Error</p>');
											}

									}
								}

							}
							else
							{
								//If we're here, the types of admins couldn't be retrieved.
								//Don't let the user try to edit an admin.
								array_push($feedback, '<p style = "color: red;">Couldn\'t get list of admin types!</p>');
							}
						}
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
						{
							if($userExists)
							{
								if($type == 1)
									array_push($feedback, '<p style = "color:red;">Cannot edit Super Admin</p>');
								else
									include("inc/editAdminForm.php");
							}
							else
								array_push($feedback, '<p style = "color: red;">Specified user does not exist</p>');
						}
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
