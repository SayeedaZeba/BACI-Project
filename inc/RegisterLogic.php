<?php
//[][][]--Load required files--[][][]//
require_once("functions.php");
require_once("mail/mail.class.php");
require_once("educationOBJ.php");

//[][][]--Variable Initialization--[][][]//

//----====Field inputs====----//
//--Login Info--//
$email = "";
$confirmEmail = "";
$password = "";
$confirmPassword = "";
//--Personal Info--//
$firstname = "";
$middlename = "";
$lastname = "";
$birthYear = "";
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
$education = array();
array_push($education, new Education());
array_push($education, new Education());
array_push($education, new Education());
//--Other Info--//
$mentorormentee = "";
$workstatus = "";
$employer = ""; 
$field = ""; 
$resumeurl= "";
$imageurl = ""; 

//----====Internal Logic variables====----//
//--User-related variables derived from internal logic--//
$code = "";
$msg = "";
$seekingmentor = 0;
$seekingmentee = 0;
$adminLoggedIn = FALSE;
$hashpassword = "";

//--Check variables--//
//-Login info-/
$checke = false;
$checkp = false;
//-Personal Info-//
$checkname = false;
$checkBirth = false;
$checkLocation = false;
//the following fields are not required, so assume they're good until you get a bad input.
$checkSocialmedia = true;
$checkPhone = true;
$checkZip = true;
//-Other Info-//
$checkmentor = false;
$checkWork = true; //Only false if working professional & failed to specify field & employer.
$userCreated = false;
$checkiurl = false;
$checkrurl = false;

//-Checkbox-//
$checkBox = false;
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


//[][][]--Get Inputs from POST--[][][]//
if(isset($_POST['submit']))
{
	//--Login Info--//
	if(isset($_POST['vEmail']))				$email= sanitize($_POST['vEmail']);
	if(isset($_POST['vConfirmEmail']))		$confirmEmail= sanitize($_POST['vConfirmEmail']);
	if(isset($_POST['vPassword']))			$password=sanitize($_POST['vPassword']);
	if(isset($_POST['vConfirmPassword']))	$confirmPassword=sanitize($_POST['vConfirmPassword']);
	//--Personal Info--//
	if(isset($_POST['vFirstName']))			$firstname = sanitize($_POST['vFirstName']);
	if(isset($_POST['vMiddleName']))		$middlename = sanitize($_POST['vMiddleName']);
	if(isset($_POST['vLastName']))			$lastname = sanitize($_POST['vLastName']);
	if(isset($_POST['vBirthYear']))			$birthYear = sanitize($_POST['vBirthYear']);
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
	if(isset($_POST['vDegree']))			$education[0]->degree = sanitize($_POST['vDegree']);
	if(isset($_POST['vDegree2']))			$education[1]->degree = sanitize($_POST['vDegree2']);
	if(isset($_POST['vDegree3']))			$education[2]->degree = sanitize($_POST['vDegree3']);

	if(isset($_POST['vSchool']))			$education[0]->school = sanitize($_POST['vSchool']);
	if(isset($_POST['vSchool2']))			$education[1]->school = sanitize($_POST['vSchool2']);
	if(isset($_POST['vSchool3']))			$education[2]->school = sanitize($_POST['vSchool3']);

	if(isset($_POST['vMajor']))				$education[0]->major = sanitize($_POST['vMajor']);
	if(isset($_POST['vMajor2']))			$education[1]->major = sanitize($_POST['vMajor2']);
	if(isset($_POST['vMajor3']))			$education[2]->major = sanitize($_POST['vMajor3']);

	if(isset($_POST['vGradYear']))			$education[0]->gradyear = sanitize($_POST['vGradYear']);
	if(isset($_POST['vGradYear2']))			$education[1]->gradyear = sanitize($_POST['vGradYear2']);
	if(isset($_POST['vGradYear3']))			$education[2]->gradyear = sanitize($_POST['vGradYear3']);

	//--Other info--//
	if(isset($_POST['vMentorOrMentee']))	$mentorormentee = sanitize($_POST['vMentorOrMentee']);
	if(isset($_POST['vIdentity']))			$workstatus = sanitize($_POST['vIdentity']); 
	if(isset($_POST['vEmployer']))			$employer = sanitize($_POST['vEmployer']);
	if(isset($_POST['vField']))				$field = sanitize($_POST['vField']);
	
	//--Checkbox--//
	if(isset($_POST['vAgreeToTerms']))				$checkBox = true;

}


