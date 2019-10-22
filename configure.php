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
		?>

				<div class="container containerinput" style="margin-top:15px">


					<form action="#" id="form" method="post" name="form">
						<h4 onclick="edituser()"><span class="glyphicon glyphicon-edit"></span><b>+</b>Edit User Info </h4>
						<div id="userinfo">
							<input name="vFirstName" placeholder="*First Name" type="text" value="shiva">
							<input name="vMiddleName" placeholder="Middle Name" type="text" value="k">
							<input name="vLastName" placeholder="*Last Name" type="text" value="sss">
							<input name="vPhone" placeholder="Phone Number" type="text" value="7799889988">
							<input name="vAddress" placeholder="Street Address" type="text" value="655656">
							<input name="vCity" placeholder="City" type="text" value="546565">
							<input name="vState" placeholder="State/Province" type="text" value="65656">
							<input name="vZip" placeholder="Zip" type="text" value="5656">
						</div>

						<h4 onclick="social()"><span class="glyphicon glyphicon-edit"></span><b>+</b>Social Media Links </h4>
						<div id="social">
							<input name="vFB" placeholder="Facebook Link" type="text" value="">
							<input name="vLinkedIn" placeholder="LinkedIn Link" type="text" value="">
							<input name="vTwitter" placeholder="Twitter Link" type="text" value="">
						</div>

						<h4 onclick="login()"><span class="glyphicon glyphicon-edit"></span><b>+</b>Login Info </h4>
						<div id="login">

                            <input name="vEmail" placeholder="email" type="text" value="roll@gmail.com">
							<input name="vPassword" placeholder="*Enter Password" type="password" value="123456">
							<input name="vConfirmPassword" placeholder="*Confirm Your Password" type="password" value="123456">

						</div>

						<h4 onclick="education()"><span class="glyphicon glyphicon-edit"></span><b>+</b>Education </h4>
						<div id="education">
							<select id="degree" name="vDegree">
								<option value="" selected disabled hidden>Master</option>
								<option value="Mentor">HS</option>
								<option value="Mentee">BS</option>
												<option value="Mentee">Master</option>
								<option value="Mentee">PHD</option>
								<option value="Mentee">Technical Certificate</option>
								<option value="Mentee">Other</option>
							</select>
							<input name="vSchool" placeholder="*School Name" type="text" value="xyz school">
							<input name="vMajor" placeholder="*Major" type="text" value="abc">
							<input name="vGradYear" placeholder="*Year of Graduation" type="text" value="2016">

						</div>

						<input id="send" name="submit" type="submit" value="Save Changes">
					</form>


			    </div>
		</div>

		<script>
			$(document).ready(function() {
				$('#userinfo').hide();
				$('#social').hide();
				$('#education').hide();
				$('#login').hide();
			});
			function edituser() {
				$('#userinfo').toggle();
			}
			function social() {
				$('#social').toggle();
			}
			function education() {
				$('#education').toggle();
			}
			function login() {
				$('#login').toggle();
			}

		</script>

		</body>
</html>
