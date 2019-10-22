<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

  <?php
  //Call dependencies
	require_once("inc/functions.php");
	require_once("inc/adminOBJ.php");
	//Start a session.
	session_start();


  $pageTitle = "";
  $URL = $_SERVER["PHP_SELF"];
  switch( $URL )
  {
	  case("/CourseProject/add_countries.php"):
  		$pageTitle = "Administrative functions:  Add Country to list.";
  		break;
    case("/CourseProject/editCountry.php"):
    	$pageTitle = "Administrative functions:  Edit a Country.";
    	break;
  	case("/CourseProject/add_degree_levels.php"):
  		$pageTitle = "Administrative functions:  Add Education Levels to list.";
  		break;
    case("/CourseProject/editDegree.php"):
  		$pageTitle = "Administrative functions:  Edit a degree.";
  		break;
  	case("/CourseProject/addAdmin.php"):
  		$pageTitle = "Add New Administrator";
  		break;
  	case("/CourseProject/admin_logout.php"):
  		$pageTitle = "Administrator Logout";
  		break;
  	case("/CourseProject/adminLogin.php"):
  		$pageTitle = "Administrator Login";
  		break;
  	case("/CourseProject/admin.php"):
  		$pageTitle = "Administrative page";
  		break;
  	case("/CourseProject/configure.php"):
  		$pageTitle = "Edit Account Details";
  		break;
  	case("/CourseProject/confirmDeleteAdmin.php"):
  		$pageTitle = "Confirm Admin Deletion";
  		break;
  	case("/CourseProject/EditAdminList.php"):
  		$pageTitle = "Edit Administrators";
  		break;
  	case("/CourseProject/EditAdmin.php"):
  		$pageTitle = "Edit An Administrator";
  		break;
  	case("/CourseProject/EditUser.php"):
  		$pageTitle = "Edit A User";
  		break;
  	case("/CourseProject/forgot_password.php"):
  		$pageTitle = "Forgot Password";
  		break;
    case("/CourseProject/changePassword.php"):
  		$pageTitle = "Change Password";
  		break;
    case("/CourseProject/HomePage.php"):
  		$pageTitle = "User Home";
  		break;
  	case("/CourseProject/login.php"):
  		$pageTitle = "A network for students and professionals across the United States.";
  		break;
  	case("/CourseProject/Register.php"):
  		$pageTitle = "Registration Page";
  		break;
  	case("/CourseProject/search.php"):
  		$pageTitle = "Search";
  		break;
  	case("/CourseProject/MentorMenteeList.php"):
    		$pageTitle = "View Current Pairs";
    		break;
    case("/CourseProject/pendingMentorMenteeList.php"):
  		$pageTitle = "View Pending Requests";
  		break;
    case("/CourseProject/inc/modifyMentorship.php"):
  		$pageTitle = "Request Modification";
  		break;
  	case("/CourseProject/mentorList.php"):
    		$pageTitle = "Search for mentors or mentees";
    		break;
    case("/CourseProject/AdminPair.php"):
    		$pageTitle = "Pair mentor and mentee";
    		break;
	case("/CourseProject/editState.php"):
			$pageTitle = "Edit a state";
			break;
	case("/CourseProject/HistoryTable.php"):
		$pageTitle = "View mentoring history";
		break;

  	default:
  		$pageTitle = $URL;

	//Check if there is an admin logged in.
	$adminLoggedIn = FALSE;

	if(isset($_SESSION['admin']))
		$adminLoggedIn = TRUE;
    //require_once "inc/sessionVerifyAdmin.php";
  }
  ?>



