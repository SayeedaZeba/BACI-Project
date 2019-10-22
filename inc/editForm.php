
<!-- Heading Of The Form -->

<!-- Feedback Form Ends Here -->
<script src="inc/Register.js"></script>
	<form action="<?php echo "" . $_SERVER['REQUEST_URI']?>" id="form" method="post" name="form" enctype="multipart/form-data">
				<p class="registerHeader">
				Personal Info
				</p>
  <?php print $firstBlank;?><label>*First name</label><input name="vFirstName" placeholder="*First Name" type="text" value="<?php echo $firstname?>">
			  <label>Middle Name</label><input name="vMiddleName" placeholder="Middle Name" type="text" value="<?php echo $middlename?>">
  <?php print $lastBlank;?><label>*Last Name</label><input name="vLastName" placeholder="*Last Name" type="text" value="<?php echo $lastname?>">
				<label>Phone</label><input class="phone" name="vPhoneDigits" placeholder="Phone Number" type="text" value="<?php echo $phone?>">
				<p class = "alert alert-secondary">Phone numbers should be in the format +##(###)###-#### or (###)###-####</p>
				<label>*Address</label><input name="vAddress" placeholder="*Street Address" type="text" value="<?php echo $address?>">
				<label>*City</label><input name="vCity" placeholder="*City" type="text" value="<?php echo $city?>">

				<label>*Select Country</label>
				<select id="country" name="vCountry" onchange="showHint(this.value)">
					<option value="" selected disabled hidden>*Select Country</option>
					<?php
					//runs for each loop to get the country value for each country from the array
					foreach ($countryArray as $value) {
						if($value->ID == $country)
							print '<option value="'.$value->ID.'" selected = "selected">'.$value->Name."</option>" . "<br />";
						else
							print '<option value="'.$value->ID.'">'.$value->Name."</option>" . "<br />";
					}
					?>
				</select>

				<label>*Select State</label>
				<?php print $lastBlank;?><span  id="txtHint">
				  <?php print $list; ?>
				</span>
				<?php print $lastBlank;?><label>Zip Code</label><input name="vZip" placeholder="Zip" type="text" value="">
				<p class = "alert alert-secondary">Zip Codes should be in the format ##### or ####-#####</p>
  <label>Select Gender</label><input id="gender" type="radio" name="vGender" value="male" <?if(empty($gender) or $gender =="male") echo 'checked="checked"';?>> Male
  <input id="gender" type="radio" name="vGender" value="female" <?php if($gender =="female") echo 'checked="checked"';?>> Female
  <input id="gender" type="radio" name="vGender" value="other" <?php if($gender =="other") echo 'checked="checked"';?>> Other<br>
  <br />
				<p class="registerHeader">
				Social Media Links
				</p>
				<?php print $lastBlank;?><label>Facebook</label><input name="vFB" placeholder="Facebook Link" type="text" value="<?php echo $fblink?>">
				<?php print $lastBlank;?><label>LinkedIn</label><input name="vLinkedIn" placeholder="LinkedIn Link" type="text" value="<?php echo $linkedInlink?>">
				<?php print $lastBlank;?><label>Twitter</label><input name="vTwitter" placeholder="Twitter Link" type="text" value="<?php echo $twitterlink?>">
				<?php
				if(!empty($oldEducation))
				{	echo'
					<p class="registerHeader">
					Current Education Data
					</p>
					';
					echo '<div style="text-align:right; padding-right: 70px">';
					foreach( $oldEducation as $educationItem )
					{
						echo '		<span  class="btn-group-toggle" data-toggle="buttons">';
						echo '			<label class="btn btn-outline-danger" style = "width:100px;"> Delete';
						echo '				<input type = "checkbox" name = "EducationItem'.$educationItem->RecordID.'" value = "'.$educationItem->RecordID.'">';
						echo ' 			</label>';
						echo '		</span>';
						echo '		<p class="editDegree" style="text-align:center">' . $educationItem->DegreeLevel . ' in ' . $educationItem->Major . ' from ' . $educationItem->SchoolName . ', Achieved ' . $educationItem->GradYear . '</p><br />';


					}
					echo '</div>';
				}
				?>

				<p class = "registerHeader">
				New Education Data
				</p>
				<div id="degreeDiv">
					<div id="degree1">
						<label>Select Degree</label><select id="degree" name="vDegree">
							<option value="" selected disabled >*Select Degree</option>
							<?php
							//runs for each loop to get the degree value for each degree from the array
							foreach($degreeArray as $value) {
								print '<option value="'.$value->ID.'">'.$value->DegreeLevel."</option>";
							}
							?>
						</select>

						<label>School Name</label><input name="vSchool" placeholder="*School Name" type="text" value="">
						<label>Major</label><input name="vMajor" placeholder="*Major" type="text" value="">
						<label>Select Grad Year</label><select id="GradYear" name="vGradYear">
							<option value="" selected disabled hidden>Select Grad Year</option>
							<?php
							//runs for each loop to get the country value for each country from the array
							for ($yr = date("Y")+5; $yr >= 1970; $yr--){
								print '<option value = "'.$yr.'">'.$yr.'</option>';
							}
							?>
							</select>
						<button type="button"  class="addDeg" id="addDegreeButton1">Add another degree</button>
					</div>
				</div>
				<p class="registerHeader">
				Other Info
				</p>
  <label>*Do you wish to be a Mentor or Mentee</label><select id="mentorOrMentee" name="vMentorOrMentee">
					<option value="" selected disabled hidden>Select Mentor or Mentee</option>
	<option value="Mentor" <?php if($mentorormentee == "Mentor") echo 'selected="selected"';?>>Mentor</option>
	<option value="Mentee" <?php if($mentorormentee == "Mentee") echo 'selected="selected"';?>>Mentee</option>
	<option value="Neither" <?php if($mentorormentee == "Neither") echo 'selected="selected"';?>>Neither</option>
	</select>
	<p class = "alert alert-warning">Changing mentorship status will cancel any current mentorships!</p>

				<label>Select Work Status</label><select id="identity" name="vIdentity">
					<option value="" selected disabled hidden>Select Work Status</option>
	<option value="student" <?php if($workstatus == "student") echo 'selected="selected"';?>>Student</option>
	<option value="employed" <?php if($workstatus == "employed") echo 'selected="selected"';?>>Working Professional</option>
	</select><br/><br/>
	<p class = "alert alert-secondary">If you are a working professional, employer and field are required.</p>
				<label>Employer</label><input name="vEmployer" placeholder="Employer (Required if Working Professional)" type="text" value="<?php echo $employer;?>">
				<label>Field</label><input name="vField" placeholder="Field of Profession (Required if Working Professional)" type="text" value="<?php echo $field;?>">
				<br/>
				<label for="file">Upload Picture</label>
				<input type="file" name="vimage" id="vimage"><br>

				<br/>

				<label for="file">Upload Resume</label>
				<input type="file" name="vresume" id="vresume"><br>

  <input id="send" name="submit" type="submit" value="Submit">
  </form>
