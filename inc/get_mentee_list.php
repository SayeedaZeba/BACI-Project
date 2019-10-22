<?php
//Try to get the list of potential mentees.
$procedure = 'call sp_get_all_potential_mentees()';
$procedureCall = $connection->query($procedure);
//Create an empty array to store the list of users. 
$users = array();
if(!$procedureCall)
	array_push($feedback, '<p style = "color: red;">Error fetching list of mentees.</p>');
else
{
	//Create an array of results as objects, then close the cursor.
	$users = $procedureCall->fetchAll(PDO::FETCH_OBJ);
	$procedureCall->closeCursor();
}

//Before creating a table of mentees, we need to filter out mentees who have active mentorships.


require_once("build_table.php");
//Create a user table of type "mentee"
//echo user table takes arguments (db connection, users array, table type, feedback array)
echoUserTable($connection, $users, "mentee", $feedback);

?>