<div id = "navBar">
  <nav class="navbar navbar-expand-md navbar-dark fixed-top bg-dark">
    <img class="img-fluid" style = "max-height: 70px" src="img/BaciLogo.png" alt="logo"/>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExampleDefault">
      <ul class="navbar-nav mr-auto">
        <?php

		if( isset($_SESSION['uid']))
		{
			//If a user is logged in, show an option to edit their account, visit their homepage, and sign out.
     if(basename($_SERVER['PHP_SELF'])=='HomePage.php') {
       echo '<li class="nav-item active" >' . "\n"
 				.'<a class="nav-link" href="HomePage.php">Home<span class="sr-only">(current)</span></a>' . "\n"
 				.'</li>' . "\n";
     } else {
       echo '<li class="nav-item " >' . "\n"
 				.'<a class="nav-link" href="HomePage.php">Home<span class="sr-only">(current)</span></a>' . "\n"
 				.'</li>' . "\n";
     }
     if(basename($_SERVER['PHP_SELF'])=='Register.php') {
       echo '<li class="nav-item active">'  . "\n"
 				.'<a class="nav-link" href="Register.php?editMode=1">Configure Account</a>' . "\n"
 				.'</li>' . "\n";
     } else {
       echo '<li class="nav-item ">'  . "\n"
 				.'<a class="nav-link" href="Register.php?editMode=1">Configure Account</a>' . "\n"
 				.'</li>' . "\n";
     }
     if(basename($_SERVER['PHP_SELF'])=='search.php') {
       echo '<li class="nav-item active">' . "\n"
 				.'<a class="nav-link" href="search.php">Search Users</a>' . "\n"
 				.'</li>' . "\n";
     } else {
       echo '<li class="nav-item ">' . "\n"
 				.'<a class="nav-link" href="search.php">Search Users</a>' . "\n"
 				.'</li>' . "\n";
     }

			echo '<li class="nav-item">' . "\n"
				.'<a class="nav-link" href="UserLogOut.php">Logout<span class="sr-only">(current)</span></a>' . "\n"
				.'</li>' . "\n";
		}
		else
		{
			//Otherwise, show the option to register or log in.
      if(basename($_SERVER['PHP_SELF'])=='login.php') {
        echo '<li class="nav-item active">' . "\n"
  				.'<a class="nav-link" href="login.php">Log in<span class="sr-only">(current)</span></a>' . "\n"
  				.'</li>' . "\n";
      } else {
        echo '<li class="nav-item">' . "\n"
  				.'<a class="nav-link" href="login.php">Log in<span class="sr-only">(current)</span></a>' . "\n"
  				.'</li>' . "\n";
      }
      if(basename($_SERVER['PHP_SELF'])=='Register.php') {
        echo '<li class="nav-item active">' . "\n"
  				.'<a class="nav-link" href="Register.php">Register<span class="sr-only">(current)</span></a>' . "\n"
  				.'</li>' . "\n";
      } else {
        echo '<li class="nav-item ">' . "\n"
  				.'<a class="nav-link" href="Register.php">Register<span class="sr-only">(current)</span></a>' . "\n"
  				.'</li>' . "\n";
      }

		}
		?>

        <li class="nav-item">
          <a class="nav-link" href="https://thebaci.org/" target="_blank">The Baci</a>
        </li>
		<?php
		//If there's currently an admin logged in,
		//Create the admin dropdown menu.
		if(adminLoggedIn())
		{
			//Regardless of admin-level, these things need to show.
			echo '<li class="nav-item dropdown">'."\n";
			echo '<a class="nav-link dropdown-toggle" href="#" id="dropdown01" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Admin</a>'."\n";
			echo '<div class="dropdown-menu" aria-labelledby="dropdown01">'."\n";
			//These items only show for super admins.
			if($_SESSION['admin']->isSuperAdmin())
			{
				echo '<a class="dropdown-item" href="addAdmin.php">Add an Admin</a>'."\n";
				echo '<a class="dropdown-item" href="EditAdminList.php">Edit/Delete an Admin</a>'."\n";
			}
			//These items only show for users who are at least admin.
			if($_SESSION['admin']->isAtLeastAdmin())
			{
				echo '<a class="dropdown-item" href="Register.php?adminMode=1">Add a User</a>'."\n";
			}
			//These items only show for users who are at least a coordinator.
			if($_SESSION['admin']->isAtLeastCoordinator())
			{
				echo '<a class="dropdown-item" href="search.php">View/Search Users</a>'."\n";
				echo '<a class="dropdown-item" href="MentorMenteeList.php">View Current Pairs/Unpair</a>'."\n";
				echo '<a class="dropdown-item" href="pendingMentorMenteeList.php">View Pending Pairs</a>'."\n";
				echo '<a class="dropdown-item" href="AdminPair.php">Pair Mentees with Mentors</a>'."\n";
				echo '<a class="dropdown-item" href="HistoryTable.php">View History</a>'."\n";
				echo '<a class="dropdown-item" href="download.php">Download User Data to Spreadsheet</a>'."\n";
				echo '<a class="dropdown-item" href="paired_mentor.php">Download Mentorship Pairing to Spreadsheet</a>'."\n";
				echo '<a class="dropdown-item" href="add_countries.php">Add a Country</a>'."\n";
				echo '<a class="dropdown-item" href="add_degree_levels.php">Add a Degree Option</a>'."\n";
				echo '<a class="dropdown-item" href="admin_logout.php">Admin Logout</a>'."\n";
      }
			//Regardless of admin-level, these things need to show.
			echo '</div>'."\n";
			echo '</li>'."\n";
		}
		?>
      </ul>
    </div>
  </nav>
</div>

<div id = "header">
  <h3>Myanmar Students and Professionals Network USA
</h1>
  <h6><?php echo $pageTitle;?></h3>
</div>
