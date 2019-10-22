<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Add Degree Level</title>
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
					$procedure = 'Call SP_Get_Degrees()';
					$procedureResult = $connection->query($procedure);
					if(!$procedureResult) { //if results don't return an error is displayed
						array_push($feedback, '<p style = "color: red;">Couldn\'t get list of Degrees!</p>');
					}
					else { //if call is sucessful
						$html = array(); //creats an array to display the list as HTML
						while($row = $procedureResult->fetch(PDO::FETCH_ASSOC)) {
						//pushes each row into the array
						array_push($html, '<a href="editDegree.php?degree='. $row["DegreeLevel"] . '">' . $row["DegreeLevel"] . '</a>');
						};
  					$procedureResult->closeCursor();
					}

					//Runs once submit button is hit, adds new degree to list
					if(isset($_POST['submit']))
					{
						//gets name of degree from form
						$degree = sanitize($_POST['newDegree']);

						//Checks if Country already Exists
						$procedure2 = "Call SP_COUNT_DEGREE('".$degree."')";
						$procedureResult2 = $connection->query($procedure2);
						$row = $procedureResult2->fetch(PDO::FETCH_OBJ);
						$count = $row->c;
						$procedureResult2->closeCursor();
						if ($count == 1)
						{
							//Check if this is a deactivated degree.  If so, reactivate it.
							$statementText = 'SELECT * FROM EDUCATION_TYPE WHERE DegreeLevel = ?;';
							$statement = $connection->prepare($statementText);
							$statement->bindParam(1, $degree, PDO::PARAM_STR, 80);
							$successfulRetrieval = $statement->execute();
							if(!$successfulRetrieval)
								echo "Error attempting to retrieve degree.";
							else
							{
								//Get the result from the executed statement.
								$result = $statement->fetch(PDO::FETCH_OBJ);
								$statement->closeCursor();
								$active = $result->Active;
								
								if($active == 0)
								{
									//If this is a deactivated degree, reactivate it.
									$statementText = "UPDATE EDUCATION_TYPE SET ACTIVE = 1 WHERE DegreeLevel = ?;";
									$statement = $connection->prepare($statementText);
									$statement->bindParam(1, $degree, PDO::PARAM_STR, 80);
									$successfulRetrieval = $statement->execute();
									if(!$successfulRetrieval)
									{
										echo "Error attempting to reactivate degree.";
									}
									else
									{
										//If things went well, refresh the page.
										Header ("Location:add_degree_levels.php");
									}
								}
								else
									print "Sorry, this degree already exists.";
							}
							
							
							
							}
						else { //if degree doesn't already exist


							//Call the procedure to add the degree to using SQL insert procedure
							$procedure1 = "Call SP_Add_Degree('".$degree."')";
							$procedureResult1 = $connection->query($procedure1);
							print $procedure1;
							if(!$procedureResult1) { //if results don't return an error is displayed
								array_push($feedback, '<p style = "color: red;">Couldn\'t add the Degrees!</p>');
							} else {
								$procedureResult1->closeCursor();
		            Header ("Location:add_degree_levels.php"); //reload page so new degree shows up on list
							}
						} //else degree does exist closes
					}

				} //database connected
			}//admin logged in
			else
			{
				//If we're here, there isn't an admin logged in, or the don't have high enough privileges.
				array_push($feedback, '<p style = "color: red;">Insufficient privileges.</p>');
			}
			?>



			<h1>Current education levels:</h1>
<?php
//runs for each loop to get the degree value for each degree from the array
foreach ($html as &$value) {
    print $value . "<br />";
}
?>
					<form action="#" id="form" method="post" name="form">
					<h1>Enter new education level</h1>
					<input placeholder="Name of education level" type = "text" name = "newDegree"/>
					<br />
					<input id="send" name="submit" type="submit" value="Submit">
					</form>
		</div>
	</body>

</html>
