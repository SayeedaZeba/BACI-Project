<?php include "header.php";
$_SESSION['timeout'] = time();
$_SESSION['prevPage'] = 'pairs'; 
  require_once "inc/sessionVerifyAdmin.php";
?>

<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>Ended Mentor/Mentee Pairs</title>
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
			//Only attempt to view history if there's an admin logged in.
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


          $procedure = 'SELECT * FROM READABLE_MENTOR_HISTORY WHERE RejectDate is not NULL or EndDate is not Null';
    			$procedureCall = $connection->query($procedure);
    			if(!$procedureCall)
    				array_push($feedback,'<p style ="color:red;">Query Error.</p>');
    			else
    			{
    				//Get the user's data and close the cursor.
            $html = array(); //creats an array to display the list as HTML
						while($row = $procedureCall->fetch(PDO::FETCH_ASSOC)) {
						//pushes each row into the array
						array_push($html, '<tr>  <td>' . $row["RequestDate"] . '</td> <td>' . $row["RejectDate"] . '</td><td>' . $row["EndDate"] . '</td><td>' . $row["MentorEmail"] .'</td>' . '<td>' . $row["MentorFirstName"] . " " . $row["MentorLastName"] . '</td>' .'<td>'. $row["MenteeEmail"] . '</td>' . '<td>'
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
echoUserTable($html);

function echoUserTable($html)
{
	//Initialize heading, subheading
	$heading = "";
	$subheading = "";

	//Types allow this function to perform multiple functions.
	//Add new types as needed!

		//Heading.  This should be a summary of the type of table.
		$heading = "Ended/Rejected Mentors/Mentee list";
		//Table subheading.  This should probably be instructional.
		$subheading = "";
		//URL to a context-sensitive link.  For example, for admins, this could be used to link to an edit user page.
		//In this case, it's linking to a page to start a mentorship.
		//Remember, $url will always be followed by a GET value to the user's email in the current design.


	//Check if there are users on the list


		echo '<h1>'. $heading . '</h1>';
		echo '<p>' . $subheading . '</p>';
		//Output a table with headings.

		echo '<table id="user_table" class="display">' . "\n";
		echo '<thead>';
		echo '<tr>';
    echo '<th>Request Date</th>';
    echo '<th>Reject Date</th>';
    echo '<th>End Date</th>';
		echo '<th>Mentor Email</th>';
		echo '<th>Mentor Name</th>';
		echo '<th>Mentee Email</th>';
		echo '<th>Mentee Name</th>';
		echo '</tr>';
		echo '</thead><tbody>';
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
