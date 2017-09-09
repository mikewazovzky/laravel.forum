<?php

// $ php-cgi test.php parameter1=value1 parameter2=value2

$param1 = $_GET['parameter1']; 
$param2 = $_GET['parameter2'];

echo $param1;
echo $param2;
