<?php //Keeping this separate allows this to be hidden from users with insufficient privileges.
//We don't want non-administrators seeing anything about how admins are created.
?>
			          <!-- Heading Of The Form -->
			            <H1>Editing administrator: "<?php echo $username; ?>"</H1>
						<p>If a field is left blank, it will not be changed.</p>
			              <form action="#" id="form" method="post" name="form">
			                <!-- Form contains a field for login and password -->
							<input name="vPassword" placeholder="Enter New Password" type="password" value="" size ="100"/>
							<input name="vConfirm" placeholder="Confirm New Password" type="password" value="" size ="100"/>
							<?php
							if(!$gotAdminTypes)
							{
								echo '<p>Failed to get admin types</p>';
							}
							else
							{
								echo '<select name="permissions" >';
									for($index = 1; $index < count($adminTypes); $index++)
									{
										//Create an option from this entry's values.
										echo '<option value ="';
										echo $adminTypes[$index]->ID;
										echo '"';
										//Set the target user's current type to be the default.
										if(isset($type) and !empty($type))
										{
											if( $adminTypes[$index]->ID == $type )
												echo ' selected = "selected"';
										}
										echo '>';
										echo $adminTypes[$index]->Description;
										echo '</option>';
									}
								echo '</select>';
							}
							?>
							</select>
			              <br />
			              <input id="send" name="submit" type="submit" value="Submit">
						</form>
						<br />
						<form action = "confirmDeleteAdmin.php" method="get" name="delete">
							<Button id="send" name="username" type="submit" value="<?php echo $username;?>">Delete user</button>
						</form>