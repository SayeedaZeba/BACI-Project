<?php
//[][][]--Load required files--[][][]//
require_once("functions.php");
require_once("mail/mail.class.php");
require_once("educationOBJ.php");

//[][][]--Variable Initialization--[][][]//

//----====Field outputs====----//
//--Personal Info--//
$firstname = "";
$middlename = "";
$lastname = "";
$phone = ""; //Format +**(###)###-#### where ** is an optional input.
$address = "";
$city = "";
$country = "";
$state = "";
$zipCode = ""; //Format ****-#####
$gender = "";
//--Social Media Links--//
$fblink = "";
$linkedInlink = "";
$twitterlink = "";
//--Education--//
$oldEducation = array();
$educationDeletions = array();
$newEducation = array();
$newEducation = array();
array_push($newEducation, new Education());
array_push($newEducation, new Education());
array_push($newEducation, new Education());
//--Other Info--//
$mentorormentee = "";
$workstatus = "";
$employer = ""; 
$field = ""; 
$resumeurl= "";
$imageurl = "";
$userID = "";

//----====Internal Logic variables====----//
//--User-related variables derived from internal logic--//
$code = "";
$msg = "";
$seekingmentor = 0;
$seekingmentee = 0;
$adminLoggedIn = FALSE;
$hashpassword = "";

//--Check variables--//
//-Personal Info-//
$checkname = false;
$checkPhone = true; //Phone is not required, so assume it's good, until you get a bad input.
$checkZip = true; //Assume zip is good, in case no zip is entered.
$checkLocation = false;
$checkSocialMedia = true;
//-Other Info-//
$checkMentorChange = false; //Assume mentorship didn't change untill proven wrong
$checkmentor = true; // Assume mentorship check is good until proven wrong.
$userCreated = false;
$checkiurl = true; //In case of no new image submission, assume true.
$checkrurl = true; //In case of no new resume submission, assume true.
//-Checkbox-//
$checkBox = false;
$checkWork = true;
//-internal checks-//
$valid = false;
$adminMode = FALSE;
$checkDB = false;
//--ID Variables--//
$userid = 0;
$count = 0;

//--Input/Output--//
$feedback = array();
$emailBlank = "";
$passwordBlank = "";
$firstBlank = "";
$lastBlank = "";
$agreeBlank = "";
$connection = dbConnect();

$list='<input name="vState" placeholder="*State/Province (Select a country first)" type="text" value="" Disabled>';


//[][][]--Database Retrieval Actions--[][][]//
if($connection === FALSE)
	array_push($feedback, '<p style = "color: red">  Error:  Database connection failed. </p>');
else
	$checkDB = true;
