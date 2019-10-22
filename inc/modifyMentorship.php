<?php include "../header.php";
$_SESSION['timeout'] = time();
  require_once "sessionVerifyAdmin.php";
	require_once "mail/mail.class.php";
?>

<?php
$code = "";
$menteeEmail = "";
$mentorEmail = "";
$historyID = "";
$connection = dbConnect();
$today = date("Y-m-d");
if(isset($_GET['mentor']))	{
  $mentorEmail = $_GET['mentor'];
}
if(isset($_GET['mentee']))	{
  $menteeEmail = $_GET['mentee'];
}
if(isset($_GET['code']))	{
  $code = $_GET['code'];
}
if(isset($_GET['historyID']))	{
  $historyID = $_GET['historyID'];
}


//Make sure the database connection was succesful.
if($connection === FALSE)
  array_push($feedback, '<p style = "color: red;">Cannot connect to database.</p>');
else
{
  //get mentee ID
  $procedure = 'call sp_get_userTable_data_by_email("' . $menteeEmail . '");';
  $procedureCall = $connection->query($procedure);
  //error if procedure call empty
  if(!$procedureCall)
    array_push($feedback,'<p style ="color:red;">Query Error.</p>');
  else
  {
    //Get the user's data and close the cursor.
    $user = $procedureCall->fetch(PDO::FETCH_OBJ);
    $procedureCall->closeCursor();
    //(Seeking mentorship is interpreted to mean they're looking for a mentor.)
    $menteeID = $user->ID;
  }

  //get mentor ID
  $procedure = 'call sp_get_userTable_data_by_email("' . $mentorEmail . '");';
  $procedureCall = $connection->query($procedure);
  //error if procedure call empty
  if(!$procedureCall)
    array_push($feedback,'<p style ="color:red;">Query Error.</p>');
  else
  {
    //Get the user's data and close the cursor.
    $user = $procedureCall->fetch(PDO::FETCH_OBJ);
    $procedureCall->closeCursor();
    //(Seeking mentorship is interpreted to mean they're looking for a mentor.)
    $mentorID = $user->ID;
  }

}



if($code=='approve') {
  //create procedure to add start date to histor table
  $procedure = 'call sp_confirm_mentorship("' . $today .  '" , '  . $historyID . ')';
  $procedureCall = $connection->query($procedure);
} else if ($code=='reject') {
  //create procedure to input start date, end date, and reject date
  $procedure = 'call sp_reject_mentorship("' . $today .  '" , '  . $historyID . ')';
  $procedureCall = $connection->query($procedure);
} else if ($code=='unmatch') {
  //create procedure to add end date to history table
  $procedure = 'call sp_unpair_mentorship("' . $today .  '" , '  . $historyID . ')';
  $procedureCall = $connection->query($procedure);
}


$subject = "Mentor pairing update";
//sets body for Mail() function
$body = 'This message is to inform you that the mentorship pairing between' .$mentorEmail. ' and ' . $menteeEmail . ' has been ' . $code . ' by the administrator';

$mailer = new Mail();
//emails both the mentor and mentee with the status update
if (($mailer->sendMail($mentorEmail, 'BACI User', $subject, $body))==true)
if (($mailer->sendMail($menteeEmail, 'BACI User', $subject, $body))==true)

	// $_SESSION['message'] = "<b>Thank you for registering. A welcome message has been sent to the address you have just registered.</b>";
// message displays an error if email does not go through
// else $_SESSION['message'] = "Email not sent. " . $_SESSION['email'].' '. $_SESSION['first'].' '. $subject.' '. $body;
// Header ("Location:Output.php?msg=".$message) ;

if ($_SESSION['prevPage'] == 'pending') {
  Header("location:../pendingMentorMenteeList.php");
} else if ($_SESSION['prevPage'] == 'pairs') {
  Header("location:../MentorMenteeList.php");
} else {
  echo "error";
}


?>
