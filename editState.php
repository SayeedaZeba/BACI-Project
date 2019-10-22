<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Edit Existing State</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
<?php include "header.php";
$_SESSION['timeout'] = time();
  require_once "inc/sessionVerifyAdmin.php";
?>


			<?php

			//Create required variables.
			$state = "";
			$newState="";
			$permissions;
			$feedback = array();
			$stateExists = FALSE;



			//Only attempt to edit a country if there's a --Coordinator logged in.
			//This caused an error during our presentation, as it was orginally superAdmin
			if(adminLoggedIn() and $_SESSION['admin']->isAtLeastCoordinator())
			{
				//Get the country from GET if it exists.
				if(isset($_GET['state']))
				$state = sanitize($_GET['state']);

				//Get the country from GET if it exists.
				if(isset($_GET['countryID']))
				$countryID = sanitize($_GET['countryID']);

				//Create a connection to the database.
				$connection = dbConnect();
				if($connection === FALSE)
				{
					array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
				}
				else
				{
					//Make sure we're looking at a country that really exists.
					$procedure2 = 'Call SP_COUNT_STATE("' . $state . '","' . $countryID . '")';
					$procedureResult2 = $connection->query($procedure2);
					if(!$procedureResult2)
					{
						array_push($feedback,'<p style="color:red;">Query Error1</p>');
					}
					else
					{
						//Based on instructor provided demo files.
						$row = $procedureResult2->fetch(PDO::FETCH_OBJ);
						$count = $row->c;
						$procedureResult2->closeCursor();
						if($count == 1)
						{
							$countryExists = TRUE;
							if(isset($_POST['submit']))
							{
							//gets name of country from form
							$newState = sanitize($_POST['vState']);

							//Mark that the country does actually exist.
							$oldState = $state;

							//Updates the Country
							$procedure = "Call SP_UPDATE_STATE('".$newState."', '".$oldState."', '".$countryID."')";
							echo $procedure;
							$procedureResult = $connection->query($procedure);
							if (!$procedureResult) {
								array_push($feedback,'<p style="color:red;">Query Error2</p>');
							}
							else {
								$procedureResult->closeCursor();
								Header ("Location:add_countries.php");
							}

						}

						if(isset($_POST['buttonDelState']))
						{
							$procedure = 'Call SP_DEACTIVATE_STATE("' . $state . '","' . $countryID . '")';
							$procedureResult = $connection->query($procedure);
							if (!$procedureResult) {
								echo "error";
							}
							else
							$procedureResult->closeCursor();
							Header ("Location:add_countries.php");
						}

						} //count==1 Country exists
					}
				}
			}
			else
			{
				//If we're here, there isn't an admin logged in, or the don't have high enough privileges.
				array_push($feedback, '<p style = "color: red;">Insufficient privileges.</p>');
			}
			?>

						<?php
						for($index = 0; $index < count($feedback); $index++)
						{
							echo $feedback[$index];
						}
						?>

		<div id="main">
			<H1>Editing State: "<?php echo $state; ?>"</H1>
		<p>If a field is left blank, it will not be changed.</p>
		<form action="#" id="form" method="post" name="form">
					<!-- Form contains a field for login and password -->
		<input name="vState" type="text" value="<?php echo $state; ?>" size ="100"/>
		<input id="send" name="submit" type="submit" value="Confirm Edit">
		</form>
		<br />
		<form action = "#" method="post" name="delete">
		<input id="delete" name="buttonDelState" type="submit" value="Remove State"></button>
		</form>
		</div>


	</body>

</html>
