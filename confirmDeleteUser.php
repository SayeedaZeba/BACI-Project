<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Delete user:  <?php if(isset($_GET['email'])) echo $_GET['email'];?></title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
<?php include "header.php"; ?>

		<div id = "main">
			<?php

			//Create required variables.
			$username = "";
			$feedback = array();
			$delete = "";
			
			//Only attempt to delete the user if there's an admin logged in.
			if(adminLoggedIn() and $_SESSION['admin']->isAtLeastAdmin())
			{
				$connection = dbConnect();
				if($connection === FALSE)
				{
					array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
				}
				else
				{
					//Get the email from GET
					if(isset($_GET['email']))
						$username = sanitize($_GET['email']);
					//Figure out if the user exists.
					$procedure = 'call sp_count_users_by_email("' . $username . '");';
					$procedureResult = $connection->query($procedure);
					if(!$procedureResult)
					{
						array_push($feedback,'<p style="color:red;">Error occurred while attempting deletion.</p>');
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
							if(isset($_POST['delete']))
							{
								$delete = $_POST['delete'];
								if($delete == 'YES')
								{
									//Deletion means deactivation and NULLing of a user's activation code.
									$procedure = 'call sp_delete_user("' . $username . '");';
									//Get a success result to make sure the procedure worked.
									$success =	$connection->query($procedure);
									if($success)
									{
										array_push($feedback, '<p>Deletion successful.  Returning to User List</p>');
										header('Location: search.php' );
									}
									else
									{
										array_push($feedback, '<p style = "color: red;">Query Error</p>');
									}
								}
								else
								{
									array_push($feedback, '<p>Returning to Edit Admin Page</p>');
									header('Location: user.php?email=' . $username );
								}
							}
						}
					}
				}
			}
			else
			{
				//If we're here, there isn't an admin logged in, or they don't have high enough privileges.
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
								include("inc/confirmDeletion.php");							
							else
								array_push($feedback, '<p style = "color:red;">User does not exist</p>');
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
