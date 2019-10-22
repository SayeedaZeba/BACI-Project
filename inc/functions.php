<?php

function generatePIN()
{
	//Create a string containing all possible characters in the PIN
	//Removed the characters O, L, and I due to their similar appearance to numbers.
	$sourceString = '0123456789ABCDEFGHJKMNPQRSTUVWXYZ';
	//Create a string to hold the output.
	$outputString = '';
	for($index = 0; $index < 6; $index++)
	{
		//Add a randomly selected character from the sourceString.
		$chosenCharacterIndex = rand(0, strlen($sourceString) - 1);
		$outputString .= $sourceString[$chosenCharacterIndex];
	}
	return $outputString;
}

function dbConnect()
{
	/*** mysql hostname ***/
	$hostname = '_';			//add your hostname here

	/*** mysql dbname ***/
	$dbname = '_';				//add the name of your database here

	/*** mysql username ***/
	$username = '_';			//add your username here

	/*** mysql password ***/
	$password = '_';			//add your password here

	try 
	{
		$con = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
		return $con;
    }
	catch(PDOException $e)
    {
		return FALSE;
    }
}

//Assumes that admins are stored as session variable objects called "admin"
function adminLoggedIn()
{
	//First, assume we're returning false.
	$returnValue = FALSE;
	if(isset($_SESSION['admin']))
		if(getType($_SESSION['admin'] == 'object'))
			$returnValue = TRUE;
	return $returnValue;
}

//This function uses a hashing function for password storage and retrieval.
//It is not particularly secure, and security could be improved by replacing it.
function passHash($inputPassword)
{
	$outputString = sha1($inputPassword);
	$outputString = saltPassword($outputString);
	return $outputString;
}

//This is a simple salting algorithm intended to make the SHA1 password slightly more secure.
//It is still recommended to use a better encryption algorithm than SHA1, but PHP5.3.3 does not
//support many better alternatives
function saltPassword($inputPassword)
{
	//Create a salt string and a secret string.
	//Characters from the salt string are inserted into the string.
	$saltString = "gCm1Bx0EoEy9xQzqQuRYr2Tj72dvnmJ2aGCrF0aJhlVNap6uLF";
	//Characters from the secret string are used to make the RNG seed not directly related to the input password.
	$secretString = "A0MPRreBjHOidITZUBQtJESSj3JsOtVpgLeN7xxSSakWimybOb";
	srand((int)$inputPassword);
	//Create an empty string to compare the seed.
	$seedString = "";
	$stringLength = rand(10,25);
	for($i = 0; $i < $stringLength; $i++)
	{
		$indexOfLetter = rand(0,49);
		$seedString .= $secretString[$indexOfLetter];
	}
	//Seed the RNG with the seed string.
	srand((int)$seedString);
	//Pick how many salt characters to swap in.
	$saltCount = rand(10,25);
	for($i = 0; $i < count($saltCount); $i++)
	{
		$indexToSwapAt = rand(0, count($inputPassword) - 1);
		$sourceIndex = rand(0, 49);
		$inputPassword[$indexToSwapAt] = $saltString[$sourceIndex];
	}
	return $inputPassword;
}

// This code is based on code from the chapter 13 demo file.
// It has been modified to take advantage of PHPs built in chr function.
function randomCodeGenerator($length){
          $code = "";
         for($i = 0; $i<$length; $i++){
             //generate a random number between 65 and 10
             $r = mt_rand(65,100);
             //if the number is less than 90, use the built-in chr string function to convert it from an ASCII number to a letter.
             if ($r <= 90) {
                $code .= chr($r);
            }
            else 
			{    
				//If it was greater than 90, subtract 43 from it to move it into the ASCII numbers.
                 $code .= chr($r - 43);
            }

         }
         return $code;

}



// This function sanitizes the email them uses the filter_input function to verity
// That it is a valid format
function emailchecker($input)
  {
  //removes all illegal characters
  $field=filter_var($input, FILTER_SANITIZE_EMAIL);
  //validates the format of the email
  if(filter_var($field, FILTER_VALIDATE_EMAIL))
    {
    return TRUE; //if email format is valid function returns true
    }
  else
    {
    return FALSE;//if email format is not a valid function returns false
    }
  }

