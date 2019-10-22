<?php
// Array with names

// get the q parameter from URL
require_once("functions.php");
require_once("adminOBJ.php");

$list = "";
$q = "";
$state = "";
$fromSearch=False;
if(isset($_REQUEST["state"]))		$state = sanitize($_REQUEST["state"]);
if(isset($_REQUEST["q"]))			$q = sanitize($_REQUEST["q"]);
if(isset($_REQUEST["fromSearch"]))	$fromSearch=True;


// Output "no suggestion" if no hint was found or output correct values

?>


<?php

$connection = dbConnect();
if($connection === FALSE)
{
  array_push($feedback,'<p style ="color:red;">Database connection failed!</p>');
}
else
{
  //Calls SQL procedure that generates a list of countries
  $procedure1 = 'Call Get_States("'.$q.'")';
  $procedureResult1 = $connection->query($procedure1);
  if(!$procedureResult1) { //if results don't return an error is displayed
    array_push($feedback, '<p style = "color: red;">Couldn\'t get list of States!</p>');
  }
  else { 
  //if call is successful
    $htmlStates = array(); //creats an array to display the list as HTML
    $states = $procedureResult1->fetchAll(PDO::FETCH_OBJ);
	sortObjectsByName($states);
	//If "From Search" is selected, add an "any state' option.
	if($fromSearch)
		if($state == "")
			array_push($htmlStates, '<option id="" value="" selected>Any State In This Country</option>');
		else
			array_push($htmlStates, '<option id="" value="">Any State In This Country</option>');
	foreach( $states as $stateInDB)
	{
		//pushes each row into the array
		if($state == $stateInDB->ID)
		  array_push($htmlStates, '<option selected id="'. $stateInDB->ID . '" value="' . $stateInDB->ID . '">' . $stateInDB->Name . '</option>');
		else
		  array_push($htmlStates, '<option id="'. $stateInDB->ID . '" value="' . $stateInDB->ID . '">' . $stateInDB->Name . '</option>');
	}
	

    $procedureResult1->closeCursor();
  }
}
$stateList = '<select id="stateSelect" name="vState" >';
$stateList .= '<option id ="None" value="" disabled hidden>Select State</option>';
foreach ($htmlStates as &$value) {
    $stateList .= $value;
}
$stateList .=  '</select>';



print $stateList;


 ?>