if($checkDB)
{
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
	//--Countries--//
	//calling the list of countries from the database for the dropdown list
	$procedure = 'Call Get_Countries()';
	$procedureResult = $connection->query($procedure);
	if(!$procedureResult) 
	{
		//Print an error if the countries cannot be fetched.
		array_push($feedback, '<p style = "color: red;">Couldn\'t get list of Countries!</p>');
	}
	else 
	{ 	
		//if call is successful, creates an array to display the list.
		$countryArray = $procedureResult->fetchAll(PDO::FETCH_OBJ);
		sortObjectsByName($countryArray);
		$procedureResult->closeCursor();
	}
	
	//----====Fetch user data====----//

	//-Administrative Validation-//
	//this must happen before fetching any data
	if(isset($_SESSION['admin']))
		$adminLoggedIn = TRUE;
	if(isset($_GET['adminMode']) and $adminLoggedIn)
	{
		if($_GET['adminMode'] == "1" and $_SESSION['admin']->isAtLeastAdmin())
			$adminMode = TRUE;
	}

	//Depending on whether we're in admin mode or not, the email will come from the session, or GET
	if($adminMode)
	{
		//If we're in admin mode, get an email from GET
		if(isset($_GET['email']))	$email = $_GET['email'];
		else	array_push($feedback, '<p style = "color: red;">No user selected for editing!</p>');
	}
	else
	{
		//Otherwise, get it from the session variable.
		if(!isset($_SESSION['uid']))
			array_push($feedback, '<p style = "color: red;">No user logged in!</p>');
		else
			$email = $_SESSION['uid'];
	}	
	
	if(empty($email))
		array_push($feedback, '<p style = "color: red;">User email is missing!</p>');
	else
	{
		//Initially, get data from the database.
		$procedure = 'call sp_get_userTable_data_by_email("'.$email.'");';
		$procedureCall = $connection->query($procedure);
		if(!$procedureCall)
			array_push($feedback, '<p style = "color: red;">Failed to retrieve user data!</p>');
		else
		{
			//--==Assign data to variables==--//
			$userInfo = $procedureCall->fetch(PDO::FETCH_OBJ);
			$procedureCall->closeCursor();
			//--Personal information--//
			$firstname = $userInfo->FirstName;
			$middlename = $userInfo->MiddleName;
			$lastname = $userInfo->LastName;
			$phone = $userInfo->Phone;
			$address = $userInfo->Address;
			$city = $userInfo->City;
			$state = $userInfo->StateProvinceID;
			$country = $userInfo->CountryID;
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
			$imageurl = $userInfo->ImageURL;
			$resumeurl = $userInfo->ResumeURL;
			$userid = $userInfo->ID;
			//Some special routines must run for mentorship status to default to the correct selection.
			if($seekingmentor == 1)
				$mentorormentee = "Mentee";
			else if($seekingmentee == 1)
				$mentorormentee = "Mentor";
			else
				$mentorormentee = "Neither";
			//Need the user's ID for mentor/mentee history changes.
			$userID = $userInfo->ID;
			
			//--Education info--//
			//Must be retrieved in a separate query.
			$procedure = 'call sp_get_all_education_by_email("' . $email . '");';
			$procedureCall = $connection->query($procedure);
			if(!$procedureCall)
				echo '<p style="color: red;">Error Retrieving Education!</p>';
			else
			{
				$oldEducation = $procedureCall->fetchAll(PDO::FETCH_OBJ);
				$procedureCall->closeCursor();
				$success = true;
			}
			
		}
		if(isset($_POST['submit']))
		{
			//If POST is set, replace data with it.
			//--Personal Info--//
			if(isset($_POST['vFirstName']))			$firstname = sanitize($_POST['vFirstName']);
			if(isset($_POST['vMiddleName']))		$middlename = sanitize($_POST['vMiddleName']);
			if(isset($_POST['vLastName']))			$lastname = sanitize($_POST['vLastName']);
			if(isset($_POST['vPhoneDigits']))		$phone = sanitize($_POST['vPhoneDigits']);
			if(isset($_POST['vAddress']))			$address = sanitize($_POST['vAddress']);
			if(isset($_POST['vCity']))				$city = sanitize($_POST['vCity']);
			if(isset($_POST['vCountry']))			$country = sanitize($_POST['vCountry']);
			if(isset($_POST['vState']))				$state = sanitize($_POST['vState']);
			if(isset($_POST['vZip']))				$zipCode = sanitize($_POST['vZip']);
			if(isset($_POST['vGender']))			$gender = sanitize($_POST['vGender']);
			//--Social Media Links--//
			if(isset($_POST['vFB']))				$fblink = sanitize($_POST['vFB']);
			if(isset($_POST['vLinkedIn']))			$linkedInlink = sanitize($_POST['vLinkedIn']);
			if(isset($_POST['vTwitter']))			$twitterlink = sanitize($_POST['vTwitter']);
			//--Education--//
			if(isset($_POST['vDegree']))			$newEducation[0]->degree = sanitize($_POST['vDegree']);
			if(isset($_POST['vDegree2']))			$newEducation[1]->degree = sanitize($_POST['vDegree2']);
			if(isset($_POST['vDegree3']))			$newEducation[2]->degree = sanitize($_POST['vDegree3']);

			if(isset($_POST['vSchool']))			$newEducation[0]->school = sanitize($_POST['vSchool']);
			if(isset($_POST['vSchool2']))			$newEducation[1]->school = sanitize($_POST['vSchool2']);
			if(isset($_POST['vSchool3']))			$newEducation[2]->school = sanitize($_POST['vSchool3']);

			if(isset($_POST['vMajor']))				$newEducation[0]->major = sanitize($_POST['vMajor']);
			if(isset($_POST['vMajor2']))			$newEducation[1]->major = sanitize($_POST['vMajor2']);
			if(isset($_POST['vMajor3']))			$newEducation[2]->major = sanitize($_POST['vMajor3']);

			if(isset($_POST['vGradYear']))			$newEducation[0]->gradyear = sanitize($_POST['vGradYear']);
			if(isset($_POST['vGradYear2']))			$newEducation[1]->gradyear = sanitize($_POST['vGradYear2']);
			if(isset($_POST['vGradYear3']))			$newEducation[2]->gradyear = sanitize($_POST['vGradYear3']);
			
			
			//--Other info--//
			if(isset($_POST['vMentorOrMentee']))	$newmentorormentee = sanitize($_POST['vMentorOrMentee']); // This needs a special value, so that we can check if it changed.
			if(isset($_POST['vIdentity']))			$workstatus = sanitize($_POST['vIdentity']); 
			if(isset($_POST['vEmployer']))			$employer = sanitize($_POST['vEmployer']);
			if(isset($_POST['vField']))				$field = sanitize($_POST['vField']);
		}
	}
}