function checkBlank($input) {
  if(isset($_POST[$input]))	{
		$input = $_POST[$input];
		if ($input=="")
		{
		}
		else return TRUE;
}
}

function checkZip($input)
{
	//Assume an invalid response.
	$valid = false;
	//For option:  #####
	if(strlen($input) == 5)
	{
		//If the string is of length 5, exclusively numbers, it's assumed to be valid.
		$numbers = countRegExWithinString($input, '/[0-9]/');
		if($numbers == 5)
			$valid = true;
	}
	//For option: ####-#####
	else if(strlen($input) == 10)
	{
		//Check if the 5th item is a hyphen
		if($input[4] == "-")
		{
			//If we're here, get rid of the hyphen.
			$input = str_replace("-","",$input);
			//if what's left over is just numbers, it's assumed the zip is valid.
			$numbers = countRegExWithinString($input, '/[0-9]/');
			if($numbers == 9)
				$valid = true;
		}
	}
	return $valid;
}

function checkPhone($input)
{
	//Assume an invalid response.
	$valid = false;
	//For option: +##(###)###-####
	if(strlen($input) == 16)
	{
		if($input[0] == "+")
		{
			//If the + was there and in the correct position, replace it with a space for now.
			//This way I don't have to think about how the length of the string is changing.
			$input = str_replace("+"," ",$input);
			if($input[3] == "(" and $input[7] == ")")
			{
				$input = str_replace("(", " ", $input);
				$input = str_replace(")", " ", $input);
				if($input[11] == "-")
				{
					//From here, we can start removing whitespace and formatting characters.
					//The number is probably valid, but we haven't checked whether it's all numbers yet.
					$input = str_replace("-", "", $input);
					$input = str_replace(" ", "", $input);
					$numbers = countRegExWithinString($input, '/[0-9]/');
					if($numbers == strlen($input))
						$valid = true;
				}
			}
		}
	}
	//For option: (###)###-####
	else if(strlen($input) == 13)
	{
		if($input[0] == "(" and $input[4] == ")")
		{
			$input = str_replace("(", " ", $input);
			$input = str_replace(")", " ", $input);
			if($input[8] == "-")
			{
				$input = str_replace("-", "", $input);
				$input = str_replace(" ", "", $input);
				$numbers = countRegExWithinString($input, '/[0-9]/');
				if($numbers == strlen($input))
					$valid = true;
			}
		}
	}
	return $valid;
}

//Checks if a social media link is in the proper format.
//if successful, returns a properly formatted url.  If it fails, 
function socialMediaCheck( $urlPrefix, $input)
{
	//Reference used:  https://www.w3schools.com/php/filter_validate_url.asp
	$input = filter_var($input, FILTER_SANITIZE_URL);
	if( filter_var($input, FILTER_VALIDATE_URL) )
	{
		if(strpos($input, "http://") === 0 or strpos($input, "https://") === 0)
		{
			//Store a temporary version of the input without the http:// or https://
			$tempinput = str_replace("http://", "", $input);
			$tempinput = str_replace("https://", "", $tempinput);
			if(strpos($tempinput, $urlPrefix) === 0)
				return $input;
			else
				return false;
		}
		else
			return false;
	}
	else
		return false;

	
}

//Checks that a password is in a proper format.  Can provide feedback using a feedback string passed by reference.
function passwordCheck($password, $confirmPassword, $requiredLength, &$feedback)
{
	$isValid = FALSE;
	$password = trim($password);
	//Check if the password and password confirmation match.
	if($password == $confirmPassword)
	{
		//Check if the password meets the length requirement.
		if(strlen($password) >= $requiredLength)
		{
			//Count the number of letters in the password.
			$numbers = countRegExWithinString($password, '/[0-9]/');
			$letters = countRegExWithinString($password, '/[A-Za-z]/');
			//Make sure there is at least one number and at least one letter.
			if($numbers > 0 and $letters > 0)
				//As long as this is the case, accept the password.
				$isValid = TRUE;
			else
				//Otherwise, indicate that the user needs to include a digit in their password.
				array_push($feedback ,'<p style = "color:red;">Password must contain letters, and must contain at least 1 digit.</p>');
		}
		else
			array_push($feedback ,'<p style = "color:red;">Password must meet a minimum length of '.$requiredLength.'.');
	}
	else
		array_push($feedback , '<p style = "color:red;">Password and confirmation password must match.');

	return $isValid;
}

