<?php

require "db_includes.php";

// Create Connection 
$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    die("Could not connect to database: " . mysqli_connect_error());
}

$identifier = "";
$latitude = "";
$longitude = "";
$mc_id = "";
$action = "New Location";
if (isset($_POST["action"])) {
    $action = $_POST["action"];
    if ($action == "New Location") {
        $identifier = $_POST["identifier"];
        $latitude = $_POST["latitude"];
        $longitude = $_POST["longitude"];
        if (verify_machine_values($identifier, $latitude, $longitude)) {
            $sql = "INSERT INTO machines(identifier, location) VALUES('$identifier', POINT($latitude, $longitude))";
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                print("Error: Error inserting new machine");
            }
        }
    } elseif ($action == "Edit Location") {
        $mc_id = $_POST["mc_id"];
        $sql = "SELECT mc_id,identifier,st_x(location) AS latitude,st_y(location) AS longitude FROM machines WHERE mc_id=$mc_id";
        $row = mysqli_fetch_assoc(mysqli_query($conn, $sql));
        $identifier = $row["identifier"];
        $latitude = $row["latitude"];
        $longitude = $row["longitude"];
        $action = "Modify Location";
    } elseif ($action == "Modify Location") {
        $mc_id = $_POST["mc_id"];
        $identifier = $_POST["identifier"];
        $latitude = $_POST["latitude"];
        $longitude = $_POST["longitude"];
        if (verify_machine_values($identifier, $latitude, $longitude)) {
            $sql = "UPDATE machines SET identifier=\"$identifier\", location=POINT($latitude, $longitude) WHERE mc_id=$mc_id";
            $result = mysqli_query($conn, $sql);
            if (!$result) {
                print("Error: Error updating machine details");
            }
            $identifier = "";
            $latitude = "";
            $longitude = "";
            $mc_id = "";
            $action = "New Location";
        } else {
            $action = "Modify Location";
        }
    } elseif ($action == "Delete Location") {
        $mc_id = $_POST["mc_id"];
        $sql = "DELETE FROM machines WHERE mc_id=$mc_id";
        $result = mysqli_query($conn, $sql);
        if (!$result) {
            print("Error: Error deleting row");
        }
        $mc_id = "";
        $action = "New Location";
    }

}

$sql = "SELECT mc_id,identifier,st_x(location) AS latitude,st_y(location) AS longitude FROM machines";
$result = mysqli_query($conn, $sql);

?>

<html>
    <style>
        /* Set the size of the div element that contains the map */
        #map {
            height: 400px;  /* The height is 400 pixels */
            width: 50%;  /* The width is the width of the web page */
        }
    </style>
    <script>
        var markers = [];
    </script>


<form method="POST">
    <input type=hidden name=mc_id value="<?php print $mc_id ?>">
    <h3>Machine Identifier:</h3>
    <input type="text" name="identifier" value="<?php print $identifier ?>">
    <h3>Latitude:</h3>
    <input type="text" name="latitude" value="<?php print $latitude ?>">
    <h3>Longitude:</h3>
    <input type="text" name="longitude" value="<?php print $longitude ?>">
    <br/>
    <br/>
    <input type="submit" name="action" value="<?php print $action ?>">
    <?php if ($action == "Modify Location") { ?>
        <input type="submit" name="action" value="Delete Location">
    <?php } ?>
</form>

<?php if (mysqli_num_rows($result) > 0) { ?>
<centre>
<div id="map"></div>
<table border=1>
    <tr><td>Machine Identifier</td><td>Location (Latitude, Longitude)</td><td></td></tr>   
    <?php
    while ($row = mysqli_fetch_assoc($result)) {
        $r_mc_id = $row["mc_id"];
        $r_identifier = $row["identifier"];
        $r_latitude = $row["latitude"];
        $r_longitude = $row["longitude"];

        print (
            "<tr>
                <form method=POST>
                    <input type=hidden name=action value=\"Edit Location\" >
                    <input type=hidden name=mc_id value=$r_mc_id>
                    <td> $r_identifier</td>
                    <td> $r_latitude, $r_longitude</td>
                    <td><input type=submit Value=Edit></td>
                    <script>
                        markers.push({location: {lat: $r_latitude, lng: $r_longitude}, description: \"$r_identifier\"});                       
                    </script>
                </form>    
            </tr>"
        );
    }
    ?>
</table>
</centre>
<script>
        // Initialize and add the map
        function initMap() {
            // The map center
            var c = {lat: 52.139561, lng: -0.464029};
            // The map
            var map =  new google.maps.Map( document.getElementById('map'), {zoom: 10, center: c});

            for (i = 0; i < markers.length; i++) {
                var marker = new google.maps.Marker({
                                                    position: markers[i]["location"],
                                                    map: map,
                                                    title: markers[i]["description"]
                                                    });
            }
        }
</script>
<script async defer
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCqmq8fGMZ4n2qfgvIezJTxF_pwGyWT0O4&callback=initMap">
</script>
</html>
<?php } ?>
<?php
mysqli_close($conn);
?>