//[][][]--Input Validation and Messages--[][][]//
if(isset($_POST['submit']))
{

	//--Login info--//
	//-Email-//
	if(empty($email))
		$emailBlank = '<p style = "color: red">Please enter an email address.</p>';
	else
	{
		if($email != $confirmEmail)
			$emailBlank = '<p style= "color: red">Email and confirmation email must match.</p>';
		else
		{
			$checke  = emailchecker($email);
			if($checke == false)
			$emailBlank = '<p style= "color: red">Invalid Email</p>';
		}
	}
	//-Password-//
	if(empty($password))
		$passwordBlank = '<p style= "color: red">Enter a password.</p>';
	else
		$checkp = passwordCheck($password, $confirmPassword, 8, $feedback);
	
	if($checkp == false)
		$passwordBlank = '<p style= "color: red">Password should contain at least 8 characters including both letters and numbers</p>';
	else
		//Hash the password if it is valid.
		$hashpassword=passHash($password);
		
	//--Personal Information--//
	//-Name-//
	if(!empty($firstname) and !empty($lastname))
		$checkname = true;
	else
		array_push($feedback, '<p style = "color: red">Enter a first and last name.</p>');
	//Birth year
	if(!empty($birthYear))
		$checkBirth = true;
	else
		array_push($feedback, '<p style = "color: red">Please enter a birth year.</p>');
	//-Phone-//
	if(!empty($phone))
		$checkPhone = checkPhone($phone);
	if(!$checkPhone)
		array_push($feedback, '<p style = "color: red">Error, phone number must be in format +##(###)###-#### or (###)###-####</p>');
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
	if($mentorormentee == "Mentor")
	{
		$checkmentor = true;
		$seekingmentor = 0;
		$seekingmentee = 1;
	}
	else if($mentorormentee == "Mentee")
	{
		$checkmentor = true;
		$seekingmentor = 1;
		$seekingmentee = 0;
	}
	else if($mentorormentee == "Neither")
	{
		$checkmentor = true;
		$seekingmentor = 0;
		$seekingmentee = 0;
	}
	else if(empty($mentorormentee))
	{
		
		array_push($feedback, '<p style = "color: red">Select a mentorship/menteeship preference.</p>');
	}
	
	//--Work status--//
	if($workstatus == "employed")
		if( empty($field) or empty($employer) )
		{
			$checkWork = false;
			array_push($feedback, '<p style = "color: red">Employer & Field are required for working professionals.</p>');
		}
	
	//--Checkbox--//
	if (!$checkBox)
		$agreeBlank = '<p style="color: red">Must agree to continue</p>';
		
	//--Internal validations--//
	//-Administrative Validation-//
	if(isset($_SESSION['admin']))
		$adminLoggedIn = TRUE;
	if(isset($_GET['adminMode']) and $adminLoggedIn)
	{
		if($_GET['adminMode'] == "1" and $_SESSION['admin']->isSuperAdmin())
			$adminMode = TRUE;
	}

	//--Final account validity check--//
	//Image/resume upload is also included here to avoid excessive image uploads
	//(Why upload files for an account that won't be created?)
	if($checke and $checkp and $checkname and $checkBirth and $checkPhone and $checkLocation and $checkZip and $checkSocialmedia and $checkmentor and $checkWork and $checkBox)
	{
		//--Resume and Picture uploads--//
		if(isset($_FILES['vimage']))			$imageurl = uploadimage($email);
		if(isset($_FILES['vresume']))			$resumeurl = uploadresume($email);
		
		//--Image url check--//
		if($imageurl == "")
			array_push($feedback, '<p style = "color: red">Invalid image file / Couldn\'t upload</p>');
		else
			$checkiurl = true;
		//--Resume url check--//
		if($resumeurl == "")
			array_push($feedback, '<p style = "color: red">Invalid resume file/ Couldn\'t upload</p>');
		else
			$checkrurl =  true;
		
		//If the image upload was successful, go ahead and mark the account as valid.
		if($checkiurl and $checkrurl)
			$valid = true;
	}
		
}

