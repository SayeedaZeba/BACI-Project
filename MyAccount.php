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
require_once "functions.php";
require_once "mail/mail.class.php";
include "RegistrationFormPHP.php"
?>

        <div class="container containerinput" style="margin-top:75px">
          <!-- Feedback Form Starts Here -->

          <!-- Heading Of The Form -->

            <?php print "<h2>".$message."</h2>"; ?>


            <!-- Feedback Form Ends Here -->

              <form action="Register.php" id="form" method="post" name="form">
							<p>
							Personal Info
							</p>
              <?php print $firstBlank;?><input name="vFirstName"  type="text" value="Joseph">
						  <input name="vMiddleName"  type="text" value="Keith">
              <?php print $lastBlank;?><input name="vLastName"  type="text" value="Owens">
							<?php print $lastBlank;?><input name="vPhone" type="text" value="555-5555">
							<?php print $lastBlank;?><input name="vAddress"  type="text" value="1234 Highland Dr.">
							<?php print $lastBlank;?><input name="vCity" type="text" value="Timbucktoo">
							<?php print $lastBlank;?><input name="vState" type="text" value="IN">
							<?php print $lastBlank;?><input name="vZip"  type="text" value="12345">
							<select id="country" name="vCountry">
                <option value="US" selected >USA</option>
                <option value="Myanmar">Myanmar</option>
            	</select>
              <input id="gender" type="radio" name="vGender" value="male" checked="checked"> Male<br>
              <input id="gender" type="radio" name="vGender" value="female"> Female<br>
              <br />
							<p>
							Social Media Links
							</p>
							<?php print $lastBlank;?><input name="vFB" placeholder="Facebook Link" type="text" value="facebook/jkowens.com">
							<?php print $lastBlank;?><input name="vLinkedIn" placeholder="LinkedIn Link" type="text" value="linkedin/jkowens.com">
							<?php print $lastBlank;?><input name="vTwitter" placeholder="Twitter Link" type="text" value="twitter/jkowens.com">
							<p>
							Login Info
							</p>
              <?php print $matchBlank;?>
              <?php print $passwordBlank;?><input name="vPassword" placeholder="*Enter New Password" type="password" value="">
              <?php print $confirmPasswordBlank;?><input name="vConfirmPassword" placeholder="*Confirm Your New Password" type="password" value="">
							<p>
							Education
							</p>
							<select id="degree" name="vDegree">
                <option value="Mentor">HS</option>
                <option value="Mentee" selected>BS</option>
								<option value="Mentee">Master</option>
                <option value="Mentee">PHD</option>
                <option value="Mentee">Technical Certificate</option>
                <option value="Mentee">Other</option>
            	</select>
							<?php print $lastBlank;?><input name="vSchool" type="text" value="Purdue">
							<?php print $lastBlank;?><input name="vMajor" type="text" value="MET">
							<?php print $lastBlank;?><input name="vGradYear" type="text" value="2009">
							<button>Add another degree</button>
							<p>
							Other Info
							</p>
              <select id="mentorOrMentee" name="vMentorOrMentee">
                <option value="Mentor" selected>Mentor</option>
                <option value="Mentee">Mentee</option>
            	</select>

							<select id="identity" name="vIdentity">
                <option value="student" selected>Student</option>
                <option value="employed">Working Professional</option>
            	</select>
							<?php print $lastBlank;?><input name="vEmployer" placeholder="Employer (Required if Working Professional)" type="text" value="">
							<?php print $lastBlank;?><input name="vField" placeholder="Field of Profession (Required if Working Professional)" type="text" value="">
							<br/>
							<button>Upload New Picture</button>
							<button>Upload New Resume</button> <br/>

            	</br/>

							<br/>
							<br/>
              <input id="send" name="submit" type="submit" value="Save Changes">
              </form>



      </div>
</div>


  </body>
</html>
