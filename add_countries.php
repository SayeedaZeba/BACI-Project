<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Add New Country</title>
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
			$gotCountries = FALSE;
			$procedureResult;

			//Only attempt to add Countries if there's an admin logged in.
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
					$procedure1 = 'Call Get_Countries()';
					$procedureResult1 = $connection->query($procedure1);
					if(!$procedureResult1) { //if results don't return an error is displayed
						array_push($feedback, '<p style = "color: red;">Couldn\'t get list of Countries!</p>');
					}
					else 
					{
					//if call is successful
					$countries = $procedureResult1->fetchAll(PDO::FETCH_OBJ);
					sortObjectsByName($countries);
					//Takes procedure result and creates an array of HTML links.
					$html = array();
					foreach($countries as $country)
						array_push($html, '<a href="editCountry.php?country='. $country->Name . '">' . $country->Name . '</a>');
					$procedureResult1->closeCursor();
					}

					//Runs once submit button is hit, adds new country to list
					if(isset($_POST['submit']))
					{
						//gets name of country from form
						$country = sanitize($_POST['newCountry']);

						//Checks if Country already Exists
						$procedure2 = "Call SP_COUNT_ALL_COUNTRY('".$country."')";
						$procedureResult2 = $connection->query($procedure2);
						$row = $procedureResult2->fetch(PDO::FETCH_OBJ);
						$count = $row->c;
						$procedureResult2->closeCursor();
						if ($count == 1)
						{
							//If the country exists, but it's an inactive country, re-activate it!
							//First, check the database entry for this country and get its active/inactive status
							$procedure = 'call SP_COUNTRY_ACTIVE_CHECK("' . $country . '");';
							$procedureResult = $connection->query($procedure);
							if(!$procedureResult)
								print 'Error retrieving country active status';
							else
							{
								$row = $procedureResult->fetch(PDO::FETCH_OBJ);
								$active = $row->Active;
								$procedureResult->closeCursor();
								//If the country is active, return an error.
								if($active)
									print "Sorry, this country already exists";
								else
								{
									$procedure = 'call SP_ACTIVATE_COUNTRY("' . $country . '");';
									$procedureResult = $connection->query($procedure);
									if($procedureResult === FALSE)
										print "Error re-activating existing country.";
									else
										//Reload the page so the re-activated country shows up on the list.
										Header ("Location:add_countries.php");
								}
							}
						}
						else { //if country doesn't already exist

						//Call the procedure to add the country to using SQL insert procedure
						$procedure2 = "Call SP_Add_Country('".$country."')";
						$procedureResult2 = $connection->query($procedure2);
						$procedureResult2->closeCursor();
            Header ("Location:add_countries.php"); //reload page so new Country shows up on list
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



			<h1>Current countries list:</h1>
			<p>Click on a country to edit it.</p>
<?php
//runs for each loop to get the country value for each country from the array
foreach ($html as &$value) {
    print $value . "<br />";
}
?>
					<form action="#" id="form" method="post" name="form">
					<h1>Enter new country name</h1>
					<input placeholder="Country name" type = "text" name = "newCountry"/>
					<br />
					<input id="send" name="submit" type="submit" value="Submit">
					</form>
		</div>
	</body>

</html>