//[][][]--Input Validation and Messages--[][][]//
if(isset($_POST['submit']))
{
	//--Personal Information--//
	//-Name-//
	if(!empty($firstname) and !empty($lastname))
		$checkname = true;
	else
		array_push($feedback, '<p style = "color: red">Enter a first and last name.</p>');
	//-Location(Address, City, Country, State)-//
	if(empty($address))
		array_push($feedback, '<p style = "color: red">Please enter an address.</p>');
	else if(empty($city))
		array_push($feedback, '<p style = "color: red">Please enter a city.</p>');
	else if(empty($country))
		array_push($feedback, '<p style = "color: red">Please select a country.</p>');
	//else if(empty($state))
	//	array_push($feedback, '<p style = "color: red">Please select a state.</p>');
	else
		$checkLocation = true;
	//-Phone-//
	if(!empty($phone))
		$checkPhone = checkPhone($phone);
	if(!$checkPhone)
		array_push($feedback, '<p style = "color: red">Error, phone number must be in format +##(###)###-#### or (###)###-####</p>');
	//-Zip-//
	if(!empty($zipCode))
		$checkZip = checkZip($zipCode);
	if(!$checkZip)
		array_push($feedback, '<p style = "color: red">Error, Zip must be in format ##### or ####-#####</p>');
	//-Social Media-//
	//Omit the "com", due to international domains.
	if(!empty($fblink))
	{
		$fblink = socialMediaCheck("www.facebook.", $fblink);
		if($fblink === FALSE)
		{
			$checkSocialmedia = false;
			array_push($feedback, '<p style = "color: red">Error:  Facebook link is invalid</p>');
		}
	}
	if(!empty($linkedInlink))
	{
		$linkedInlink = socialMediaCheck("www.linkedin.", $linkedInlink);
		if($linkedInlink === FALSE)
		{
			$checkSocialmedia = false;
			array_push($feedback, '<p style = "color: red">Error:  LinkedIn link is invalid</p>');
		}
	}
	if(!empty($twitterlink))
	{
		$twitterlink = socialMediaCheck("www.twitter.", $twitterlink);
		if($twitterlink === FALSE)
		{
			$checkSocialmedia = false;
			array_push($feedback, '<p style = "color: red">Error:  Twitter link is invalid</p>');
		}
	}
	
	//--Other information--//
	//-Mentorship-//
	//Check to see if mentorship status is about to change.
	if($mentorormentee != $newmentorormentee)
	{
		$checkMentorChange = true;
		$mentorormentee = $newmentorormentee;
	}
	if($mentorormentee == "Mentor")
	{
		$seekingmentor = 0;
		$seekingmentee = 1;
	}
	else if($mentorormentee == "Mentee")
	{
		$seekingmentor = 1;
		$seekingmentee = 0;
	}
	else if($mentorormentee == "Neither")
	{
		$seekingmentor = 0;
		$seekingmentee = 0;
	}
	else if(empty($mentorormentee))
	{
		$checkmentor = false;
		array_push($feedback, '<p style = "color: red">Select a mentorship/menteeship preference.</p>');
	}
	
	//--Work status--//
	if($workstatus == "employed")
		if( empty($field) or empty($employer) )
		{
			$checkWork = false;
			array_push($feedback, '<p style = "color: red">Employer & Field are required for working professionals.</p>');
		}

	//--Final account validity check--//
	//Image/resume upload is also included here to avoid excessive image uploads
	//(Why upload files for an account that won't be created?)
	if($checkname and $checkPhone and $checkLocation and $checkZip and $checkmentor and $checkWork)
	{
		//--Resume and Picture uploads--//
		if(isset($_FILES['vimage']))			$newimageurl = uploadimage($email);
		if(isset($_FILES['vresume']))			$newresumeurl = uploadresume($email);
		
		//--Image url check--//
		if($newimageurl != "__NO FILE__" and $imageurl != "")
			$imageurl = $newimageurl;
		if($imageurl == "" and $newimageurl != "__NO FILE__")
			array_push($feedback, '<p style = "color: red">Invalid image file / Couldn\'t upload</p>');
		else
			$checkiurl = true;
		//--Resume url check--//
		if($newresumeurl != "__NO FILE__" and $imageurl != "")
			$resumeurl = $newresumeurl;
		if($resumeurl == "" and $newresumeurl != "__NO FILE__")
			array_push($feedback, '<p style = "color: red">Invalid resume file/ Couldn\'t upload</p>');
		else
			$checkrurl =  true;
		
		//If the image upload was successful, go ahead and mark the account as valid.
		if($checkiurl and $checkrurl)
			$valid = true;
	}
}

