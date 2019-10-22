<!-- Page generates a table of all of the pending relationhips
Pending relationships can be defined as relationships in the relationship history table
that don't have a start date.  Approving the relationship adds a start date to the table
rejecting the relationship adds a start, end, and reject date, which are all the same -->


<?php include "header.php";
$_SESSION['timeout'] = time();
$_SESSION['prevPage'] = 'pending';
  require_once "inc/sessionVerifyAdmin.php";
?>

<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Pending Mentor/Mentee Pairs</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
		<link rel="stylesheet" href='https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css'/>
		<script src="https://code.jquery.com/jquery-3.3.1.js"></script>
		<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
		<script>
		$(document).ready(function() { $('#user_table').DataTable(); } );
		</script>
	</head>

	<body>

		<div id = "main">
			<?php

			//Create required variables.
			$feedback = array();
			$procedureResult;
			$html = array();
	   	$url = "inc/modifyMentorship.php";

			//Only attempt to add Countries if there's an admin logged in.
			if(adminLoggedIn() and $_SESSION['admin']->isAtLeastCoordinator())
			{
				$connection = dbConnect();
				if($connection === FALSE)
				{
					array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
				}
				else
				{
					//Generate a list of Mentor and Mentee Pairs

          $procedure = 'SELECT * FROM READABLE_MENTOR_HISTORY WHERE StartDate is NULL AND RejectDate IS NULL';
    			$procedureCall = $connection->query($procedure);
    			if(!$procedureCall)
    				array_push($feedback,'<p style ="color:red;">Query Error.</p>');
    			else
    			{
    				//Get the user's data and close the cursor.
            $html = array(); //creats an array to display the list as HTML
						while($row = $procedureCall->fetch(PDO::FETCH_ASSOC)) {
						//pushes each row into the array
						array_push($html, '<tr>  <td> <a href = "' . $url . '?code=approve&mentee=' . $row["MenteeEmail"] . '&mentor=' . $row["MentorEmail"] . '&historyID=' . $row["RecordID"] . '"> Approve </a> &nbsp  &nbsp <a href = "' . $url . '?code=reject&mentee=' . $row["MenteeEmail"] . '&mentor=' . $row["MentorEmail"] . '&historyID=' . $row["RecordID"] . '"> Reject </a></td>' . '<td>' . $row["MentorEmail"] .'</td>' . '<td>' . $row["MentorFirstName"] . " " . $row["MentorLastName"]. '</td>' .'<td>'. $row["MenteeEmail"] . '</td>' . '<td>'
						. $row["MenteeFirstName"] . " " . $row["MenteeLastName"] . '</td> </tr>' );
    				//(Seeking mentorship is interpreted to mean they're looking for a mentor.)
						} //close while loop
    				$procedureCall->closeCursor();
					}//close else !procedure call

				} //database connected
			}//admin logged in
			else
			{
				//If we're here, there isn't an admin logged in, or the don't have high enough privileges.
				array_push($feedback, '<p style = "color: red;">Insufficient privileges.</p>');
			}


			?>


<?php
//displays a table based on the $html array of user data
echoUserTable($html);
//function that creates a table based on procedure calls
function echoUserTable($html)
{

  $heading = "Pending Mentors/Mentee list";
  //Table subheading.  This should probably be instructional.
  $subheading = "Click on a link to approve or reject the relationship";
  //output table headings
  echo '<h1>'. $heading . '</h1>';
  echo '<p>' . $subheading . '</p>';
  //Output table data
  echo '<table id="user_table" class="display">' . "\n";
  echo '<thead>';
  echo '<tr>';
  echo '<th>Unpair Mentor/Mentee</th>';
  echo '<th>Mentor Email</th>';
  echo '<th>Mentor Name</th>';
  echo '<th>Mentee Email</th>';
  echo '<th>Mentee Name</th>';
  echo '</tr>';
  echo '</thead><tbody>';
  //loops through data from database
  foreach( $html as $value )
  {
  	//Show the user's name, email, and address.
  	echo $value;
  }
  echo '</tbody></table>' . "\n";
}
?>

		</div>
	</body>

</html>
