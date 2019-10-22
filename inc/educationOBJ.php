<?php
class Education
{
	//For now, this class is primarily a collection of public variables.
	var $degree = "";
	var $school = "";
	var $major = "";
	var $gradyear = "";
	
	public function __construct()
	{
		
	}
	public function isEmpty()
	{
		//If everything's empty, return true.
		if( empty($this->degree) and empty($this->school) and empty($this->major) and empty($this->gradyear) )
			return true;
		else
			return false;
	}
	public function isFull()
	{
		//If everything is filled in, return true.  Otherwise, return false.
		if( !empty($this->degree) and !empty($this->school) and !empty($this->major) and !empty($this->gradyear) )
			return True;
		else
			return False;
	}
}
?>