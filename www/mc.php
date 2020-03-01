<?php

require "db_includes.php";

// Create Connection 
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    die("Could not connect to database: " . mysqli_connect_error());
}

$action = "new";
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    if ($action == "new") {
        $identifier = $_POST["identifier"];
        $latitude = $_POST["latitude"];
        $longitude = $_POST["longitude"];
        if (verify_machine_values($identifier, $latitude, $longitude)) {
            $sql = "INSERT INTO machines(identifier, location) VALUES('$identifier', POINT($latitude, $longitude))";
            $result = mysqli_query($conn, $sql);
            if (!result) {
                print("Error: Error inserting new machine");
            }
        }
    } elseif ($action == "edit") {
        $mc_id = $_POST["mc_id"];
        $sql = "SELECT mc_id,identifier,st_x(location) AS latitude,st_y(location) AS longitude FROM machines WHERE mc_id=$mc_id";
        $row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
        $identifier = $row["identifier"];
        $latitude = $row["latitude"];
        $longitude = $row["longitude"];
        $action = "editsubmit";
    } elseif ($action == "editsubmit") {
        print "Save edited info";
    }
}

$sql = "SELECT mc_id,identifier,st_x(location) AS latitude,st_y(location) AS longitude FROM machines";
$result = mysqli_query($conn, $sql);

//print "result: ".$result;

?>

<html>
<form method="POST">
    <input type=hidden name=action value="<?php print $action ?>">
    <input type=hidden name=action value="<?php print $mc_id ?>">
    <h3>Machine Identifier:</h3>
    <input type="text" name="identifier" value="<?php print $identifier ?>">
    <h3>Latitude:</h3>
    <input type="text" name="latitude" value="<?php print $latitude ?>">
    <h3>Longitude:</h3>
    <input type="text" name="longitude" value="<?php print $longitude ?>">
    <br/>
    <br/>
    <input type="submit" name="submit">
</form>

<?php if (mysqli_num_rows($result) > 0) { ?>
<centre>
<table border=1>
    <tr><td>Machine Identifier</td><td>Location (Latitude, Longitude)</td><td></td></tr>   
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        print (
            "<tr>
                <form method=POST>
                    <input type=hidden name=action value=edit >
                    <input type=hidden name=mc_id value=".$row["mc_id"] .">
                    <td>". $row["identifier"]."</td>
                    <td> ". $row["latitude"].", ". $row["longitude"] ."</td>
                    <td><input type=submit Value=Edit></td>
                <form>    
            </tr>"
        );
    }
    ?>
</table>
</centre>
</html>
<?php } ?>
<?php
mysqli_close($conn);
?>