//The code for this function is based on code from the chapter 13 demo file.
function countRegExWithinString($string, $regExpression)
{
	// Initialize a count
	$count = 0;
	// Create an array of characters from the source string.
	$charArray = str_split($string);
	// iterate over the length of the string.
	for($i = 0; $i < strlen($string); $i++)
	{
		if(preg_match($regExpression,$charArray[$i]))
		{
			$count += 1;
		}
	}
	return $count;
}


/*This function will validate if the code that was sent via email is the correct format.
*/
function codeValidate($input){
	$field = trim($input);
	if (strlen($input) < 50){
		return false;
	}
	else {
		//go through each character and find if there is a number or letter
    $array = str_split($input);
		for($i = 0; $i<strlen($input); $i++){
			if (preg_match("/[A-Za-z]/",$array[$i])){
        for($i = 0; $i<strlen($input); $i++){
          if (preg_match("/[0-9]/",$array[$i])){
            return TRUE;
          }
			}
		}
		}
	}
}

function activateCode($em,$fname,$code)
	{
	$subject = "Email Activation";
										
				$body = 'Hello!<br/><br/>Thank you for registering with us.<br/> Please click on this url to activate your account. <br/>
				http://yourwebsite.com/login.php?a='.$code;  //this link is sent to the valid email registered
				$mailer = new Mail();
				if (($mailer->sendMail($em, $fname, $subject, $body))==true)
					$msg = "<b>Thank you for registering. A welcome message has been sent to the address you have just registered.</b></br></br>";
				else $msg = "Email not sent. " . $em.' '. $fname.' '. $subject.' '. $body;
				
				return $msg;
	}


//function to upload a resume
function uploadresume($email)
{
	//Return a special string if no file was uploaded
	if($_FILES['vresume']['error'] == 4)
		return "__NO FILE__";
	
       /* Make sure your program has "write" permission to the tmp directory in the root directory (the default temporary upload directory defined in php.ini file) 
	 * as well as the final upload directory */
	
	$allowedExts = array( "jpeg", "jpg", "png","pdf","txt");
	$url = "";
	//The global $_FILES will contain all the uploaded file information. In the following examples, "file" is the user defined name of the file input object in the html page.   
	//$_FILES['file']['name']: name of the file on the client machine
	//$_FILES['file']['type']: mime type of the file
	//$_FILES['file']['size']: size of the file in bytes
	//$_FILES['file']['tmp_name']: The temporary filename of the file in which the uploaded file was stored on the server.
	//$_FILES['file']['error']: The error code associated with this file upload.
	
	$temp = explode(".", $_FILES["vresume"]["name"]); //get the uploaded file name, use . to seperate the name and the extension into the temp array
	
	$extension = end($temp); //get the last element of the array, which will be the file extension
	if ((($_FILES["vresume"]["type"] == "image/jpeg")
		|| ($_FILES["vresume"]["type"] == "image/jpg")
		|| ($_FILES["vresume"]["type"] == "image/pjpeg")
		|| ($_FILES["vresume"]["type"] == "image/x-png")
		|| ($_FILES["vresume"]["type"] == "image/png")
		|| ($_FILES["vresume"]["type"] == "application/pdf")
		|| ($_FILES["vresume"]["type"] == "text/plain"))
		&& ($_FILES["vresume"]["size"] < 5000000)
		&& in_array($extension, $allowedExts))
  	{
  		if ($_FILES["vresume"]["error"] > 0)
    		{
    			echo "Return Code: " . $_FILES["vresume"]["error"] . "<br>";
    		}
  		else
    		{

			if (is_uploaded_file($_FILES['vresume']['tmp_name'])) 
			{
   					echo "File ". $_FILES['vresume']['name'] ." uploaded successfully.\n";
   
			} else {
   				echo "unable to upload file ";
  
			}




    			if (file_exists("resume/" . $_FILES["vresume"]["name"]))
      			{
      					echo $_FILES["vresume"]["name"] . " already exists. ";
      			}
    			else
      			{
					//To eliminate collisions (files of the same name), an ID name is created.
					$idName = date('Y_m_d') . "_" . time() . "_". str_replace("@","",$email) . "rsm." . $extension;
      				move_uploaded_file($_FILES["vresume"]["tmp_name"],
      				"resume/" . $idName);													//change the url here according to where you want to store the resume
					$url = "resume/" . $idName;		//change the url here according to where you want to store the resume
					return $url;
      			}
    		}
  	}
	else
  	{
  		//echo "Invalid resume file<br/>";
  	}
}	
	
