<?php

$inCode = $_POST['verify'];
$mongo = new MongoDB\Driver\Manager("");
$filter = ['Verify Code' => $inCode];
$options = [];
$query = new \MongoDB\Driver\Query($filter, $options);
$rows   = $mongo->executeQuery('login.login', $query); 
$json = $rows->toArray();
$newJson = json_encode($json);
if($newJson == "[]"){
    echo "You have entered an incorrect Verification Code. Please try again.
    <center>

       
      
          
          <form action='code.php' method='POST'>
          
          
 
          <form name='verify'> <input name='verify' type='text' required autocomplete='off' placeholder='Verification Code'>
           
          
  
     
          <br>
             <br>
           <input type='submit'>
    
   
   </center>";
}
else{
    echo "Your account has been verified!";
    echo "<br> <a href='login.html'>Click here to Login!</a>";
    function get_string_between($string, $start, $end){
    $string = ' ' . $string;
    $ini = strpos($string, $start);
    if ($ini == 0) return '';
    $ini += strlen($start);
    $len = strpos($string, $end, $ini) - $ini;
    return substr($string, $ini, $len);
}

    $query1 = new \MongoDB\Driver\Query($filter, $options);
    $rows1   = $mongo->executeQuery('login.login', $query1); 
    $json1 = $rows1->toArray();
    $newJson1 = json_encode($json1);
    $objectID = substr($newJson1, strrpos($newJson1, "$oid") + 17, 24);
     $bulk = new MongoDB\Driver\BulkWrite;
     $var = explode("User", $newJson);
$newVar = explode("Name", $var[0]);
$name = str_replace('"', "", get_string_between($newVar[1], ':', ','));
$var1 = explode("Salt", $newJson);
$newVar1 = explode("User", $var1[0]);
$inUser = str_replace('"', "", get_string_between($newVar1[1], ':', ','));

    
    $dbSalt = substr($newJson1, strrpos($newJson1, "Salt") + 7, 18);
    $dbPass = substr($newJson1, strrpos($newJson1, "Password") + 11, 64);
    $uuid = substr($newJson1, strrpos($newJson1, "UUID") + 7, 13);
    $comments  = "";
    $document = ['_id' => new \MongoDB\BSON\ObjectID($objectID), 'Name'=> $name ,'User' => $inUser, 'Salt' => $dbSalt, 'Password' => $dbPass, 'UUID' => $uuid, 'Verify Code' => $inCode, 'Verify Status' => 1, 'Comments' => $comments];
    $_id1 = $bulk->update($filter, $document);
    $result = $mongo->executeBulkWrite('login.login', $bulk);
    
    
}
?>