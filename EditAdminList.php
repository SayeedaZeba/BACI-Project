<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Edit Administrators</title>
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
			$feedback = array();
			$gotAdminList = FALSE;

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
					//Attempt to get the list of admins.
					$procedure = 'call sp_get_admin_list();';
					$procedureResult = $connection->query($procedure);
					//On failure, return an error.
					if(!$procedureResult)
						array_push($feedback, '<p style = "color: red;>Query Error.</p>');
					else
					{
						//On success, create an array of admins called "adminList"
						$adminList = $procedureResult->fetchAll(PDO::FETCH_OBJ);
						$procedureResult->closeCursor();
						$gotAdminList = TRUE;
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
						if($gotAdminList)
						{
							//Create a table of admins.
							echo '<h2>Admins:</h2>' . "\n";
							echo '<p>Click an admin\'s username to edit their account details.</p>';
							echo '<table style = "width:  100%;">' . "\n";
							echo '<tr><th>Username</th><th>Permissions</th></tr>' . "\n";
							for($index = 0; $index < count($adminList); $index++)
							{
								echo '<tr>';
								echo '<td>' .'<a href="editAdmin.php?username='. $adminList[$index]->Username . '">' . $adminList[$index]->Username . '</td>';
								echo '<td>' . $adminList[$index]->Description . '</td>';
								echo '</tr>' . "\n";
							}
							echo '</table>' . "\n";
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
