<?php
//--Variable initialization--//
$user = "";
$retrievalSuccess = FALSE;
$mentorMentee = "neither";
$requestType = "";
$requests = array();
$pairings = array();
$menteeHistory = array();
$mentorHistory = array();
//--Database actions--//
if($connection === FALSE)
	echo '<h1 class = "alert alert-danger">Database connection failed!</h1>';
else
{
	//Retrieve the user's data from the database.
	$statement = $connection->prepare("select * from USER where Email = ?");
	$result = $statement->execute(array($_SESSION['uid']));
	if(!$result)
		echo '<h1 class = "alert alert-danger">Failed to retrieve user data!</h1>';
	else
	{
		//Assign the user variable as an object containing the user's data from the user table.
		$user = $statement->fetch(PDO::FETCH_OBJ);
		$statement->closeCursor();
		//--Figure out whether the user is a mentor or a mentee--//
		if($user->SeekingMenteeship)
			$mentorMentee = "mentor";
		else if($user->SeekingMentorship)
			$mentorMentee = "mentee";
		//--Figure out whether the user gets mentorship or menteeship requests.--//

		if($mentorMentee == "mentor")
			$requestType = "mentee";
		else
			$requestType = "mentor";
		
		
		//--Get Requests statements--//
		//Prepare a different statement, depending on if the user is a mentor or a mentee.
		if($mentorMentee == "mentor")
			$requestsStatement = $connection->prepare("select * from READABLE_MENTOR_HISTORY where MentorEmail = ? AND StartDate IS NULL and RejectDate IS NULL;");
		else if($mentorMentee == "mentee")
			$requestsStatement = $connection->prepare("select * from READABLE_MENTOR_HISTORY where MenteeEmail = ? AND StartDate IS NULL and RejectDate IS NULL;");
		//--Get Pairings statements--//
		if($mentorMentee == "mentee")
			$pairingsStatement = $connection->prepare("select * from READABLE_MENTOR_HISTORY where MenteeEmail = ? and StartDate IS NOT NULL and EndDate IS NULL and RejectDate IS NULL;");
		else if($mentorMentee == "mentor")
			$pairingsStatement = $connection->prepare("select * from READABLE_MENTOR_HISTORY where MentorEmail = ? and StartDate IS NOT NULL and EndDate IS NULL and RejectDate IS NULL;");
		//--Get history statements--//
		$menteeHistoryStatement = $connection->prepare("select * from READABLE_MENTOR_HISTORY where MenteeEmail = ? and EndDate IS NOT NULL and RejectDate IS NOT NULL;");
		$mentorHistoryStatement = $connection->prepare("select * from READABLE_MENTOR_HISTORY where MentorEmail = ? and EndDate IS NOT NULL and RejectDate IS NOT NULL;");
		
		//--Process the above statements--//
		if($mentorMentee == "mentor" or $mentorMentee == "mentee")
		{
			//--Try to get requests --//
			$result = $requestsStatement->execute(array($_SESSION['uid']));
			if(!$result)
			{
				//Mark this as a failure and issue an error.
				$retrievalSuccess = false;
				echo '<h1 class = "alert alert-danger">Failed to retrieve mentorship requests!</h1>';
			}
			else
			{
				//Mark this as a success and get an array of requests.
				$retrievalSuccess = true;
				$requests = $requestsStatement->fetchAll(PDO::FETCH_OBJ);
				$requestsStatement->closeCursor();
			}
			
			//--Try to get pairings--//
			$result = $pairingsStatement->execute(array($_SESSION['uid']));
			if(!$result)
			{
				//Mark this as a failure and issue an error.
				$retrievalSuccess = false;
				echo '<h1 class = "alert alert-danger">Failed to retrieve mentorship pairings!</h1>';
			}
			else
			{
				//Mark this as a success and get an array of pairings.
				$retrievalSuccess = true;
				$pairings = $pairingsStatement->fetchAll(PDO::FETCH_OBJ);
				$pairingsStatement->closeCursor();
			}
			
			//--Try to get Mentee history--//
			$result = $menteeHistoryStatement->execute(array($_SESSION['uid']));
			if(!$result)
			{
				//Mark this as a failure and issue an error.
				$retrievalSuccess = false;
				echo '<h1 class = "alert alert-danger">Failed to retrieve menteeship history!</h1>';
			}
			else
			{
				//Mark this as a success and get an array of pairings.
				$retrievalSuccess = true;
				$menteeHistory = $menteeHistoryStatement->fetchAll(PDO::FETCH_OBJ);
				$menteeHistoryStatement->closeCursor();
			}
			
			//--Try to get Mentor history--//
			$result = $mentorHistoryStatement->execute(array($_SESSION['uid']));
			if(!$result)
			{
				//Mark this as a failure and issue an error.
				$retrievalSuccess = false;
				echo '<h1 class = "alert alert-danger">Failed to retrieve mentorship history!</h1>';
			}
			else
			{
				//Mark this as a success and get an array of pairings.
				$retrievalSuccess = true;
				$mentorHistory = $mentorHistoryStatement->fetchAll(PDO::FETCH_OBJ);
				$mentorHistoryStatement->closeCursor();
			}
			
		}
	}
}
?>