//[][][]--Database Actions--[][][]//
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
	$procedure1 = 'Call Get_Countries()';
	$procedureResult1 = $connection->query($procedure1);
	if(!$procedureResult1) 
	{
		//Print an error if the countries cannot be fetched.
		array_push($feedback, '<p style = "color: red;">Couldn\'t get list of Countries!</p>');
	}
	else 
	{ 	
		//if call is successful, creates an array to display the list.
		$countryArray = $procedureResult1->fetchAll(PDO::FETCH_OBJ);
		$procedureResult1->closeCursor();
	}
	
	//----====Registration actions====----//
	if($valid)
	{
		//Counting the number of users with the input username
		$procedure = 'Call SP_Count_Users("' . $email . '")';
		$procedureResult = $connection->query($procedure);
		if(!$procedureResult) //if results don't return an error is displayed
		{
			array_push($feedback, '<p style = "color:red;">Error when attempting to if user exists.</p>');
		}
		else
		{
			//get the usercount.
			$row = $procedureResult->fetch(PDO::FETCH_OBJ);
			$count = $row->c;
			//Close the cursor.
			$procedureResult->closeCursor();
		}
		//checking if the user already exists
		if($count>0)
		{
			//If they already exist, issue an error telling them to log in, instead.
			array_push($feedback, '<p style = "color:red;">User already exists.  Please <a href = "login.php">log in</a> instead.</p>');
		}
		else
		{
			//If they do not yet exist, continue with registration.
			if($adminMode)
				$startActivated = 1;
			else
				$startActivated = 0;
			//Generate an activation code for the user.
			$activationcode = randomCodeGenerator(60);
			$procedure = 'call sp_add_user_v2("'.$email.'", "'.$hashpassword.'", "'.$firstname.'", "'.$middlename.'", "'.$lastname.'", "'.$birthYear.'", "'.$phone.'", "'.$address.'", "'.$city.'", "'.$country.'", "'.$state.'", "'.$zipCode.'", "'.$gender.'", "'.$fblink.'", "'.$linkedInlink.'", "'.$twitterlink.'", "'.$seekingmentor.'", "'.$seekingmentee.'", "'.$workstatus.'", "'.$employer.'", "'.$field.'", "'.$imageurl.'", "'.$resumeurl.'", "'.$startActivated.'", "'.$activationcode.'")';
			//Send the user an email with their activation details if they are not pre-activated.

			//Attempt to create the user.
			$result = $connection->query($procedure);
			if($result === FALSE)
			{
				array_push($feedback, '<p style = color: red">Error occurred while attempting to add a new user.</p>');
				array_push($feedback, $procedure);
			}
			else
			{
				array_push($feedback, '<p>User created successfully!</p>');
				if(!$adminMode)
				{
					//sets body for Mail() function
					$body = "<h1>Thank you for registering to the BACI mentorship system!</h1>";
					$body .= '<p>Please <a href="http://yourwebsite.com/Activation.php?code='.$activationcode.'&email='.$email.'">click here</a> to activate your account. </p>';
					$subject = "BACI Mentorship System Activation";

					//calls mail function to send out the activation email, shows message if successful
					$mailer = new Mail();
					if (($mailer->sendMail($email,"BACI USER", $subject, $body))==true)
						array_push($feedback, '<p>An email with activation steps has been sent.</p>');
					//message displays an error if email does not go through
					else 
						array_push($feedback,  "<p>Error:  Activation email not sent. Please save the contents of this activation email instead." . $email.' '. $subject.' '. $body . '</p>');
				}
				else
				{
					//If a super user is logged in, inform the user that activation was skipped.
					array_push($feedback, '<p>Super-user logged in.  This account has been pre-activated.</p>');
				}
				//Try to get the user's ID.
				$procedure = 'Call SP_Get_User_ID("' . $email . '")';
				$procedureResult = $connection->query($procedure);
				if(!$procedureResult) 
				{ //if results don't return an error is displayed
					array_push($feedback, '<p style = "color: red;">Couln\'t retrieve information about newly created user!</p>');
				}
				else 
				{ //if call is successful
					$row = $procedureResult->fetch(PDO::FETCH_OBJ);
					$userid = $row->userid;
					//Close the cursor.
					$procedureResult->closeCursor();
					//Flag that a user has been created, so that calls dependent on userID can execute.
					$userCreated = true;
				}
			}
		}
	}

	//--Add educations for a newly created user--//
	if($userCreated == true)
	{
		foreach($education as $educationItem)
		{
			//Only attempt to add an education if it's filled in.
			if($educationItem->isFull())
			{
				//Adding the education details of the user to the USER_EDUCATION table
				$procedure = 'Call SP_Add_User_Education("' . $userid . '", "' . $educationItem->degree . '", "' . $educationItem->school . '", "' . $educationItem->major . '","' . $educationItem->gradyear . '");';
				$result = $connection->query($procedure);
				if($result === FALSE)
					array_push($feedback, '<p style = "color: red;">Error querying database. User Education not created</p>');
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
	}
	
}

?>