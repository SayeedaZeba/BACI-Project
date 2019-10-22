<?php  //this must be the very first line on the php page, to register this page to use session variables
  include "header.php";
	//if this is a page that requires login always perform this session verification
  require_once "inc/functions.php";
  $connection = dbConnect();
  $feedback = array();

if(!adminLoggedIn())
{
	$_SESSION['timeout'] = time();
	require_once "inc/sessionVerify.php";
}
?>

<!DOCTYPE html PUBLIC>
<html lang="EN" dir="ltr" xmlns="http://www.w3.org/1999/xhtml">
	<head>
	<title>Search Users</title>
	
	
	<!--Datatables scripts-->
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.css"/>
<script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.js"></script>
<script>
		function showHint(str, state) {
				if (str.length == 0) {
						document.getElementById("txtHint").innerHTML = "";
						return;
				} else {
						var xmlhttp = new XMLHttpRequest();
						xmlhttp.onreadystatechange = function() {
								if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
										document.getElementById("txtHint").innerHTML = xmlhttp.responseText;
								}
						}
						xmlhttp.open("GET", "inc/stateSelectorAjax.php?q=" + str + "&state=" + state + "&fromSearch=1", true);
						xmlhttp.send();
				}
		}
		</script>
	
  <!-- Stylesheet link -->
  <link href="style.css" rel="stylesheet">
	</head>

	<body>
		<br/><br/>
<?php

//----====Fetch lists====----//
	//--Countries--//
$procedure1 = 'Call Get_Countries()';
$procedureResult1 = $connection->query($procedure1);
if(!$procedureResult1) { //if results don't return an error is displayed
	array_push($feedback, '<p style = "color: red;">Couldn\'t get list of Countries!</p>');
}
else { //if call is sucessful
	//creates an array to display the list as HTML
	$countryArray = $procedureResult1->fetchAll(PDO::FETCH_OBJ);
	sortObjectsByName($countryArray);
	$procedureResult1->closeCursor();
}

//----====Fetch lists====----//
	//--Degrees--//
	$procedure = 'Call SP_Get_Degrees()';
	$procedureResult = $connection->query($procedure);
	if(!$procedureResult) 
	{
		//Print an error if the degrees cannot be fetched.
		array_push($feedback, '<p style = "color: red;">Couldn\'t get list of Degrees!</p>');
	}
	else 
	{
		//if call is successful, creates an array to display the list.
		$degreeArray = $procedureResult->fetchAll(PDO::FETCH_OBJ);
		$procedureResult->closeCursor();
	}




//Set defaults
		$firstName = "";
		$lastName = "";
		$minAge = "1";
		$maxAge = "999";
		$gender = "";
		$country = "";
		$state = "";
		$field = "";
		$fromregisterdate = "0000/00/00";
		$toregisterdate = "9999/12/31";
		$workstatus = "";
		$mentorPreferred = "";
		$seekMentor = "";
		$seekMentee = "";
		//$degree = "";
		//$school = "";
		//$major = "";
		$list='<input name="State" placeholder="*State/Province" type="text" value="">';
		
		
		if(isset($_POST['FirstName']))													$firstName = sanitize($_POST['FirstName']);
		if(isset($_POST['LastName']))													$lastName = sanitize($_POST['LastName']);
		if(isset($_POST['minAge']) and !empty($_POST['minAge']))						$minAge = sanitize($_POST['minAge']);
		if(isset($_POST['maxAge']) and !empty($_POST['minAge']))						$maxAge = sanitize($_POST['maxAge']);
		if(isset($_POST['gender']) and !empty($_POST['gender']))						$gender = sanitize($_POST['gender']);
		if(isset($_POST['Country']) and !empty($_POST['Country']))						$country = sanitize($_POST['Country']);
		if(isset($_POST['vState']) and !empty($_POST['vState']))							$state = sanitize($_POST['vState']);
		if(isset($_POST['Field']) and !empty($_POST['Field']))							$field = sanitize($_POST['Field']);
		if(isset($_POST['Fromregisterdate']) and !empty($_POST['Fromregisterdate']))	$fromregisterdate = sanitize($_POST['Fromregisterdate']);
		if(isset($_POST['Toregisterdate']) and !empty($_POST['Toregisterdate']))		$toregisterdate = sanitize($_POST['Toregisterdate']);
		if(isset($_POST['Workstatus']) and !empty($_POST['Workstatus']))				$workstatus = sanitize($_POST['Workstatus']);
		if(isset($_POST['Workstatus']) and !empty($_POST['Workstatus']))				$workstatus = sanitize($_POST['Workstatus']);
		//For mentor/mentee preference, input must be converted to match the database format.
		if(isset($_POST['MentorOrMentee']))
		{
			$mentorPreferred = sanitize($_POST['MentorOrMentee']);
			if($mentorPreferred == "Mentor")
			{
				$seekMentee = 1;
				$seekMentor = 0;
			}
			else if($mentorPreferred == "Mentee")
			{
				$seekMentee = 0;
				$seekMentor = 1;
			}
			else if($mentorPreferred == "Neither")
			{
				$seekMentee = 0;
				$seekMentor = 0;
			}
		}

		//If they're both selected, load up the state from user preferences.
		if(!empty($country))
			echo "<script>document.onload = showHint(". $country . "," . $state .");</script>";
		
