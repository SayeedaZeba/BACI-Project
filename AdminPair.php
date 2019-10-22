<?php include "header.php";
$_SESSION['timeout'] = time();
require_once "inc/sessionVerifyAdmin.php";
$connection = dbConnect();
?>


<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Current Mentor/Mentee Pairs</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
		<link rel="stylesheet" href='https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'/>
		<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
		<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>

    <script>
		$(document).ready(function() {

      var mentorIDJS;
      var menteeUserNameJS;

      $('#user_table').DataTable();

      $('#user_table tbody').on('click', 'tr', function() {
        menteeData = $(this).children("td").map(function() {
          return $(this).text();
        }).get();
        menteeUserNameJS = menteeData[1];
      });

      $('#user_table tbody').on('click', 'tr', function() {
        mentorData = $(this).children().children("select").map(function() {
          return $(this).val();
        }).get();
        mentorIDJS = mentorData[0];
      });

      $('button').on('click', function() {
        setTimeout(function() {
         ajaxRequest(mentorIDJS);
       },100);
      });


      function ajaxRequest(id) {
          var xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange = function() {
              if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
                  location.reload();
              }
          }
          xmlhttp.open("GET", "inc/getAdminPair.php?mentee=" + menteeUserNameJS +"&mentor="+mentorIDJS, true);
          xmlhttp.send();
      };


  }); //document.ready

</script>

	</head>
	<body>
		<div id = "main">


<?php
//Logic only occurs if there's an admin of minimum status coordinator logged in.
if(adminLoggedIn() and $_SESSION['admin']->isAtLeastCoordinator())
{
	$mentorArray = array(); //creats an array to display each mentor in a list
	$procedure = 'Call sp_get_all_potential_mentors()';
	$procedureResult = $connection->query($procedure);

	if(!$procedureResult) { //if results don't return an error is displayed
	  array_push($feedback, '<p style = "color: red;">Couldn\'t get list of Mentors from DB!</p>');
	}
	else { //if call is sucessful
	   //creats an array to display each mentor in a list
	  while($row = $procedureResult->fetch(PDO::FETCH_ASSOC)) {
	  //pushes each row into the array
	  array_push($mentorArray, '<option value="'. $row["ID"] . '">' . $row["FirstName"] . " " . $row["LastName"] . '</option>');
	  };
	  $procedureResult->closeCursor();
	}

	//Try to get the list of potential mentors.
	$procedure = 'call sp_get_all_potential_mentees()';
	$procedureCall = $connection->query($procedure);
	//Create an empty array to store the list of users.
	$users = array();
	if(!$procedureCall)
		array_push($feedback, '<p style = "color: red;">Query Error 1</p>');
	else
	{
		//Create an array of results as objects, then close the cursor.
		$users = $procedureCall->fetchAll(PDO::FETCH_OBJ);
		$procedureCall->closeCursor();
	}

	function echoUserTable($connection, $usersArray, $type, &$feedback, $array)
	{
		//Initialize heading, subheading, and link url.
		$heading = "";
		$subheading = "";
		$url = "";

		$heading = "Unpaired Mentees list";
		$subheading = "Click on a users' name to start a menteeship request with that user.";
		$url = "user.php";

		//Check if there are users on the list
		if(empty($usersArray))
			echo '<p style = "color: red;">All mentees already paired.</p>';

			echo '<h1>'. $heading . '</h1>';
			echo '<p>' . $subheading . '</p>';
			//Output a table with headings.
			echo '<script>$(document).ready( function () {$(\'#user_table\').DataTable();} );</script>';
			echo '<table id="user_table" class="display">' . "\n";
			echo '<thead>';
			echo '<tr>';
			echo '<th>Name</th>';
			echo '<th>Email</th>';
			echo '<th>Address</th>';
			echo '<th>Education</th>';
		echo '<th>Select Mentor</th>';
		echo '<th>Pair Them!</th>';
			echo '</tr>';
			echo '</thead><tbody>';
			foreach( $usersArray as $user )
			{
				//Show the user's name, email, and address.
				echo '<tr>';
				//The first name will have a GET link that uses the users's email.
				//The actual link will be based on $url, so that this function can be more generic.
				echo '<td><p> <a href = "' . $url . '?email=' . $user->Email . '">' . $user->FirstName . ' ' . $user->LastName . '</a></p></td>' . "\n";
				echo '<td><p>' . $user->Email . '</p></td>' . "\n";
				echo '<td><p>' . $user->Address . ', ' . $user->City . ', ' . $user->StateProvince . ', ' . $user->Country . '</p></td>' . "\n";
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
				echo '</td>';
		  echo '<td><select name="vMentor">';

		  foreach( $array as $mentor )
			echo $mentor;

		  '</select></td>' . "\n";

		  echo '<td><button>Pair These Two </button></td>';

				echo '</tr>';
			}
		echo '</tbody></table>' . "\n";

	}


	//Create a user table of type "mentee"
	//echo user table takes arguments (db connection, users array, table type, feedback array)
	echoUserTable($connection, $users, "mentee", $feedback, $mentorArray);
}
else
{
	array_push($feedback, '<p style = "color: red;">Insufficient privileges!</p>');
}
?>

		</div>
	</body>

</html>
