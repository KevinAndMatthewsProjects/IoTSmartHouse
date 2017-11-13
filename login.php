<html>
    <head>
        <meta charset="utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/main.css" />
			<link rel="stylesheet" href="assets/css/main.1.css" />
    </head>
    
    <body>
<?php
$mongo = new MongoDB\Driver\Manager("");

$inUser = filter_var(strtolower($_POST['username']), FILTER_SANITIZE_STRING);
$filter = ['User' => $inUser];
$options = [];
$query = new \MongoDB\Driver\Query($filter, $options);
$rows   = $mongo->executeQuery('login.login', $query); 
$json = $rows->toArray();
$newJson = json_encode($json);

if($newJson !== "[]"){
     function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}
     $var = explode("User", $newJson);
$newVar = explode("Name", $var[0]);
$name = str_replace('"', "", get_string_between($newVar[1], ':', ','));
if (strpos($name, ' ') !== false) {
    
   $firstName = substr($name, 0, strpos($name, ' ')) ;
   $lastName = substr($name, strpos($name, ' '));
}
else{
    $firstName = $name;
    $lastName = '';
}
$user = substr($newJson, strrpos($newJson, $inUser), strlen($inUser));
$salt = substr($newJson, strrpos($newJson, "Salt") + 7, 18);
$password = substr($newJson, strrpos($newJson, "Password") + 11, 64);
$uuid = substr($newJson, strrpos($newJson, "UUID") + 7, 13);
$verifyStatus = substr($newJson, strrpos($newJson, "Verify Status") + 15 , 1);
$hashedPassword = hash('sha256', $_POST['password'].$salt);
   session_start();
   $_SESSION['user'] = $user;

if($verifyStatus == 1){
    if($hashedPassword == $password){
    	//$greetings = array("Ciao, ", "Hello, ", "Bonjour, ", "Hola, ", "Xin Ch&agrave;o, ", "Namaste, ");
    	$greetings = array("Hello, ");
	$filter = ['User' => $inUser];
	$options = [];
	$query = new \MongoDB\Driver\Query($filter, $options);
	$rows   = $mongo->executeQuery('devices.device', $query); 
	$notJson = $rows->toArray();
	$stillNotJson = json_encode($notJson[0]);
    $json = json_decode($stillNotJson, true);
    $deviceArray = $json['Devices'];
     echo "
         
         <section id='main' class='wrapper'>
				<div class='container'>
				<div align='right'><b>".$name."&nbsp;&nbsp;&nbsp;&nbsp;</b><br><b>".$user."</b>&nbsp;&nbsp;&nbsp;&nbsp;<br><b>Your IoT Base's UUID: ".$uuid."&nbsp;&nbsp;&nbsp;&nbsp;</b></div>
		<br>
				<header class='major special'>
						<h2>".$greetings[array_rand($greetings, 1)].$firstName."</h2>
						<p>Manage your IoT Devices</p>
					</header>

         

	
       
        
							&nbsp;&nbsp;&nbsp;&nbsp;<h2>Your IoT Devices</h2>
			
							<div class='table-wrapper'>
								<table>
									<thead>
										<tr>
											<th><h4>IoT Device</h4></th>
											<th><h4>Description</h4></th>
											<th><h4>Status</h4></th>
										</tr>
									</thead>";
    
	echo "<tbody>";
 $boolean = true;
    foreach($deviceArray as $value => $key){
         $deviceInfo = json_decode($key, true);
        
      foreach($deviceInfo as $name => $arr){
          $status = $arr["status"];
         $description = $arr["description"];
        
         if($status == "OFF"){
         	$buttonStatus = "Turn ON";
         	$buttonChange = "ON";
         }else if($status == "ON"){
         	$buttonStatus = "Turn OFF";
         	$buttonChange = "OFF";
         }else if($status == "OPEN"){
         	$buttonStatus = "CLOSE";
         	$buttonChange = "OPEN";
         }else{
         	$buttonStatus = "OPEN";
         	$buttonChange = "CLOSE";
         }
        
		if($boolean == true){
			echo "<tr class = 'bye'>";
		}
		
		else {
			echo "<tr class = 'hi'>";
		}
		
											echo " 
											<td>".$name."</td>
											<td><div contenteditable>".$description."</div></td>
											<td><b>".$status."</b></td>
											<td><a href='#'' class='button special'>".$buttonStatus."</a></td>
											</tr>
											";
											  

      }
     
       echo "
       </tbody>
       ";
  $boolean = !$boolean;
    }
   
       echo " </table>";
     echo "<br>
     <h3><a href='index.html' class='button'>Click Here to Logout</a></h3>";
         
    }else{
         echo "Invalid Password";
    }
}else{
    echo "Your account has not been verified. Please check your email and enter your verification code.";
      echo " <center>

       
      
          
          <form action='code.php' method='POST'>
          
          
 
          <form name='verify'> <input name='verify' type='text' required autocomplete='off' placeholder='Verification Code'>
           
          
  
     
          <br>
             <br>
           <input type='submit'>
    
   
   </center>";
}

    
}
else{
    echo "Invalid Email Address";
}

?>
</body>


</html>