<?php  //this must be the very first line on the php page, to register this page to use session variables
  include "header.php";
	//if this is a page that requires login always perform this session verification
  require_once "inc/functions.php";
  $connection = dbConnect();
  $feedback = array();
  $mentorData = array();
  $printMentor = FALSE;

$_SESSION['timeout'] = time();
  require_once "inc/sessionVerify.php";
?>

<!DOCTYPE html PUBLIC>
<html lang="EN" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>Mentor page</title>
  <!-- Bootstrap & Jquery scripts-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

  <!-- Stylesheet link -->
  <link href="style.css" rel="stylesheet">

	</head>

	<body>


		<br/><br/>

		<?php
		
		//Since the user's email will be transmitted by GET, we need to verify it's valid before letting the user do anything.
		if($connection === FALSE)
			array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
		else
		{
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
			
			//If the user is a mentor, get the menteeship request page.
			if($mentor)
				require_once("inc/menteeship_request_page.php");
			else if($mentee)
				//If the user is a mentee, get the mentorship request page.
				require_once("inc/mentorship_request_page.php");
			else
				//Otherwise, issue an error.
				array_push($feedback, '<p>You are currently not registered as a mentor or mentee.  Please change your status.</p>');
			
		}
			
		//Print feedback.
		foreach( $feedback as $item )
			echo $item;
		?>

		<br/><br/>
	</body>
</html>
