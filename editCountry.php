<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Edit Existing Country</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
<?php include "header.php";
$_SESSION['timeout'] = time();
  require_once "inc/sessionVerifyAdmin.php";
?>


			<?php

			//Create required variables.
			$country = "";
			$newCountry="";
			$permissions;
			$feedback = array();
			$countryExists = FALSE;



			//Only attempt to edit a country if there's a SuperAdmin logged in.
			if(adminLoggedIn() and $_SESSION['admin']->isAtLeastCoordinator())
			{
				//Get the country from GET if it exists.
				if(isset($_GET['country']))
				$country = sanitize($_GET['country']);

				//Create a connection to the database.
				$connection = dbConnect();
				if($connection === FALSE)
				{
					array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
				}
				else
				{
					//Make sure we're looking at a country that really exists.
					$procedure2 = "Call SP_COUNT_COUNTRY('".$country."')";
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

						$procedure2 = "Call SP_Get_Country_ID('".$country."')";
						$procedureResult2 = $connection->query($procedure2);
						if(!$procedureResult2)
						{
							array_push($feedback,'<p style="color:red;">Query Error</p>');
						}
						else
						{
							//Based on instructor provided demo files.
							$row = $procedureResult2->fetch(PDO::FETCH_OBJ);
							$countryID = $row->ID;
							$procedureResult2->closeCursor();

						}

						if($count == 1)
						{
							$countryExists = TRUE;
							if(isset($_POST['EditName']))
							{
							//gets name of country from form
							$newCountry = sanitize($_POST['vCountry']);
							//Don't keep going if the data is empty!
							if(empty($newCountry))
								array_push($feedback,'<p style="color:red;">Please enter a name for the country.</p>');
							else
							{
								//For the purpose of clarity, a duplicate variable of this country
								//Is created.
								$oldCountry = $country;

								//Updates the Country
								$procedure = "Call SP_UPDATE_COUNTRY('".$newCountry."', '".$oldCountry."')";
								$procedureResult = $connection->query($procedure);
								if (!$procedureResult) {
									array_push($feedback,'<p style="color:red;">Error occured while attempting to update country.</p>');
								}
								else {
									$procedureResult->closeCursor();
									Header ("Location:add_countries.php");
							}
							}

						}

						if(isset($_POST['buttonDelCountry']))
						{
							$procedure = "Call SP_DEACTIVATE_COUNTRY('".$country."')";
							$procedureResult = $connection->query($procedure);
							if (!$procedureResult) {
								echo "Error occurred while attempting to deactivate country.";
							}
							else
							$procedureResult->closeCursor();
							Header ("Location:add_countries.php");
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
			<H1>Editing Country: "<?php echo $country; ?>"</H1>
			<p>Use the field below to change the name of the country.</p>
		<form action="#" id="form" method="post" name="form">
		<input name="vCountry" type="text" value="<?php echo $country; ?>" size ="100"/>
		<input id="send" name="EditName" type="submit" value="Confirm Edit">
		</form>
		<br />
		<form action = "#" method="post" name="delete">
		<input id="delete" name="buttonDelCountry" type="submit" value="Remove Country"></button>
		</form>
		


		<div id = "state">
			<?php

			//Create required variables.
			$feedback = array();
			$gotstates = FALSE;


			//Only attempt to add states if there's an admin logged in.
			if(adminLoggedIn() and $_SESSION['admin']->isAtLeastCoordinator())
			{
				$connection = dbConnect();
				if($connection === FALSE)
				{
					array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
				}
				else
				{
					//Calls SQL procedure that generates a list of countries
					$procedure1 = 'Call Get_States("'.$countryID.'")';
					$procedureResult1 = $connection->query($procedure1);
					if(!$procedureResult1) { //if results don't return an error is displayed
						array_push($feedback, '<p style = "color: red;">Couldn\'t get list of States!</p>');
					}
					else { 
					//if call is successful
					$htmlStates = array(); //creats an array to display the list as HTML
					$states = $procedureResult1->fetchAll(PDO::FETCH_OBJ);
					sortObjectsByName($states);
					foreach($states as $state)
						array_push($htmlStates, '<a href="editState.php?state='. $state->Name . "&countryID=" . $countryID . '">' . $state->Name . '</a>');
  					$procedureResult1->closeCursor();
					}

					//Runs once submit button is hit, adds new state to list
					if(isset($_POST['addState']))
					{
						//gets name of state from form
						$state = sanitize($_POST['newState']);

						//Checks if State already Exists
						$procedure2 = 'Call SP_COUNT_ALL_STATE("' . $state . '","' . $countryID . '")';
						$procedureResult2 = $connection->query($procedure2);
						$row = $procedureResult2->fetch(PDO::FETCH_OBJ);
						$count = $row->c;
						$procedureResult2->closeCursor();
						if ($count == 1)
						{
							//If the state exists, but it's an inactive state, re-activate it!
							//First, check the database entry for this state and get its active/inactive status
							$procedure = 'call SP_STATE_ACTIVE_CHECK("' . $state . '","' . $countryID . '");';
							$procedureResult = $connection->query($procedure);
							if(!$procedureResult)
								print 'Error retrieving state active status';
							else
							{
								$row = $procedureResult->fetch(PDO::FETCH_OBJ);
								$active = $row->Active;
								$procedureResult->closeCursor();
								//If the state is active, return an error.
								if($active == "1")
									print "Sorry, this state already exists";
								else
								{
									$procedure = 'call SP_ACTIVATE_STATE("' . $state . '","' . $countryID . '");';
									$procedureResult = $connection->query($procedure);
									if($procedureResult === FALSE)
										print "Error re-activating existing state.";
									else
										//Reload the page so the re-activated state shows up on the list.
										// Header ("Location:editCountry.php?country=" + $country);
										header("Refresh:0");
								}
							}
						}
						else 
						{ //if country doesn't already exist

						//Call the procedure to add the country to using SQL insert procedure
						$procedure2 = 'Call SP_Add_State("' . $state . '","' . $countryID . '")';
						$procedureResult2 = $connection->query($procedure2);
						$procedureResult2->closeCursor();
						Header ("Location:editCountry.php?country=" . $country); //reload page so new state shows up on list
						}
					}

				} //database connected
			}//admin logged in
			else
			{
				//If we're here, there isn't an admin logged in, or the don't have high enough privileges.
				array_push($feedback, '<p style = "color: red;">Insufficient privileges.</p>');
			}
			?>



			<h1>Current state list:</h1>
			<p>Click on a state to edit that state.</p>
<?php
//runs for each loop to get the country value for each country from the array
foreach ($htmlStates as &$value) {
    print $value . "<br />";
}
?>
					<form action="#" id="form" method="post" name="form">
					<h1>Enter new state name</h1>
					<input placeholder="State name" type = "text" name = "newState"/>
					<br />
					<input id="addState" name="addState" type="submit" value="Submit">
					</form>
					<a href = "add_countries.php">Return to country List</a>
		</div>





	</body>

</html>
