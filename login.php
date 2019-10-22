<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>BACI Login Page</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
	</head>

	<body>
<?php include "header.php"; ?>

		<div id = "main">
			<?php

			//calls dependencies
			require_once("inc/functions.php");
			require_once("inc/RegistrationFormPHP.php");
			require_once("inc/LoginLogic.php");


			?>

			<!-- Form for login Starts Here -->
	<div class="login containerinput" style="margin-top:15px">
		<div>
		<!-- Heading Of The Form -->
			<H1>Login:</H1>
			<!-- <?php print $activationMessage;?> -->
			<?php print $msg;?>
				<form action="#" id="form" method="post" name="form">
					<!-- Form contains a field for login and password -->
				<input name="vEmail" placeholder="Your Email" type="text" value="<?php echo $uname?>">
				<input name="vPassword" placeholder="Enter Password" type="password" value="">
				<div>
					<?php print $passwordBlank;?>
				</div>
				<br />
				<input id="send" name="submit" type="submit" value="Submit">
			</form>
			<br />
			<a href="Register.php">
				<p>Need an Account? Sign Up Here.</p>
			</a>
			<a href="forgotPassword.php">
				<p>Forgot Password?</p>
			</a>
		</div>
	</div>


			<div class="registrationInfo">
				<p>

				<h3>Register or Log In</h3>
			  <h6>Who is eligible to become the Myanmar Student and Professional Network USA (MSPNUSA) member?
				The MSPNUSA membership is free. Eligible members should be those who identify themselves as a Myanmar, are originally from Myanmar and are currently enrolled in or graduated from higher education institutions in the United States. These students may now be living in the United States, Myanmar, or elsewhere.</h6>

				<h6>Why should you become a member of the Myanmar Student and Professional Network USA?
				Some of the benefits of being a member of the network include:</h6>
				<ol>
					<li>
						<b>Networking:</b> MSPNUSA will give you the opportunity to network and build connections among your peers, colleagues, and community members. This valuable social capital and relationships will help with personal and professional development as well as contribute to societal progress within the community
					</li>
					<li>
					  <b>Access to Mentors:</b> By joining MSPNUSA you will gain access to a network of mentors. There are THREE layers of mentor-mentee relationship:
					</li>
					<ol>
							<li>
														High School Student – College Student
							</li>
							<li>
														College Student – Graduate Student/Professional
							</li>
							<li>
														College/graduate/entry level professional – Mid/Senior level professionals or practitioners
							</li>
					</ol>
						Mentors at all three of these levels are willing and able to help by addressing any educational and professional questions you might have. You will be assigned mentor(s) based on your academic and future professional interests.
					<li>
						<b>Scholarships and Fellowships Opportunities:</b> MSPNUSA Chapters across the US regularly post and provide valuable resources and information helping you identify and apply for college scholarships and graduate fellowships.
					</li>
					<li>
						<b>Collaborative Opportunities:</b> By registering with MSPNUSA you will have the opportunity to collaborate with other members both on the individual and institutional level, through out your local, national, or international community on projects of shared interests.
					</li>
					<li>
						<b>Internship, Volunteer, and Job Opportunities:</b> With the assistance of MSPNUSA you will have access to internship and volunteer opportunities in your local communities and beyond, in order to further growth in fields of your interest. These opportunities are posted regularly both on the internship/volunteer/job opportunity web page and the Facebook page, which only members have access.
					</li>
						<b>Register now to enjoy these privileges.</b>
						<br />
						<br />
						<p>
							Create your profile here by filling out the fields below. Alternatively, you can download a copy of our membership form, fill it out, and send it to us. To submit the form via email, send it to info@mspnusa.org. To submit the form via postal mail, send it to the following address:  4925 South Shelby St., Indianapolis, IN, 46227, United States.  Download Membership Form	(14 downloads)
						</p>
				</ol>



			</p>
			</div>


		</div>


		<div>
			<a href="adminLogin.php">Admin Login</a>
		</div>

	</body>

</html>
