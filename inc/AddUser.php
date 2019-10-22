

<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>My Template</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
  <?php include "header.php"; ?>
<div id="main">


<?php
require_once("inc/functions.php");
require_once("inc/mail/mail.class.php");
include("inc/RegistrationFormPHP.php");
if(isset($_POST['submit']))
	{
		$fname=$_POST['vFirstName'];
		$lname=$_POST['vLastName'];
		$phone=$_POST['vPhone'];
		$address=$_POST['vAddress'];
		$city=$_POST['vCity'];
		$state=$_POST['vState'];
		$zip=$_POST['vZip'];
		$country=$_POST['vCountry'];
		$gener=$_POST['vGender'];
		$fblink=$_POST['vFB'];
		$linkdlink=$_POST['vLastName'];
		$twitter_link=$_POST['vTwitter'];
		$email=$_POST['vEmail'];
		$cemail=$_POST['vConfirmEmail'];
		$pass=$_POST['vPassword'];
		$cpass=($_POST['vConfirmPassword']);
		$education=$_POST['vDegree'];
		$school=$_POST['vSchool'];
		$major=$_POST['vMajor'];
		$year=$_POST['vGradYear'];

		$hostname = 'localhost';

	/*** mysql dbname ***/
	$dbname = '_';			//add the name of your database here

	/*** mysql username ***/
	$username = '_';		//add your username here

	/*** mysql password ***/
	$password = '_';		//add your password here

	
		$con = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
		$con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		 
		$procedure = 'Call SP_Get_Degrees()';
		$procedureResult = $connection->query($procedure);
		if(!$procedureResult) { //if results don't return an error is displayed
			array_push($feedback, '<p style = "color: red;">Couldn\'t get list of Degrees!</p>');
		}
		else { //if call is sucessful
			$degreeArray = array(); //creats an array to display the list as HTML
			while($row = $procedureResult->fetch(PDO::FETCH_ASSOC)) {
			//pushes each row into the array
			array_push($degreeArray, $row["DegreeLevel"]);
			};
			$procedureResult->closeCursor();
		}

		$procedure1 = 'Call Get_Countries()';
		$procedureResult1 = $connection->query($procedure1);
		if(!$procedureResult1) { //if results don't return an error is displayed
			array_push($feedback, '<p style = "color: red;">Couldn\'t get list of Countries!</p>');
		}
		else { //if call is sucessful
			$countryArray = array(); //creats an array to display the list as HTML
			while($row = $procedureResult1->fetch(PDO::FETCH_ASSOC)) {
			//pushes each row into the array
			array_push($countryArray, $row["Name"]);
			};
			$procedureResult1->closeCursor();
		}
		
		$insert=$con->prepare("INSERT INTO user(Username,Password,Email,FirstName,LastName,Address,City,StateProvinceID,CountryID)values(?,?,?,?,?,?,?,?,?)");
		$row=array("$email","$pass","$email","$fname","$lname","$address","$city","1","1");
		 $sql=$insert->execute($row);
		
		if($sql)
		{
			$getUser="select * from user where Email='$email'";
			$getuser=$con->query($getUser);
			$r = $getuser->fetch(PDO::FETCH_ASSOC);
			$userid=$r['ID'];
			

			$socila_media_insert=$con->prepare("INSERT INTO social_media_link(UserID,LinkTypeID,URL)values(?,?,?)");
			$row1=array("$userid","1","$fblink");
		 $sql1=$socila_media_insert->execute($row1);

			$education_insert=$con->prepare("INSERT INTO user_education(UserID,Type,SchoolName,Major)values(?,?,?,?)");
			$row2=array("$userid","$education","$school","$school");
		 $sql2=$education_insert->execute($row2);
		 if($sql2)
		 {
		 	echo "Success";
		 }

		}
		
		
	}
?>

        <div class="container containerinput" style="margin-top:15px">
          <!-- Feedback Form Starts Here -->

          <!-- Heading Of The Form -->

            <?php print "<h2>".$message."</h2>"; ?>


            <!-- Feedback Form Ends Here -->

              <form  id="form" method="post" name="form">
							<p>
							Personal Info
							</p>
              <?php print $firstBlank;?><input name="vFirstName" placeholder="*First Name" type="text">
						  <input name="vMiddleName" placeholder="Middle Name" type="text">
              <?php print $lastBlank;?><input name="vLastName" placeholder="*Last Name" type="text" value="">
							<?php print $lastBlank;?><input name="vPhone" placeholder="Phone Number" type="text" value="">
							<?php print $lastBlank;?><input name="vAddress" placeholder="Street Address" type="text" value="">
							<?php print $lastBlank;?><input name="vCity" placeholder="City" type="text" value="">
							<?php print $lastBlank;?><input name="vState" placeholder="State/Province" type="text" value="">
							<?php print $lastBlank;?><input name="vZip" placeholder="Zip" type="text" value="">
							<select id="country" name="vCountry">
								<option value="" selected disabled hidden>Select Country</option>
								<?php
								//runs for each loop to get the country value for each country from the array
								foreach ($countryArray as &$value) {
								    print "<option value=".$value.">".$value."</option>" . "<br />";
								}
								?>
            	</select>
              <input id="gender" type="radio" name="vGender" value="male" checked="checked"> Male<br>
              <input id="gender" type="radio" name="vGender" value="female"> Female<br>
              <br />
							<p>
							Social Media Links
							</p>
							<?php print $lastBlank;?><input name="vFB" placeholder="Facebook Link" type="text" value="">
							<?php print $lastBlank;?><input name="vLinkedIn" placeholder="LinkedIn Link" type="text" value="">
							<?php print $lastBlank;?><input name="vTwitter" placeholder="Twitter Link" type="text" value="">
							<p>
							Login Info
							</p>
              <?php print $validateBlank;?>
              <?php print $matchEmail;?>
              <?php print $emailBlank;?><input name="vEmail" placeholder="*Your Email (Will serve as Username)" type="text" value="">
              <?php print $confirmEmailBlank;?><input name="vConfirmEmail" placeholder="*Confirm Your Email" type="text" value="">
              <?php print $matchBlank;?>
              <?php print $passwordBlank;?><input name="vPassword" placeholder="*Enter Password" type="password" value="">
              <?php print $confirmPasswordBlank;?><input name="vConfirmPassword" placeholder="*Confirm Your Password" type="password" value="">
							<p>
							Education
							</p>
							<select id="degree" name="vDegree">
								<option value="" selected disabled hidden>*Select Degree</option>
								<?php
								//runs for each loop to get the degree value for each degree from the array
								foreach ($degreeArray as &$value) {
								    print "<option value=".$value.">".$value."</option>" . "<br />";
								}
								?>
							</select>
							<?php print $lastBlank;?><input name="vSchool" placeholder="*School Name" type="text" value="">
							<?php print $lastBlank;?><input name="vMajor" placeholder="*Major" type="text" value="">
							<?php print $lastBlank;?><input name="vGradYear" placeholder="*Year of Graduation" type="text" value="">
							
            


            	</br/>

							<input type="checkbox" id="agreeToTerms" name="vAgreeToTerms" value="agree">
							<label for="agreeToTerms">Agree to Terms?</label> <?php print $agreeBlank;?>
							<br/>
							<br/>
              <input id="send" name="submit" type="submit" value="Submit">
              </form>



      </div>
</div>


  </body>
</html>
