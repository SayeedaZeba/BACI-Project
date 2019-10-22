
<?php
//Don't try to do this for users that aren't admins.
require_once("inc/functions.php");
require_once("inc/adminOBJ.php");
session_start();
if(adminLoggedIn() and $_SESSION['admin']->isAtLeastCoordinator())
{
	// Database Connection
	//require("db.php");
	$servername = "_";			//add your servername here 
	 
	$username = "_";			//add your username here 
	 
	$password = "_";			//add your password here 
	 
	$db = "_";					//add the name of you database here 

	$conn = mysqli_connect($servername, $username, $password,$db);
	// Check connection
	 
	if (!$conn) {
	 
	   die("Connection failed: " . mysqli_connect_error());
	 
	}
	 
	// Get Users from Downloadable users view.
	$query = "SELECT * FROM DOWNLOADABLE_USERS;";
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
	header('Content-Disposition: attachment; filename=Users.csv');
	$output = fopen('php://output', 'w');
	fputcsv($output, array('ID', 'Username','Email','FirstName','MiddleName','LastName','Gender','BirthYear','ZipCode','Phone','Address','City','StateProvinceID','StateProvince','CountryID','SeekingMentorship',' SeekingMenteeship','WorkStatus','Employer','Field','ResumeURL','ImageURL','FacebookURL','LinkedInURL','TwitterURL','Activated','RegisterDate','Country','StateProvinceFromID','ApproxAge'));
	 
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