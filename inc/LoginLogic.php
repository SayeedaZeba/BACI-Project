<?php
//calls dependencies
require_once "functions.php";
$connection = dbConnect();
$msg = "";
$uname = "";
$pwd = "";



function spamcheck($field)
  {
  //filter_var() sanitizes the e-mail
  //address using FILTER_SANITIZE_EMAIL
  $field=filter_var($field, FILTER_SANITIZE_EMAIL);

  //filter_var() validates the e-mail
  //address using FILTER_VALIDATE_EMAIL
  if(filter_var($field, FILTER_VALIDATE_EMAIL))
    {
    return TRUE;
    }
  else
    {
    return FALSE;
    }
  }



?>

<!-- authentication section -->
<?php
  if (isset($_POST['submit']))
  {

    //take the information submitted and verify inputs
    $uname =  trim($_POST['vEmail']);
    $pwd = trim($_POST['vPassword']);

    //now veriy the username and password
    if (spamcheck($uname)) //if the email is not a valid format, don't need to continue at all
    {
		//Hash the password.
		$pwd = passHash($pwd);
      //security measure 2: use stored procedures
      $sql = "Call sp_count_user1('".$uname."', '".$pwd."');";
      //print $sql;
      $stmt = $connection->query($sql);
      if (!$stmt) {
        echo "Error occurred while attempting check password.";

      }
      else {
        $row = $stmt->fetch(PDO::FETCH_OBJ);
        $count = $row->c;

        //security measure 3: always use the actual value, don't use $count > 0
        if ($count == 1)
        {
          $_SESSION['uid'] = $uname;
          Header ("Location:HomePage.php");
        }
        else $msg = '<p style = "color: red;">Email or password not found.  The password/email may not be correct, or the account may not be activated.</p>';
      }


    }
    else $msg = '<p style = "color: red;">Please enter a valid email.</p>';

  }
  else
  {	if (isset($_GET['l'])) //if the user is redirected from the home page
    {
      $tag = $_GET['l'];
      if ($tag == 'r') $msg = "You have already registered with this email. Click on Forget Password to retrieve your password.";

    }
  }

?>
