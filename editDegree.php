<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Edit Existing Degree</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
<?php include "header.php";
$_SESSION['timeout'] = time();
  require_once "inc/sessionVerifyAdmin.php";
?>


			<?php

			//Create required variables.
			$degree = "";
			$newDegree="";
			$permissions;
			$feedback = array();
			$degreeExists = FALSE;



			//Only attempt to edit a country if there's a coordinator or greater logged in.
			if(adminLoggedIn() and $_SESSION['admin']->isAtLeastCoordinator())
			{
				//Get the country from GET if it exists.
				if(isset($_GET['degree']))
				$degree = sanitize($_GET['degree']);

				//Create a connection to the database.
				$connection = dbConnect();
				if($connection === FALSE)
				{
					array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
				}
				else
				{
					//Make sure we're looking at a degree that really exists.
					$procedure2 = "Call SP_COUNT_DEGREE('".$degree."')";
					$procedureResult2 = $connection->query($procedure2);
					if(!$procedureResult2)
					{
						array_push($feedback,'<p style="color:red;">Query Error</p>');
					}
					else
					{
						//Based on instructor provided demo files.
						$row = $procedureResult2->fetch(PDO::FETCH_OBJ);
						$count = $row->c;
						$procedureResult2->closeCursor();
						if($count == 1)
						{
							//Mark that the degree exists.
							$degreeExists = TRUE;
							if(isset($_POST['submit']))
							{
								//gets name of degree from form
							$newDegree = sanitize($_POST['vDegree']);

							//A renaming is performed for readability.
							$oldDegree = $degree;

							//Updates the Degree
							$procedure = "Call SP_UPDATE_DEGREE('".$newDegree."', '".$oldDegree."')";
							$procedureResult = $connection->query($procedure);
							if (!$procedureResult) {
								array_push($feedback,'<p style="color:red;">Error while updating degree.</p>');
								array_push($feedback,'<p style="color:red;">Possible cause:  A degree with this name exists already.  If it is a deactivated degree, enter its description in the add degree options menu, and it will be re-activated.</p>');
							}
							else {
                $procedureResult->closeCursor();
                print $newDegree;
                print $oldDegree;
  							Header ("Location:add_degree_levels.php");
              }

						}

        // This is needed if deleting a degree functionality is needed in the future
				 		if(isset($_POST['buttonDelDegree']))
				 		{
				 			$procedure = "Call SP_DEACTIVATE_DEGREE('".$degree."')";
				 			$procedureResult = $connection->query($procedure);
				 			if (!$procedureResult) {
				 				echo "Error while attempting to delete degree.";
				 			}
				 			else
				 			$procedureResult->closeCursor();
				 			Header ("Location:add_degree_levels.php");
				 		}

						} //count==1Country exists
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
			<H1>Editing Degree: "<?php echo $degree; ?>"</H1>
		<p>If a field is left blank, it will not be changed.</p>
		<form action="#" id="form" method="post" name="form">
					<!-- Form contains a field for login and password -->
		<input name="vDegree" type="text" value="<?php echo $degree; ?>" size ="100"/>
		<input id="send" name="submit" type="submit" value="Confirm Edit">
		</form>
		<br />
    <!-- This is needed if deleting a degree functionality is needed in the future -->
		<form action = "#" method="post" name="delete">
		<input id="delete" name="buttonDelDegree" type="submit" value="Remove Degree"></button>
		</form>
		</div>


	</body>

</html>