//function to upload an image
//Added email as an argument for use as part of the identifier.
function uploadimage($email)
{
	//Return a special string if no file was uploaded
	if($_FILES['vimage']['error'] == 4)
		return "__NO FILE__";
		
       /* Make sure your program has "write" permission to the tmp directory in the root directory (the default temporary upload directory defined in php.ini file) 
	 * as well as the final upload directory */
	
	$allowedExts = array("gif", "jpeg", "jpg", "png");
	$url = "";
	//The global $_FILES will contain all the uploaded file information. In the following examples, "file" is the user defined name of the file input object in the html page.   
	//$_FILES['file']['name']: name of the file on the client machine
	//$_FILES['file']['type']: mime type of the file
	//$_FILES['file']['size']: size of the file in bytes
	//$_FILES['file']['tmp_name']: The temporary filename of the file in which the uploaded file was stored on the server.
	//$_FILES['file']['error']: The error code associated with this file upload.
	
	$temp = explode(".", $_FILES["vimage"]["name"]); //get the uploaded file name, use . to seperate the name and the extension into the temp array
	
	$extension = end($temp); //get the last element of the array, which will be the file extension
	if ((($_FILES["vimage"]["type"] == "image/gif")
		|| ($_FILES["vimage"]["type"] == "image/jpeg")
		|| ($_FILES["vimage"]["type"] == "image/jpg")
		|| ($_FILES["vimage"]["type"] == "image/pjpeg")
		|| ($_FILES["vimage"]["type"] == "image/x-png")
		|| ($_FILES["vimage"]["type"] == "image/png"))
		&& ($_FILES["vimage"]["size"] < 5000000)
		&& in_array($extension, $allowedExts))
  	{
  		if ($_FILES["vimage"]["error"] > 0)
    		{
    			echo "Return Code: " . $_FILES["vimage"]["error"] . "<br>";
    		}
  		else
    		{
    			
			if (is_uploaded_file($_FILES['vimage']['tmp_name'])) 
			{
   					echo "File ". $_FILES['vimage']['name'] ." uploaded successfully.\n";
   
			} else {
   				echo "unable to upload file ";
  
			}




    			if (file_exists("pics/" . $_FILES["vimage"]["name"]))
      			{
      					echo $_FILES["vimage"]["name"] . " already exists. ";
      			}
    			else
      			{
					$idName = date('Y_m_d') . "_" . time() . "_". str_replace("@","",$email) . "img." . $extension;
      				move_uploaded_file($_FILES["vimage"]["tmp_name"],
      				"img/user/" . $idName);												//change the url here according to where you want to store the pictures
					$url = "img/user/" . $idName;   //change the url here according to where you want to store the pictures
					return $url;
      			}
    		}
  	}
	else
  	{
  		//echo "Invalid image file<br/>";
  	}
}	

//Sanitize an input.
function sanitize($input)
{
	//Trim the input
	$input = trim($input);
	//Sanitize the string.  This also strips HTML tags.
	$input = filter_var($input, FILTER_SANITIZE_STRING);
	//Make quotation marks into non-sql injectable quotation marks (but keep them)
	//This allows us to keep names like "O'leary" and such in the database.
	$input = filter_var($input, FILTER_SANITIZE_MAGIC_QUOTES);
	//Return the filtered input.
	return $input;
}

//Sort an object array by the property "name", provided it exists.
function sortObjectsByName(&$inputArray)
{
	//Return NULL if there is an entry without a name
	//(ie somehow we didn't get an objects array with names)
	foreach($inputArray as $item)
		if(!$item->Name)
			return NULL;
	function compare($a, $b)
	{
		return strcmp($a->Name, $b->Name);
	}
	usort($inputArray, "compare");
}