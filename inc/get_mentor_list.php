<?php
//Check to see if this is a mentee who is already paired.
$statementText = "SELECT COUNT(*) as c FROM READABLE_PAIRED_MENTEES WHERE MenteeEmail = ?";
$statement = $connection->prepare($statementText);
$result = $statement->execute(array($_SESSION['uid']));
if(!$result)
	array_push($feedback, '<h1>Error checking existing mentorship status.</h1>');
else
{
	$result = $statement->fetch(PDO::FETCH_OBJ);
	$count = $result->c;
	if($count > 0)
		array_push($feedback, '<h1>You already have an existing mentorship.  Please complete it before pursuing another.</h1>');
	else
	{
		//Try to get the list of potential mentors.
		$procedure = 'call sp_get_all_potential_mentors()';
		$procedureCall = $connection->query($procedure);
		//Create an empty array to store the list of users. 
		$users = array();
		if(!$procedureCall)
			array_push($feedback, '<p style = "color: red;">Error fetching list of potential mentors.</p>');
		else
		{
			//Create an array of results as objects, then close the cursor.
			$users = $procedureCall->fetchAll(PDO::FETCH_OBJ);
			$procedureCall->closeCursor();
		}

		require_once("build_table.php");
		//Create a user table of type "mentor"
		//echo user table takes arguments (db connection, users array, table type, feedback array)
		echoUserTable($connection, $users, "mentor", $feedback);
		}
}
?>