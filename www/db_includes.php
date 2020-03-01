<?php

$db_host = "localhost";
$db_user = "pmeter";
$db_password = "db_pass";
$db_name = "pmeter";

#checking validity of the values
function verify_machine_values($identifier, $latitude, $longitude)
{
    $id_length = strlen($_POSTidentifier);
    if ($id_length < 5 && $id_lenght > 99) {
        print("<br>Error: Identifier must be in between 5 and 99 characters");
        return false;
    }
    if ($latitude < -90 || $latitude > 90) {
        print("<br> Error: Incorrect latitude value");
        return false;
    }
    if ($longitude < -180 || $longitude > 180 ) {
        print("<br>Error: Incorrect longitude value");
        return false;
    }

    return true;
}
?>