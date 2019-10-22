<?php //Keeping this separate allows this to be hidden from users with insufficient privileges.
//We don't want non-administrators seeing anything about how admins are created.
?>
			          <!-- Heading Of The Form -->
								<h1>Current countries list:</h1>
					<?php
					$stmt = $connection->prepare("select * from Country");
			    $stmt->execute();
			    while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
			          //you can use function count($row) to get the total number of rows
			      print '<tr>';
			    print " Country: ".$row["Name"]. "<br />";
			      print "</td>";


			    }



					?>
									<form action="#" id="form" method="post" name="form">
						<h1>Enter new country name</h1>
						<input placeholder="Country name" type = "text" name = "newCountry"/>
									<br />
									<input id="send" name="submit" type="submit" value="Submit">
								</form>
