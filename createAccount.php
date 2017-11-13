<html >
  <head>
    <meta charset="UTF-8">
   
  </head>
  <body>
<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'Exception.php';
require 'PHPMailer.php';
require 'SMTP.php';




$mongo = new MongoDB\Driver\Manager("");


$option = str_split('abcdefghijklmnopqrstuvwxyz'
                 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                 .'0123456789');
    shuffle($option); 
    $code = '';
    foreach (array_rand($option, 15) as $k) $code .= $option[$k];



$inName = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
if(strpos($inName, "User") !== false){
   $inName = strtolower($inName);
}
if (strpos($inName, ' ') !== false) {
    
   $firstName = substr($inName, 0, strpos($inName, ' ')) ;
   $lastName = substr($inName, strpos($inName, ' '));
}
else{
    $firstName = $inName;
    $lastName = '';
}
$inUser = filter_var(strtolower($_POST['username']), FILTER_SANITIZE_STRING);
$inPass = $_POST['password'];
$filter = ['User' => $inUser];
$options = [];
$query = new \MongoDB\Driver\Query($filter, $options);
$rows   = $mongo->executeQuery('login.login', $query); 
$json = $rows->toArray();
$newJson = json_encode($json);

if($newJson !== "[]"){
    echo "That email is already registered!";
}
else{
   $seed = str_split('abcdefghijklmnopqrstuvwxyz'
                 .'ABCDEFGHIJKLMNOPQRSTUVWXYZ'
                 .'0123456789!@#$%^&*()[]|{}_+-=?<>,.');
    shuffle($seed); 
    $rand = '';
    foreach (array_rand($seed, 18) as $k) $rand .= $seed[$k];

    $hashedPass = hash('sha256', $_POST['password'].$rand);
    $bulk = new MongoDB\Driver\BulkWrite;
    $dbSalt = $rand;
    $dbPass = $hashedPass;
    $uuid = uniqid();
    $comments  = "";
    $document = ['_id' => new MongoDB\BSON\ObjectId, 'Name' => $inName,'User' => $inUser, 'Salt' => $dbSalt, 'Password' => $dbPass, 'UUID' => $uuid, 'Verify Code' => $code,'Verify Status' => 0, 'Comments' => $comments];
    $_id1 = $bulk->insert($document);
    $result = $mongo->executeBulkWrite('login.login', $bulk);
    



$mail = new PHPMailer;
$mail->isSMTP();
$mail->Host = 'smtp.gmail.com';
$mail->Port = 587;
$mail->SMTPAuth = true;
$mail->Username = "";
$mail->Password = "";
$mail->SMTPSecure = 'tls';
$mail->From = '';
$mail->FromName = "";
$mail->addAddress($inUser, $inName);
$mail->isHTML(false);
$mail->Subject = 'Verify Your IoT SmartHouse Account';
$body = 'Dear '.$firstName.",\r\n\r\nThank you for creating an IoT SmartHouse account. By doing so you are taking steps to become a pioneer of the Internet of Things revolution. To verify your account, use this confirmation code: ".$code.". We sincerely appreciate your support!";
$body .= "\r\n\r\nKevin Palani & Matthew Pham";
$mail->Body = ($body);
if(!$mail->send()) {
    echo 'Message could not be sent. Please sign up with a valid email.';
} else {

    echo '
    
<center>

       <h1>We have sent you a verification email. Please check your email and enter the code below!</h1>
      
          
          <form action="code.php" method="POST">
          
          
 
            <form name="verify"><input
name="verify" type="text" required autocomplete="off" placeholder="Verification Code">
           
          
  
     
          <br>
             <br>
           <input type="submit">
    
   
   </center>
          ';
}
}
?>
</body>


</html>