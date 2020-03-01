<?php

$db_host = "localhost";
$db_user = "pmeter";
$db_password = "db_pass";
$db_name = "pmeter";

#checking validity of the values
function verify_machine_values($identifier, $latitude, $longitude)
{
    $id_length = strlen($_POST["identifier"]);
    if ($id_length < 5 && $id_length > 99) {
        print("<br>Error: Identifier must be in between 5 and 99 characters");
        return false;
    }
    if (!is_numeric($latitude) || !is_numeric($longitude)) {
        print("<br> Error: Incorrect latitude/longitude value");
        return false;
    }

    $latitude = intval($latitude);
    $longitude = intval($longitude);

    if ($latitude < -90 || $latitude > 90 || $latitude == "") {
        print("<br> Error: Incorrect latitude value");
        return false;
    }
    if ($longitude < -180 || $longitude > 180) {
        print("<br>Error: Incorrect longitude value");
        return false;
    }

    return true;
}

function check_mc_id($mysql_conn, $mc_id)
{
    $sql = "SELECT * from machines where mc_id=$mc_id";
    $result = mysqli_query($mysql_conn, $sql);
    if (mysqli_num_rows($result) != 1) {
        print("Invalid Machine id");
        return false;
    }

    return true;
}
function check_record($pm25, $pm100)
{
    if (!(is_numeric($pm25) && is_numeric($pm100))) {
        print("Invalid PM 2.5/10 values");
        return false;
    }
    if ($pm25 < 0 || $pm25 > 1000 || $pm100 < 0 || $pm100 > 1000) {
        print("Invalid PM 2.5/10 values");
        return false;
    }
    return true;
}


?>