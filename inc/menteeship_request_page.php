<?php
//Check if there is an email provided by GET.  If there isn't, stop there.
if(!isset($_GET['email']))
	array_push($feedback, '<p style ="color:red;">No user specified!</p>');
else
{
	//If we got an email, try to get the data for the user matching that email.
	$email = $_GET['email'];
	$procedure = 'call sp_get_userTable_data_by_email("' . $email . '");';
	$procedureCall = $connection->query($procedure);
	if(!$procedureCall)
		array_push($feedback,'<p style ="color:red;">Query Error.</p>');
	else
	{
		$mentorData = $procedureCall->fetch(PDO::FETCH_OBJ);
		$procedureCall->closeCursor();
		//Check if this query returned an empty result.
		if(empty($mentorData))
			array_push($feedback,'<p style ="color:red;">User does not exist.</p>');
		//if not, check if the user is seeking menteeships.
		elseif($mentorData->SeekingMentorship == FALSE)
			array_push($feedback,'<p style ="color:red;">User is not seeking a mentor.</p>');
		else
			$printMentor = TRUE;
	}
}

if($printMentor)
{
	//Check if the user has requested a mentorship (or wishes to go back.)
	//Only do this if this mentor was considered okay to print in the first place.
	if(isset($_POST['mentorRequest']))
	{
		if($_POST['mentorRequest'] == 'YES')
		{
			//Create a mentorship request
			//First, get the user (mentee) ID and mentor ID for later use.
			//A procedure call will be needed for the logged-in user's ID.
			$procedure = 'call SP_Get_User_ID("' . $_SESSION['uid'] . '");';
			$procedureCall = $connection->query($procedure);
			if(!$procedureCall)
				array_push($feedback, '<p style = "color: red;">Error fetching user data.</p>');
			else
			{
				//Assign the mentor ID as the user's ID, and the mentee ID from the GET value.
				$result = $procedureCall->fetch(PDO::FETCH_OBJ);
				$mentorID = $result->userid;
				$procedureCall->closeCursor();
				$menteeID = $mentorData->ID;
				$menteeEmail = $mentorData->Email;
				//Then, attempt to create a request.
				require_once("create_mentorship_request.php");
				createMentorshipRequest($connection, $mentorID, $menteeID, $mentorID, $_SESSION['uid'], $menteeEmail, $feedback);
			}
		}
		else
		{
			//If the user clicked "No", return to the list.
			header("Location: mentorList.php");
		}
	}
	
	//Print the mentor's data, then a form to send a request to start a mentorship.
	require_once("user_page.php");
	printUserPage($connection, $email);
	echo '<div class = "container">';
	echo '<h2>Would you like to request to be this user\'s mentor?</h2>';
	echo '<form action="#" id="form" method="post" name="form"><input id="send" name="mentorRequest" type="submit" value="YES"> &nbsp <input id="send" name="mentorRequest" type="submit" value="NO (Return to list)"></form>';
	echo '</div>';
}
?>