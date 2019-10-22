<?php //Keeping this separate allows this to be hidden from users with insufficient privileges.
//We don't want non-administrators seeing anything about how admins are created.
?>
			          <!-- Heading Of The Form -->
			            <H1>Create new administrator:</H1>
			              <form action="#" id="form" method="post" name="form">
			                <!-- Form contains a field for login and password -->
							<input name="vUsername" placeholder="Enter Username" type="text" value="<?php echo $username;?>" size = "100"/>
							<input name="vPassword" placeholder="Enter Password" type="password" value="<?php echo $password;?>" size ="100"/>
							<input name="vConfirm" placeholder="Confirm Password" type="password" value="" size ="100"/>
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
										//If the user has picked a type, and it's this index, make sure this is the default.
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