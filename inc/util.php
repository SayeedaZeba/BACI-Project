<?php


/* This function will generate a list of states as a drop down list
*/
function stateOptionList()
{  	
	$list = '<option value = "IN">Indiana</option>
  		    <option value = "NY">New York</option>
  		    <option value = "IL">Illinois</option>
  		    <option value = "FL">Florida</option>
  		    <option value = "CO">Colorado</option>';

	return $list;
}




/* This function will generate a list of birth years based on the current year.
* It will start from the current year and provide a 100 year list in decending order.
* The parameter $bo is the default selected year.
*/
function birthOptionList($bo){

      $today = getdate();
      $thisYear = $today['year'];
      $list = "";
      for($i = $thisYear; $i > ($thisYear - 100); $i--){
             if ($i == $bo)
                $list = $list."<option value=\"".$i."\" selected = \"selected\">".$i."</option>" ;
             else  $list = $list."<option value=\"".$i."\">".$i."</option>" ;
      }
      return $list;

}

/* This function will generate a list of 10 years up till the current year. */
function tenYearList($y){

      $today = getdate();
      $thisYear = $today['year'];
      $list = "";
      for($i = $thisYear; $i > ($thisYear - 10); $i--){
             if ($i == $y)
                $list = $list."<option value=\"".$i."\" selected = \"selected\">".$i."</option>" ;
             else  $list = $list."<option value=\"".$i."\">".$i."</option>" ;
      }
      return $list;

}

/* This function will generate a list of $y years from the current year. */
function futureYearList($y){

      $today = getdate();
      $thisYear = $today['year'];
      $list = "";
      for($i = $thisYear; $i < ($thisYear + $y); $i++){
             if ($i == $y)
                $list = $list."<option value=\"".$i."\" selected = \"selected\">".$i."</option>" ;
             else  $list = $list."<option value=\"".$i."\">".$i."</option>" ;
      }
      return $list;

}




/* This function will generate a dropdown list of numbers. The first parameter is the maximum value in the list,
*  the second parameter is the interval between values.
*/
function numberOptionList($n, $j)
{	
	$res = '';
	for ($i=$j;$i<=$n;$i=($i+$j))
	{	
		$res = $res.'<option value="'.$i.'">'.$i.'</option>';
	
	}

	return $res;
}


/* This function generates a month drop down list, with this month as default. */
function monthList($m){

      $list = "";
      for($i = 1; $i <13; $i++){
             if ($i == $m)
                $list = $list."<option value=\"".$i."\" selected = \"selected\">".$i."</option>" ;
             else  $list = $list."<option value=\"".$i."\">".$i."</option>" ;
      }
      return $list;
    		
}

/* This function generates a day drop down list */
function dayList($d){

      $list = "";
      for($i = 1; $i <31; $i++){
             if ($i == $d)
                $list = $list."<option value=\"".$i."\" selected = \"selected\">".$i."</option>" ;
             else  $list = $list."<option value=\"".$i."\">".$i."</option>" ;
      }
      return $list;

	
}


/* This function generates a random code with letters and digits.
* The paramter tells the how long the code should be.
*/
function randomCodeGenerator($length){
          $code = "";
         for($i = 0; $i<$length; $i++){
             //generate a random number between 1 and 35
             $r = mt_rand(1,35);
             //if the number is greater than 26, minus 26 will generate a digit between 0 and 9
             if ($r > 26) {
                $r = $r - 26;
                $code = $code.$r ;
            }
             else {    //it's between 1 and 26, generate a character

                 $code = $code.toChar($r);
             }

         }
         return $code;

}

function toChar($digit){
         $char = "";
         switch ($digit){
                case 1: $char = "A"; break;
                case 2: $char = "B"; break;
                case 3: $char = "C"; break;
                case 4: $char = "D"; break;
                case 5: $char = "E"; break;
                case 6: $char = "F"; break;
                case 7: $char = "G"; break;
                case 8: $char = "H"; break;
                case 9: $char = "I"; break;
                case 10: $char = "J"; break;
                case 11: $char = "K"; break;
                case 12: $char = "L"; break;
                case 13: $char = "M"; break;
                case 14: $char = "N"; break;
                case 15: $char = "O"; break;
                case 16: $char = "P"; break;
                case 17: $char = "Q"; break;
                case 18: $char = "R"; break;
                case 19: $char = "S"; break;
                case 20: $char = "T"; break;
                case 21: $char = "U"; break;
                case 22: $char = "V"; break;
                case 23: $char = "W"; break;
                case 24: $char = "X"; break;
                case 25: $char = "Y"; break;
                case 26: $char = "Z"; break;
                default: "A";

         }
         return $char;
}

/*This function prevents malicious users enter multiple email addresses into the email box
*It makes sure that only one email is entered into the email box.
*/
function spamcheck($field)
  {
  //filter_var() sanitizes the e-mail
  //address using FILTER_SANITIZE_EMAIL. It removes all illegal email characters
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

/*This function will validate if user created a strong password
* Longer than 12 characters and alphanumeric letters.
*/
function pwdValidate($field){
	$field = trim($field);
	if (strlen($field) < 12){
		return false;
	}
	else {
		//go through each character and find if there is a number or letter
		$letter = false;
		$number = false;
		$chars = str_split($field);
		for($i = 0; $i<strlen($field); $i++){
			if (preg_match("/[A-Za-z]/",$chars[$i])){
				$letter = true;
				break;
			}

		}

		for($i = 0; $i<strlen($field); $i++){
			if (preg_match("/[0-9]/",$chars[$i])){
				$number = true;
				break;
			}

		}
		if (($letter == true) and ($number == true)){
			return true;
		}
		else return false;


		
 
	}

	

	
}


//This function will sanitize text input from the web form before inserting into the database
function sqlReplace($text){
 	 	
 	  $search = array(
    '@<script[^>]*?>.*?</script>@si',   // Strip out anything between the javascript tags
    '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
    '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
  );

    $text = preg_replace($search, '', $text);

    //the function below converts special characters to HTML entities, e.g. < becomes &lt;
    //read here about this function - http://php.net/manual/en/function.htmlspecialchars.php
    $text = htmlspecialchars($text, ENT_QUOTES);
        
  	return $text;
} 


?>