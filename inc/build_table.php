<?php
function echoUserTable($connection, $usersArray, $type, &$feedback)
{
	//Initialize heading, subheading, and link url.
	$heading = "";
	$subheading = "";
	$url = "";
	//Types allow this function to perform multiple functions.
	//Add new types as needed!
	if($type == "mentor")
	{
		//Heading.  This should be a summary of the type of table.
		$heading = "Mentors list";
		//Table subheading.  This should probably be instructional.
		$subheading = "Click on a user's name to start a mentorship request with this user";
		//URL to a context-sensitive link.  For example, for admins, this could be used to link to an edit user page.
		//In this case, it's linking to a page to start a mentorship.
		//Remember, $url will always be followed by a GET value to the user's email in the current design.
		$url = "startMentorship.php";
	}
	else if($type == "mentee")
	{
		$heading = "Mentees list";
		$subheading = "Click on a users' name to start a menteeship request with that user.";
		$url = "startMentorship.php";
	}
	else if($type == "user")
	{
		$heading = "User list";
		$subheading = "Click on a user's name to view that user's page.";
		$url = "user.php";
	}
	//Check if there are users on the list
	if(empty($usersArray))
		array_push($feedback, '<p style = "color: red;">No users found.</p>');
	else
	{
		echo '<h1>'. $heading . '</h1>';
		echo '<p>' . $subheading . '</p>';
		//Output a table with headings.
		echo '<script>$(document).ready( function () {$(\'#user_table\').DataTable();} );</script>';
		echo '<table id="user_table" class="display">' . "\n";
		echo '<thead>';
		echo '<tr>';
		echo '<th>Name</th>';
		echo '<th>Age</th>';
		echo '<th>Gender</th>';
		echo '<th>Email</th>';
		echo '<th>Address</th>';
		echo '<th>Field</th>';
		echo '<th>Status</th>';
		echo '<th>Education</th>';
		echo '<th>Register Date</th>';
		echo '</tr>';
		echo '</thead><tbody>';
		foreach( $usersArray as $user )
		{
			//Show the user's name, email, and address.
			echo '<tr>';
			//The first name will have a GET link that uses the users's email.
			//The actual link will be based on $url, so that this function can be more generic.
			echo '<td><p> <a href = "' . $url . '?email=' . $user->Email . '">' . $user->FirstName . ' ' . $user->LastName . '</a></p></td>' . "\n";
			echo '<td><p>' . $user->ApproxAge . '</a></p></td>' . "\n";
			echo '<td><p>' . $user->Gender . '</a></p></td>' . "\n";
			echo '<td><p>' . $user->Email . '</p></td>' . "\n";
			echo '<td><p>' . $user->Address . ', ' . $user->City . ', ' . $user->StateProvinceFromID . ', ' . $user->Country . '</p></td>' . "\n";
			echo '<td><p>' . $user->Field . '</p></td>' . "\n";
			echo '<td><p>' . $user->WorkStatus . '</p></td>' . "\n";
			echo '<td>';
			//Get the mentor's education information.
			$procedure = 'call sp_get_all_education_by_email("' . $user->Email . '");';
			$procedureCall = $connection->query($procedure);
			if(!$procedureCall)
				echo '<p style="color: red;">Error Retrieving Education!</p>';
			else
			{
				$educationData = $procedureCall->fetchAll(PDO::FETCH_OBJ);
				$procedureCall->closeCursor();
				//Print out all of the listings.
				foreach( $educationData as $educationListing )
					echo '<p>' . $educationListing->DegreeLevel . ' in ' . $educationListing->Major . ' from ' . $educationListing->SchoolName . '</p>';
			}
			echo '<td><p>' . $user->RegisterDate . '</p></td>' . "\n";
			echo '</td>';
			echo '</tr>';
		}
	echo '</tbody></table>' . "\n";
	}
}
?>