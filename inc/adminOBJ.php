<?php
class Admin 
{
	private $type;
	private $username;
	//By default, access level is 0, which gives no access of any kind.  Default name is "###INVALID_USER###".
	public function __construct($initialType = 0, $initialName = "###INVALID_USER###")
	{
		$this->type = $initialType;
		$this->username = $initialName;
	}
	
	//Below are getters for admin status.
	public function isSuperAdmin()
	{
		return $this->type == 1;
	}
	public function isAdmin()
	{
		return $this->type == 2;
	}
	public function isCoordinator()
	{
		return $this->type == 3;
	}
	public function isAtLeastAdmin()
	{
		return ($this->isSuperAdmin() or $this->isAdmin());
	}
	public function isAtLeastCoordinator()
	{
		return ($this->isAtLeastAdmin() or $this->isCoordinator());
	}
	/* End Admin status getters */
	
	//Add admin function.  Takes a pass-by-reference to the feedback array,
	//A connection to a database, and the desired username, password, and type of the new admin.
	public function addAdmin(&$feedback, $connection, $username, $password, $type)
	{
		//Only possible if the user is a super admin.
		if($this->isSuperAdmin())
		{
			//Create the procedure.
			$procedure = 'call sp_create_new_admin("' . $username . '","' . $password . '",' . $type . ');';
			//Execute the procedure.
			$result = $connection->query($procedure);
			if($result === FALSE)
				array_push($feedback, '<p style = color: red">Error querying database. Admin not created</p>');
			else
				array_push($feedback, '<p>Admin created successfully!</p>');
		}
		else
		{
			//If we got here, append a privilege error to the feedback array.
			//The error should be described as unexpected, because it shouldn't
			//be possible to call this method w/o proper privileges.
			array_push($feedback, '<p style = "color: red">Unexpected error:  Insufficient privileges</p>');
		}
	}
	
	public function editUserInfo()
	{
		//Only possible if at least an admin.
		if($this->isAtLeastAdmin())
		{
			
		}
	}
	
	public function deleteUser($targetUser)
	{
		//Only possible if at least an admin.
		if($this->isAtLeastAdmin())
		{
			
		}
	}
	
	public function viewUserInfo($targetUser)
	{
		//Only possible if at least coordinator.
		if($this->isAtLeastCoordinator())
		{
			
		}
	}
	
	public function downloadResume($targetUser)
	{
		//Only possible if at least coordinator.
		if($this->isAtLeastCoordinator())
		{
			
		}
	}
}
?>