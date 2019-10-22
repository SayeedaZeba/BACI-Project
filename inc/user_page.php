<?php
function printUserPage($connection, $email)
{
	//Check if an admin is logged in.
	if(isset($_SESSION['admin']))
		$adminLoggedIn=true;
	
	$success = false;
	if($connection === FALSE)
	{
		echo '<h1 class = "alert alert-danger">Database connection failed!</h1>';
	}
	else
	{
		//--[][]Get user's data from the database[][]--//
		$procedure = 'call sp_get_userTable_data_by_email("'.$email.'");';
		$procedureCall = $connection->query($procedure);
		if(!$procedureCall)
			echo '<h1 class = "alert alert-danger">Couldn\'t retrieve user data!</h1>';
		else
		{
			//--==Assign data to variables==--//
			//--Personal information--//
			$userInfo = $procedureCall->fetch(PDO::FETCH_OBJ);
			$procedureCall->closeCursor();
			$firstname = $userInfo->FirstName;
			$middlename = $userInfo->MiddleName;
			$lastname = $userInfo->LastName;
			$age = $userInfo->ApproxAge;
			$phone = $userInfo->Phone;
			$address = $userInfo->Address;
			$city = $userInfo->City; 
			$state = $userInfo->StateProvinceFromID;
			$country = $userInfo->Country;
			$zipCode = $userInfo->ZipCode; 
			$gender = $userInfo->Gender;
			//--Social Media Links--//
			$fblink = $userInfo->FacebookURL;
			$linkedInlink = $userInfo->LinkedInURL;
			$twitterlink = $userInfo->TwitterURL;
			//--Other Info--//
			$seekingmentor = $userInfo->SeekingMentorship;
			$seekingmentee = $userInfo->SeekingMenteeship;
			$workstatus = $userInfo->WorkStatus;
			$employer = $userInfo->Employer;
			$field = $userInfo->Field;
			$registered = $userInfo->RegisterDate;
			$imageURL = $userInfo->ImageURL;
			$resumeurl = $userInfo->ResumeURL;
			//Make sure the image URL is valid.  If not, use the example image instead.
			if(!file_exists($imageURL) or empty($imageURL))
				$imageURL = "img/user/example.png";
			//Some special routines must run for mentorship status to default to the correct selection.
			if($seekingmentor == 1)
				$mentorormentee = "Mentee";
			else if($seekingmentee == 1)
				$mentorormentee = "Mentor";
			else
				$mentorormentee = "Neither";
			//Education
			//Get the mentor's education information.
			$procedure = 'call sp_get_all_education_by_email("' . $email . '");';
			$procedureCall = $connection->query($procedure);
			if(!$procedureCall)
				echo '<p style="color: red;">Error Retrieving Education!</p>';
			else
			{
				$userEducation = $procedureCall->fetchAll(PDO::FETCH_OBJ);
				$procedureCall->closeCursor();
				$success = true;
			}
		}
	}
	
	//If all the data was retrieved successfully, print the user page.
	if($success == true)
	{
		echo '<div class = "container">';
		echo '	<div class = "jumbotron">';
		//display profile image
		echo '		<img src = "'.$imageURL.'" class = "rounded float-left" style = "width: 150px; height: 150px; margin: 10px;" alt = "profile image"/>';
		//Display first name, middle name, and last name
		echo '		<h1 class = "display-4">'. $firstname . ' ' . $middlename . ' ' . $lastname . '</h1>';
		//Display mentorship status, if present.
		if($mentorormentee != "Neither")
			echo '		<h3 class = "display-7">'. $mentorormentee . ', '.$gender.', Approximate age '. $age .'</h3>';
		else
			echo '		<h3 class = "display-7">'.$gender.', Approximate age '. $age .'</h3>';
		//Display education
		foreach($userEducation as $educationItem)
			echo '		<p class = "lead">' . $educationItem->DegreeLevel . ' in ' . $educationItem->Major . ' from ' . $educationItem->SchoolName . ', Achieved ' . $educationItem->GradYear . '</p>';
		echo '		<p class = "lead">Registered ' .$registered .'</p>';
		//End large section
		echo '	</div>';
		//Social media links.
		//Only create the group if at least 1 exists.
		if(!empty($fblink) or !empty($linkedInlink) or !empty($twitterlink))
		{
			echo '		<div class = "btn-group" role = "group">' . "\n";
			if(!empty($fblink))
				echo '			<a href = "'.$fblink.'" class = "btn btn-primary">Facebook</a>' . "\n";
			if(!empty($linkedInlink))
				echo '			<a href = "'.$linkedInlink.'" class = "btn btn-info">LinkedIn</a>' . "\n";
			if(!empty($twitterlink))
				echo '			<a href = "'.$twitterlink.'" class = "btn btn-secondary">Twitter</a>' . "\n";
			echo '		</div>' . "\n";
		}
		//Email address
		echo '<h4>Email</h4><p>'.$email.'</p>';
		//Phone number
		if(!empty($phone))
			echo '<h4>Phone Number</h4><p>'.$phone.'</p>';
		//Address
		echo '<h4>Address</h4><p>'.$address.', ' .$city. ', ' . $state . ', ';
		if(!empty($zipCode))
			echo 'Zip code ' .$zipCode. ', ';
		echo $country. '</p>';
		//Work status
		if(!empty($workstatus))
			echo '<h4>Work Status</h4><p>'.$workstatus.'</p>';
		//Employer
		if(!empty($employer))
			echo '<h4>Employer</h4><p>'.$employer.'</p>';
		//Field
		if(!empty($field))
			echo '<h4>Field</h4><p>'.$field.'</p>';
		//Resume.  Only loads on homepage, or if the user is an admin.
		$showResume = false;
		if(strpos($_SERVER['PHP_SELF'],"HomePage.php") !== FALSE)
			$showResume = true;
		if(isset($adminLoggedIn) and $_SESSION['admin']->isAtLeastCoordinator())
			$showResume = true;
		if($showResume)
		{
			if(!empty($resumeurl) and file_exists($resumeurl))
				echo '<p><a class = "btn btn-primary" href = "' .$resumeurl .'" download>Download resume</a></p>';
		}
		
		//Stuff that's only visible on the user.php page.
		if(strpos($_SERVER['PHP_SELF'],"user.php") !== FALSE )
		{
			//Admin activities, such as edit/delete user.
			if(adminLoggedIn() and $_SESSION['admin']->isAtLeastAdmin())
			{
				echo '<p><a class = "btn btn-danger" href = "confirmDeleteUser.php?email='.$email.'">Delete this user</a>';
				echo '<a class = "btn btn-warning" href = "Register.php?adminMode=1&editMode=1&email='.$email.'">Edit this user</a>';
			}
			//Return to the users list.
			echo '<a class = "btn btn-success" href = "search.php">Return to users list</a></p>';
		}
		echo '</div>';
	}
}

?>