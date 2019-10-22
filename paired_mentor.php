
<?php
//Don't try to do this for users that aren't coordinators.
require_once("inc/functions.php");
require_once("inc/adminOBJ.php");
session_start();
if(adminLoggedIn() and $_SESSION['admin']->isAtLeastCoordinator())
{
	 
	// Database Connection

	$servername = "_";			//add your servername here
	 
	$username = "_";			//add your username here
	 
	$password = "_";			//add your password here
	 
	$db = "_";					//add name of your database here

	$conn = mysqli_connect($servername, $username, $password,$db);
	// Check connection
	 
	if (!$conn) {
	 
	   die("Connection failed: " . mysqli_connect_error());
	 
	}
	 
	// get Users
	$query = "SELECT * FROM READABLE_MENTOR_HISTORY";
	if (!$result = mysqli_query($conn, $query)) {
		exit(mysqli_error($conn));
	}
	 
	$users = array();
	if (mysqli_num_rows($result) > 0) {
		while ($row = mysqli_fetch_assoc($result)) {
			$users[] = $row;
		}
	}
	 
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename=paired_mentor.csv');
	$output = fopen('php://output', 'w');
	fputcsv($output, array('RecordID', 'MenteeID','MenteeEmail','MenteeFirstName','MenteeLastName','MenteePhone','MenteeAddress','MenteeCity','MenteeStateProvince','MenteeCountry','MentorID',' MentorEmail','MentorFirstName','MentorLastName','MentorPhone','MentorAddress','	MentorCity','MentorStateProvince','MentorCountry','StartDate','EndDate','RejectDate','RequestDate','RequesterEmail'));
	 
	if (count($users) > 0) {
		foreach ($users as $row) {
			fputcsv($output, $row);
		}
	}
}
else
{
	echo 'Insufficient privileges';
}
?>