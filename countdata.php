<!DOCTYPE html>
<html>

	<head>
		<meta charset="UTF-8"/>
		<title>My Template</title>
		<link rel=Stylesheet href="style.css" type="text/css"/>
		 <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <link rel="stylesheet" href="/resources/demos/style.css">
<script src="https://code.jquery.com/jquery-1.12.4.js"></script> 
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  <script>
  $( function() {
    $( "#fromdate" ).datepicker({
    	dateFormat:'yy-m-d',
    });
  } );
   $( function() {
    $( "#todate" ).datepicker({
    	dateFormat:'yy-m-d',
    });
  } );
  </script>

	</head>

	<body>

<div id="main">


<?php
$servername = "_";		//add your servername here 
 
$username = "_";		//add your username here 
 
$password = "_";		//add your password here 
 
$db = "_";				//add the name of your database here 

$conn = mysqli_connect($servername, $username, $password,$db);
// Check connection
 
if (!$conn) {
 
   die("Connection failed: " . mysqli_connect_error());
 
}

                
  ?>



        <div class="container containerinput" style="margin-top:15px">
          <!-- Feedback Form Starts Here -->

          <!-- Heading Of The Form -->

            <!-- Feedback Form Ends Here -->

              
				<select id="gender" name="gender">
					<option value="">Select Gender</option>

                	<option value="male">Male</option>
                	<option value="female">Female</option>
            	</select><br/><br/>	
            	<select id="state" name="state">
					<option value="">Select State</option>
                	<?php 
                	$query = "SELECT distinct StateProvinceID FROM USER";
                	$result = mysqli_query($conn, $query);
                	while ($row = mysqli_fetch_assoc($result)) {
                		$sid=$row['StateProvinceID'];
              
                		$getresult=mysqli_fetch_assoc(mysqli_query($conn,$getsnames));
                		$sname=$getresult['Name'];
				if($sname!='')
                		{
                	?>
                	<option value="<?php echo $sid; ?>"><?php echo $sname; ?></option>

                	<?php
                		}
				}	
                	?>
            	</select><br/><br/>		
				<select id="country" name="vCountry">
					<option value="">Select Country</option>
                	<?php 
                	$query = "SELECT distinct CountryID FROM USER";
                	$result = mysqli_query($conn, $query);
                	while ($row = mysqli_fetch_assoc($result)) {
                		$cid=$row['CountryID'];
                		
                		$getresult=mysqli_fetch_assoc(mysqli_query($conn,$getsnames));
                		$cname=$getresult['Name'];
                	?>
                	<option value="<?php echo $cid; ?>"><?php echo $cname; ?></option>

                	<?php
                		}
                	?>
                	
            	</select><br/><br/>
              
				<select id="degree" name="vDegree">
					<option value="">Select Degree</option>
				<?php 
                	$query = "SELECT ID,DegreeLevel FROM EDUCATION_TYPE";
                	$result = mysqli_query($conn, $query);
                	while ($row = mysqli_fetch_assoc($result)) {
                		$degree=$row['DegreeLevel'];
                		$did=$row['ID'];
                		
                		
                	?>
                	<option value="<?php echo $did; ?>"><?php echo $degree; ?></option>

                	<?php
                		}
                	?>
            	</select><br/><br/><br/>
            	 Select From date:<input type="text" id="fromdate" >
            	 Select To date:<input id="todate" type="text">						
							
              <input id="submitcount" name="submit" type="submit" value="Submit">
              



      </div>
      <div id="res">

      </div>
</div>
<script type="text/javascript">
	$(document).ready(function(){
		$(document).on('click','#submitcount',function(){
			var gender=$('#gender').val();
			var state=$('#state').val();
			var country=$('#country').val();
			var degree=$('#degree').val();
			var fromdate=$('#fromdate').val();
			var todate=$('#todate').val();
			$.ajax({
				type:'POST',
				url:'displaydata.php',
				data:'gender='+gender+'&state='+state+'&country='+country+'&degree='+degree+'&fromdate='+fromdate+'&todate='+todate,
				success:function(data)
				{
					$('#res').html(data);
				}
			})
		});
	});

</script>

  </body>
</html>
