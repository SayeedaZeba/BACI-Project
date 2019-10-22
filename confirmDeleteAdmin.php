<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Add New Administrator</title>
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
					//Get the username from GET
					if(isset($_GET['username']))
						$username = sanitize($_GET['username']);
					//Figure out if the user exists.
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
							if(isset($_POST['delete']))
							{
								$delete = $_POST['delete'];
								if($delete == 'YES')
								{
									//For now, deletion means ACTUAL DELETION.
									//This may not be desired, but we can change it later if needed.
									$procedure = 'call sp_delete_admin("' . $username . '");';
									//There's no need to return a procedure result.  Instead, a try catch block is used
									try
									{
										$connection->query($procedure);
										array_push($feedback, '<p>Deletion successful.  Returning to Admins List</p>');
										header('Location: EditAdminList.php' );
									}
									catch(PDOException $e)
									{
										array_push($feedback, '<p style = "color: red;">Query Error</p>');
									}
								}
								else
								{
									array_push($feedback, '<p>Returning to Edit Admin Page</p>');
									header('Location: editAdmin.php?username=' . $username );
								}
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
