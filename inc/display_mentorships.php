<?php
require_once("get_mentorships.php");
//Only attempt to load anything if the retrieval was successful.
if($retrievalSuccess == true)
{
	//----====POST logic====----//
	//--Approval process--//
	if(isset($_POST['approveID']))
	{
		$ID = $_POST['approveID'];
		$date = date("Y-m-d");
		$procedure = 'call sp_confirm_mentorship("'.$date.'","'.$ID.'");';
		$procedureCall = $connection->query($procedure);
		if(!$procedureCall)
			echo '<h1 class = "alert alert-danger">Error attempting to issue pairing approval!</h1>';
		else
		{
			//Close the cursor.
			$procedureCall->closeCursor();
			//All other requests to the mentee specified by this entry should be rejected automatically.
			//First, initialize a variable for mentee ID
			$menteeID = "";
			//Somewhere in requests, there should still be an entry that matches the ID specified by POST.
			foreach($requests as $request)
				if($request->RecordID == $ID)
					$menteeID = $request->MenteeID;
			//Now that we have a menteeID, use it to reject all other mentorship requests for that mentee.
			$procedure = 'call sp_reject_mentorship_by_menteeID("' . $date . '","' . $menteeID . '");';
			$procedureCall = $connection->query($procedure);
			if(!$procedureCall)
				echo '<h1 class = "alert alert-danger">Error attempting to remove mentee\'s other requests!</h1>';
			else
			{
				$procedureCall->closeCursor();
				header("Location: " . $_SERVER["PHP_SELF"] );
			}
			
		}
	}
	//--Rejection Process--//
	if(isset($_POST['rejectID']))
	{
		//Get the ID from POST and the date from PHP.
		$ID = $_POST['rejectID'];
		$date = date("Y-m-d");
		$procedure = 'call sp_reject_mentorship("'.$date.'","'.$ID.'");';
		$procedureCall = $connection->query($procedure);
		if(!$procedureCall)
			echo '<h1 class = "alert alert-danger">Error attempting to issue pairing rejection!</h1>';
		else
		{
			//Close the cursor and refresh the page.
			$procedureCall->closeCursor();
			header("Location: " . $_SERVER["PHP_SELF"] );
		}
	}
	//--Mentorship ending process--//
	if(isset($_POST['endID']))
	{
		//Get the ID from POST and the date from PHP.
		$ID = $_POST['endID'];
		$date = date("Y-m-d");
		$procedure = 'call sp_unpair_mentorship("'.$date.'","'.$ID.'");';
		$procedureCall = $connection->query($procedure);
		if(!$procedureCall)
			echo '<h1 class = "alert alert-danger">Error attempting to end mentorship!</h1>';
		else
		{
			//Close the cursor and refresh the page.
			$procedureCall->closeCursor();
			header("Location: " . $_SERVER["PHP_SELF"] );
		}
	}
	

	//----====Display items====----//
	//Make sure the user is a mentor or mentee.
	if($mentorMentee == "neither")
		echo '<h1>You are currently not accepting mentorships.</h1>';
	else
	{
		echo '<form action ="#" method = "POST">' . "\n";
		echo '<div class = "card-group">' . "\n";
		//Create a card to display the user's pending requests.
		echo '<div class = "card">' . "\n";
		echo '	<div class = "card-header">' . "\n";
		echo '		Pending ' . $requestType . 'ship requests' . "\n";
		echo '	</div>' . "\n";
		echo '	<div class = "card-body">' . "\n";
		if(empty($requests))
		{
			echo '		<h5 class = "card-title">No pending requests.</h5>' . "\n";
			echo '		<p class = "card-text">It looks like you don\'t have any requests right now.</p>' . "\n";
		}
		else
		{
			foreach( $requests as $request )
			{
				//Check if this is a request sent by the user, or received by the user.
				if($request->RequesterEmail == $_SESSION['uid'])
				{
					//Sent by user:  Check if the user is a mentor or mentee.
					//Only rejection (or technically, cancellation) should be possible
					if($mentorMentee == "mentor")
						echo '		<h5 class = "card-title">Request to '.$request->MenteeEmail.' requested on ' . $request->RequestDate . '. </h5>' . "\n";
					else
						echo '		<h5 class = "card-title">Request to '.$request->MentorEmail.' requested on ' . $request->RequestDate . '.</h5>' . "\n";
					echo '		<p class = "card-text">This request by you for a '.$requestType.'ship is still pending.</p>' . "\n";
					echo '		<button type = submit name = "rejectID" value = "'.$request->RecordID.'" class = "btn btn-warning">Cancel Request</button>' . "\n";
				}
				else
				{
					//Received by user:  Give the option to approve or reject.
					echo '		<h5 class = "card-title">Request from '.$request->RequesterEmail.' requested on ' . $request->RequestDate . '.</h5>' . "\n";
					echo '		<p class = "card-text">'.$request->RequesterEmail.' would like to be your '. $requestType .'!</p>' . "\n";
					echo '		<button type= "submit" name ="approveID" value="'.$request->RecordID.'" class = "btn btn-primary">Approve</button> <button type= "submit" name ="rejectID" value="'.$request->RecordID.'" class = "btn btn-warning">Reject</button>' . "\n";
				}
			}
		}
		//If we're not on the page to look for pairings, give a link to it.
			if(strpos($_SERVER["PHP_SELF"], "mentorList.php") === FALSE)
				echo '		<br/><br/><a href = "mentorList.php" class = "btn btn-primary">Look for a '.$requestType.'</a>';
		echo '	</div>' . "\n";
		echo '</div><br/>' . "\n";
		
		//Create a card to display the user's mentorship/menteeship status.
		echo '<div class = "card">' . "\n";
		echo '	<div class = "card-header">' . "\n";
		echo '		Current ' . $requestType . 'ships' . "\n";
		echo '	</div>' . "\n";
		echo '	<div class = "card-body">' . "\n";
		if(empty($pairings))
		{
			echo '		<h5 class = "card-title">No current '.$requestType.'ship.</h5>' . "\n";
			echo '		<p class = "card-text">It looks like you don\'t have any pairings right now.</p>' . "\n";
		}
		else
		{
			foreach( $pairings as $pairing )
			{
				if($mentorMentee == "mentor")
					echo '		<h5 class = "card-title">'.$requestType.'ship with '.$pairing->MenteeFirstName.' '.$pairing->MenteeLastName.'</h5>' . "\n";
				else
				{
					echo '		<h5 class = "card-title">'.$requestType.'ship with '.$pairing->MentorFirstName.' '.$pairing->MentorLastName.'</h5>' . "\n";
				}
				echo '		<p class = "card-text">Started on '.$pairing->StartDate.'</p>' . "\n";
				echo '		<button type = submit name = "endID" value = "'.$pairing->RecordID.'" class = "btn btn-warning">End '.$requestType.'ship</button>'. "\n";
				
			}
		}
		echo '	</div>' . "\n";
		echo '</div>' . "\n";
		echo '</div>' . "\n";
		echo '</form>' . "\n";
		
		//Create a card to toglle viewing history.
		echo '<div class = "card">' . "\n";
		echo '	<div class = "card-header">' . "\n";
		echo '		Mentor/menteeship history' . "\n";
		echo '	</div>' . "\n";
		echo '	<button class ="btn btn-primary" data-toggle="collapse" href = "#history">Show Mentorship History</button> ' . "\n";
		echo '	<div class = "card-body collapse" id = "history">' . "\n";
		if(empty($mentorHistory) and empty($menteeHistory))
		{
			echo '		<h5 class = "card-title">No mentor/menteeship history.</h5>' . "\n";
			echo '		<p class = "card-text">It looks like you haven\'t participated in any mentorships.</p>' . "\n";
		}
		else
		{
			//Display mentorships
			foreach( $mentorHistory as $mentorship )
			{
				echo '		<h5 class = "card-title">Mentorship with ' . $mentorship->MenteeEmail . '</h5>' . "\n";
				echo '		<p class = "card-text">Requested on ' . $mentorship->RequestDate . '</p>' . "\n";
				if(!empty($mentorship->RejectDate))
					echo '		<p class = "card-text">Rejected on ' . $mentorship->RejectDate . '.</p>' . "\n";
				else
					echo '		<p class = "card-text">Ended on ' . $mentorship->EndDate . '.</p>' . "\n";
			}
			//Display menteeships
			foreach( $menteeHistory as $menteeship )
			{
				echo '		<h5 class = "card-title">Menteeship with ' . $menteeship->MentorEmail . '</h5>' . "\n";
				echo '		<p class = "card-text">Requested on ' . $menteeship->RequestDate . '</p>' . "\n";
				if(!empty($mentorship->RejectDate))
					echo '		<p class = "card-text">Rejected on ' . $menteeship->RejectDate . '.</p>' . "\n";
				else
					echo '		<p class = "card-text">Ended on ' . $menteeship->EndDate . '.</p>' . "\n";
			}
		}
		echo '	</div> ' . "\n";
		echo '</div> <br/>' . "\n";
	}
}
?>