?>
	<div id = "main">
	<div class="container containerinput" style="margin-top:15px">
	<button class = "btn btn-primary btn-lg btn-block" data-toggle="collapse" href = "#search">Click here to view/hide options to refine your search</button>
		<div id = "search" class = "collapse">
				<form action = "" id="form" method="post" name="form" >
				<label>First Name</label><input type ="text" size = "30px" placeholder = "First Name" name = "FirstName" value = "<?php echo $firstName;?>"/><br/>
				<label>Last Name</label><input type ="text" placeholder = "Last Name" name = "LastName" value = "<?php echo $lastName;?>"/><br/>
				<label>Minimum Age</label><input type ="text" placeholder = "Minimum Age" name = "minAge" value ="<?php if($minAge != "1") echo $minAge;?>"/><br/>
				<label>Maximum Age</label><input type ="text" placeholder = "Maximum Age" name = "maxAge" value ="<?php if($maxAge != "999") echo $maxAge;?>"/><br/>
				<label>Gender</label>
				<select name = 'gender' id = "gender" >
							<option value="" selected >Any Gender</option>
							<option value = "Male">Male</option>
							<option value = "Female">Female</option>
							<option value = "Other">Other</option>
				</select><br/>
				<label>Country</label>
				<select id="country" name="Country" onchange="showHint(this.value)">
					<option value="" selected>Any Country</option>
					<?php
					//runs for each loop to get the country value for each country from the array
					foreach ($countryArray as $value) {
						if($value->ID == $country)
							print '<option value="'.$value->ID.'" selected = "selected">'.$value->Name."</option>" . "<br />";
						else
							print '<option value="'.$value->ID.'">'.$value->Name."</option>" . "<br />";
					}
					?>
				</select><br/>
				<label>State</label>			
				<span  id="txtHint">
					 <?php print $list; ?>
				</span><br/>
				
				<label>Field</label><input type ="text" placeholder = "Field" name = "Field" id = "Field" value ="<?php echo $field;?>"/> <br/>
				<label>Register Date From: </label><input type ="text" placeholder = "Register Date (YYYY-MM-DD)" name = "Fromregisterdate" id = "Fromregisterdate" value ="<?php if($fromregisterdate != "0000/00/00") echo $fromregisterdate;?>"/> <br/>
				<label>Register Date To: </label><input type ="text" placeholder = "Register Date (YYYY-MM-DD)" name = "Toregisterdate" id = "Toregisterdate" value ="<?php if($toregisterdate != "9999/12/31")echo $toregisterdate;?>"/> <br/>
				<label>Select Work Status</label><select id="Workstatus" name="Workstatus">
					<option value="" selected>No Work Status Preference</option>
					<option value="student" <?php if($workstatus == "student") echo 'selected="selected"';?>>Student</option>
					<option value="employed" <?php if($workstatus == "employed") echo 'selected="selected"';?>>Working Professional</option>
				</select><br/>
				  <label>Mentorship preference</label><select id="mentorOrMentee" name="MentorOrMentee">
					<option value="" selected>Do not filter by preference</option>
					<option value="Mentor" <?php if($mentorormentee == "Mentor") echo 'selected="selected"';?>>Mentor</option>
					<option value="Mentee" <?php if($mentorormentee == "Mentee") echo 'selected="selected"';?>>Mentee</option>
					<option value="Neither" <?php if($mentorormentee == "Neither") echo 'selected="selected"';?>>Neither</option>
				</select>
				
				<?php
				if(adminLoggedIn())
					if($_SESSION['admin']->isAtLeastCoordinator())
					{
						echo'<button class = "btn-lg btn-block btn-primary" name ="CSV" value = "getCSV">Download Data to CSV</button><br/>';
					}
				?>
				<br/> <button type ="submit" class = "btn-lg btn-block">Submit</button> </center>
			</form>
		</div>
	</div>	
	</div>
		<?php
		//Make sure the database connection was succesful.
					
		if($connection === FALSE)
			array_push($feedback, '<p style = "color: red;">Cannot connect to database.</p>');
		else
		{
			//Create a prepared statement
			//This prints a datatable based on the inputs provided by the user
			$statementText = "SELECT * FROM READABLE_ACTIVATED_USERS WHERE FirstName LIKE CONCAT(?, '%') AND LastName LIKE CONCAT(?,'%') 
			AND CountryID LIKE CONCAT(?,'%') AND StateProvinceID LIKE CONCAT(?,'%')AND ApproxAge BETWEEN ? AND ? AND Gender LIKE CONCAT(?,'%')
			AND Field LIKE CONCAT(?,'%') AND RegisterDate BETWEEN ? AND ? AND WorkStatus LIKE CONCAT(?,'%') AND SeekingMenteeship LIKE CONCAT(?,'%') AND SeekingMentorship LIKE CONCAT(?,'%');";
			$statement = $connection->prepare($statementText);
			$successCheck = $statement->execute(array($firstName,$lastName,$country,$state,$minAge,$maxAge,$gender,$field,$fromregisterdate,$toregisterdate,$workstatus,$seekMentee,$seekMentor));
			if($successCheck === FALSE)
				array_push($feedback, '<p style = "color: red;">Could not retrieve data.</p>');
			else
			{
				//Fetch all of the user's data, then close the cursor.
				$users = $statement->fetchAll(PDO::FETCH_OBJ);
				$statement->closeCursor();
				require_once("inc/build_table.php");
				echoUserTable($connection, $users, "user", $feedback);
				//Download the CSV if specified to do so by the user.
				if(adminLoggedIn())
					if($_SESSION['admin']->isAtLeastCoordinator())
						if(isset($_POST['CSV']) and $_POST['CSV']== 'getCSV')
						{
								$output = fopen('searchResult.csv', 'w');
								fputcsv($output, array('Email','FirstName','MiddleName','LastName','Gender','BirthYear','ApproxAge','ZipCode','Phone','Address','City','StateProvinceFromID','Country','Prefers to be mentee','Prefers to be mentor','WorkStatus','Employer','Field','FacebookURL','LinkedInURL','TwitterURL','RegisterDate'));
								foreach($users as $user)
									fputcsv($output, array($user->Email, $user->FirstName, $user->MiddleName, $user->LastName, $user->Gender, $user->BirthYear, $user->ApproxAge, $user->ZipCode, $user->Phone, $user->Address, $user->City, $user->StateProvinceFromID, $user->Country, $user->SeekingMentorship, $user->SeekingMenteeship, $user->WorkStatus, $user->Field, $user->Employer, $user->FacebookURL, $user->LinkedInURL, $user->TwitterURL, $user->RegisterDate));
								fclose($output);
								echo '<script>window.open("searchResult.csv")</script>';
						}
			}
			
			
		}
		
		//Print feedback.
		foreach( $feedback as $item )
			echo $item;
			
		?>
		
		<p><a href = "HomePage.php">Return to Homepage</a></p>

		<br/><br/>
	</body>
</html>

