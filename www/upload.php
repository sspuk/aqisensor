<?php
require "db_includes.php";

// Create Connection 
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    die("Could not connect to database: " . mysqli_connect_error());
}

$input = (array) json_decode(file_get_contents('php://input'), TRUE);
$mc_id = intval($input["mc_id"]);
$pm25 = intval($input["pm25"]);
$pm10 = intval($input["pm10"]);

if (!check_mc_id($conn, $mc_id)) {
    header("HTTP/1.1 404 Not Found");
    exit('Not found');
}

if (!check_record($pm25, $pm10)) {
    header("HTTP/1.1 422 Unprocessable Entity");
    exit('Invalid values');  
}

$sql = "INSERT INTO records(mc_id, pm25, pm100) VALUES($mc_id, $pm25, $pm10)";
$result = mysqli_query($conn, $sql);
if (!$result) {
    header("HTTP/1.1 500 Internal Server Error");
    exit('Could not insert into DB');  
}   
header("HTTP/1.1 200 OK");
exit();
?>