//[][][]--Database Submit Actions--[][][]//
if($checkDB)
{
	//----====Submit user edits====----//
	if($valid and isset($_POST['submit']))
	{
		//Commit changes to the USER table.
		$procedure = 'call sp_edit_user("' . $email . '","' . $firstname . '","' . $middlename . '","' . $lastname . '","' . $phone . '","' . $address . '","' . $city . '","' . $state . '","' . $country . '","' . $zipCode . '","' . $gender . '","' . $fblink . '","' . $linkedInlink . '","' . $twitterlink . '","' . $seekingmentor . '","' . $seekingmentee . '","' . $workstatus . '","' . $employer . '","' . $field . '","' . $imageurl . '","' . $resumeurl . '");';
		$procedureResult = $connection->query($procedure);
		if(!$procedureResult)
		{
			array_push($feedback, '<p style = "color: red;">Error updating user data!</p>');
			array_push($feedback, $procedure);
		}
		else
		{
			array_push($feedback, '<p>Updates successful!</p>');
			$procedureResult->closeCursor();
		}
		//If there is a mentorship change, end all mentorships and menteeships with this user.
		//It should be safe to do this as an ordinary query, since it uses no user-specified inputs.
		//Nevertheless, the userID is sanitized for safety's sake.
		if($checkMentorChange)
		{
			$query = 'UPDATE MENTOR_HISTORY SET EndDate = CURRENT_TIMESTAMP WHERE MentorID = "' . sanitize($userID) . '" OR MenteeID = "' . sanitize($userID) . '" AND EndDate IS NULL AND RejectDate IS NULL;';
			$mentorshipUpdateSuccess = $connection->query($query);
			if($mentorshipUpdateSuccess === FALSE)
				array_push($feedback, '<p style = "color: red;">Necessary changes to mentorship status were not made!</p>');
		}
		//Commit changes to the education table
		//First, add new educations.
		foreach($newEducation as $educationItem)
		{
			//Only attempt to add an education if it's filled in..
			if($educationItem->isFull())
			{
				//Adding the education details of the user to the USER_EDUCATION table
				$procedure = 'Call SP_Add_User_Education("' . $userid . '", "' . $educationItem->degree . '", "' . $educationItem->school . '", "' . $educationItem->major . '","' . $educationItem->gradyear . '");';
				$result = $connection->query($procedure);
				if($result === FALSE)
					array_push($feedback, '<p style = color: red">Error querying database. User Education not created</p>');
				else
					array_push($feedback, '<p>User  Education Added successfully!</p>');
			}
			else
			{
				//If the data isn't full, but it's also not empty, warn the user
				//That the entry wasn't put in due to missing data.
				if(!$educationItem->isEmpty())
					array_push($feedback, '<p style = "color: red;">A user education was not added due to missing data.</p>');
			}
		}
	
		//Then, delete old educations.
		foreach($oldEducation as $educationItem)
		{
			if(isset($_POST['EducationItem'.$educationItem->RecordID]))
				if($_POST['EducationItem'.$educationItem->RecordID] == $educationItem->RecordID)
				{
					$statementText = 'DELETE FROM USER_EDUCATION WHERE ID = ?;';
					$statement = $connection->prepare($statementText);
					$successCheck = $statement->execute(array($educationItem->RecordID));
					if(!$successCheck)
						array_push($feedback, '<p style = color: red">Error querying database. Failed to delete education</p>');
					else
						array_push($feedback, '<p style = color: red">User Education deleted successfully!</p>');
					
				}
		}
		
	}
}


?>