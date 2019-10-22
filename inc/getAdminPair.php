<?php
require_once("functions.php");
require_once("adminOBJ.php");
require_once("mail/mail.class.php");

$connection = dbConnect();
// get the q parameter from URL
$menteeEmail = $_REQUEST["mentee"];
$mentorID = $_REQUEST["mentor"];
$today = date("Y-m-d");

// lookup all hints from array if $q is different from ""

$procedure = 'call sp_get_userTable_data_by_email("' . $menteeEmail . '")';
$procedureResult = $connection->query($procedure);
$row = $procedureResult->fetch(PDO::FETCH_ASSOC);
$menteeRowID = $row["ID"];
$procedureResult->closeCursor();

$procedure1 = 'call sp_admin_create_new_mentorship(' . $mentorID . ', ' . $menteeRowID . ', ' . $mentorID . ', "' . $today .  '" )';
$procedureResult1 = $connection->query($procedure1);
$procedureResult->closeCursor();

$procedure2 = 'call SP_Get_Email_By_ID(' . $mentorID . ')';
$procedureResult2 = $connection->query($procedure2);
$row2 = $procedureResult2->fetch(PDO::FETCH_ASSOC);
$mentorEmail = $row2["Username"];
$procedureResult->closeCursor();


// Output "no suggestion" if no hint was found or output correct values
// Header ("Location:AdminPair.php")

//sets subject and body for Mail() function
$subject = "Mentor pairing update";
$body = 'This message is to inform you that the mentorship pairing between ' .$mentorEmail. ' and ' . $menteeEmail . ' has been created by the administrator';

//emails both the mentor and mentee with the status update
$mailer = new Mail();
if (($mailer->sendMail($mentorEmail, 'BACI User', $subject, $body))==true)
if (($mailer->sendMail($menteeEmail, 'BACI User', $subject, $body))==true)


?>
