<?php  //this must be the very first line on the php page, to register this page to use session variables
  include "header.php";
	//if this is a page that requires login always perform this session verification
  require_once "inc/functions.php";
  $connection = dbConnect();
  $feedback = array();


$_SESSION['timeout'] = time();
  require_once "inc/sessionVerify.php";
?>

<!DOCTYPE html PUBLIC>
<html lang="EN" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>Search for Mentorships</title>

	<!--Datatables scripts-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css"/>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
	
  <!-- Stylesheet link -->
  <link href="style.css" rel="stylesheet">

	</head>

	<body>


		<br/><br/>

		<?php
		//Make sure the database connection was succesful.
		if($connection === FALSE)
			array_push($feedback, '<p style = "color: red;">Cannot connect to database.</p>');
		else
		{
			require_once("inc/display_mentorships.php");
			//Figure out if the user is seeking mentorship or menteeship
			//Get the user's email from the session variable.
			$email = $_SESSION['uid'];
			//Create flags for the user's mentor and mentee status.
			$mentor = FALSE;
			$mentee = FALSE;
			$procedure = 'call sp_get_userTable_data_by_email("' . $email . '");';
			$procedureCall = $connection->query($procedure);
			
			if(!$procedureCall)
				array_push($feedback,'<p style ="color:red;">Query Error.</p>');
			else
			{
				//Get the user's data and close the cursor.
				$user = $procedureCall->fetch(PDO::FETCH_OBJ);
				$procedureCall->closeCursor();
				//(Seeking mentorship is interpreted to mean they're looking for a mentor.)
				$mentee = $user->SeekingMentorship;
				//(Seeking menteeship is interpreted to mean they're looking for a mentee.)
				$mentor = $user->SeekingMenteeship;
			}
		
			if($mentor)
				//If they're a mentor, get a mentee list.
				require_once("inc/get_mentee_list.php");
			else if($mentee)
				//If they're a mentee, get a mentor list.
				require_once("inc/get_mentor_list.php");
			else
				//if they're neither, print an error.
				array_push($feedback, '<p>You are currently not registered as a mentor or mentee.  Please change your status.</p>');
		}
		
		//Print feedback.
		foreach( $feedback as $item )
			echo $item;
			
		?>
		
		<p><a href = "HomePage.php">Return to Homepage</a></a>

		<br/><br/>
	</body>
</html>
