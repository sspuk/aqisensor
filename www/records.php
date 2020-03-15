<?php

require "credentials.php";
require "db_includes.php";

$conn = mysqli_connect($db_host, $db_user, $db_password, $db_name);
if (!$conn) {
    die("Could not connect to database: " . mysqli_connect_error());
}

if (isset($_GET["timerange"])) {
    $timerange = $_GET["timerange"];
    $ts = explode(" - ", $timerange);
    $time_from = $ts[0];
    $time_to = $ts[1];
    $time_now = strtotime($time_from);
} else {
    $time_now  = strtotime("2020-03-04 12:00");
    $time_now -= $time_now % 3600;
    $time_from = strftime("%F %R", $time_now);
    $time_to = strftime("%F %R", $time_now + (1 * 60 * 60));
    $timerange = "$time_from - $time_to";
}
$timeprange = strftime("%F %R", $time_now - (1 * 60 * 60)) ." - ". strftime("%F %R", $time_now - (0 * 60 * 60));
$timenrange = strftime("%F %R", $time_now + (1 * 60 * 60)) ." - ". strftime("%F %R", $time_now + (2 * 60 * 60));
//print("<h1> $timeprange\n<br> ".strftime("%F %R", $time_now)." - ". strftime("%F %R", $time_now + (1 * 60 * 60)) ." <br> $timenrange</h1>");


?>

<html>
<?php require "headers.php";?>
<style>
        /* Set the size of the div element that contains the map */
        #map_pm25 {
            height: 400px;  /* The height is 400 pixels */
            width: 50%;  /* The width is the width of the web page */
        }
        #map_pm100 {
            height: 400px;  /* The height is 400 pixels */
            width: 50%;  /* The width is the width of the web page */
        }
</style>

<body>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
	<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
	<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
	<form>
        <center>
	    Time Range: <input type="text" name=timerange />
	    <input type=submit name=submit value=Submit />
        <br>
        <a href="records.php?timerange=<?php print(urlencode($timeprange)); ?>">Previous Hour</a>|
        <a href="records.php">Reset</a>|
        <a href="records.php?timerange=<?php print(urlencode($timenrange)); ?>">Next Hour</a>
	    </center>
		<script>
			$(function()
				{
					$('input[name="timerange"]').daterangepicker({timePicker: true, startDate: moment("<?php print $time_from; ?>"), endDate: moment("<?php print $time_to; ?>"), locale: { format: 'YYYY/MM/DD HH:MM'} });
				}
			);
		</script>
    </form>

    <centre>
    <br><h2>PM 2.5</h2><h4><?php print($timerange); ?></h4>
    <br><div id="map_pm25"></div>
    <br>
    <br><h2>PM 10</h2><h4><?php print($timerange); ?></h4>
    <br><div id="map_pm100"></div>
    </centre>
</body>

<script>
var pm25_HeatmapData = [];
function pm25_HeatmapData_generate(map) {
    var bounds = new google.maps.LatLngBounds();

<?php
$sql = "SELECT 
            records.mc_id as mc_id,
            machines.identifier as identifier,
            st_x(machines.location) AS latitude,
            st_y(machines.location) AS longitude,
            max(records.pm25) AS pm25
            FROM records
            INNER JOIN machines ON machines.mc_id = records.mc_id
            WHERE records.time >= '$time_from' AND records.time <= '$time_to'
            GROUP BY records.mc_id;";

//print "<h1> $sql</h1>";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)){
    $mc_id = $row["mc_id"];
    $identifier = $row["identifier"];
    $latitude = $row["latitude"];
    $longitude = $row["longitude"];
    $pm25 = $row["pm25"];

    print("\tmyLatLng = new google.maps.LatLng($latitude, $longitude);\n");
    print("\tpm25_HeatmapData.push({location: myLatLng, weight:$pm25});\n");
    print("\tbounds.extend(myLatLng);\n\n");
 }
?>
    map.fitBounds(bounds);
}

var pm100_HeatmapData = [];
function pm100_HeatmapData_generate(map) {
    var bounds = new google.maps.LatLngBounds();

<?php
$sql = "SELECT 
            records.mc_id as mc_id,
            machines.identifier as identifier,
            st_x(machines.location) AS latitude,
            st_y(machines.location) AS longitude,
            max(records.pm100) AS pm100
            FROM records
            INNER JOIN machines ON machines.mc_id = records.mc_id
            WHERE records.time >= '$time_from' AND records.time <= '$time_to'
            GROUP BY records.mc_id;";

//print "<h1> $sql</h1>";
$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)){
    $mc_id = $row["mc_id"];
    $identifier = $row["identifier"];
    $latitude = $row["latitude"];
    $longitude = $row["longitude"];
    $pm100 = $row["pm100"];

    print("\tmyLatLng = new google.maps.LatLng($latitude, $longitude);\n");
    print("\tpm100_HeatmapData.push({location: myLatLng, weight:$pm100});\n");
    print("\tbounds.extend(myLatLng);\n\n");
 }
?>
    map.fitBounds(bounds);
}

</script>

<script>
        // Initialize and add the map
        function initMap() {
            // The pm 2.5 map
            var map_pm25 =  new google.maps.Map( document.getElementById('map_pm25'));
            pm25_HeatmapData_generate(map_pm25);
            var pm25_heatmap = new google.maps.visualization.HeatmapLayer({ data: pm25_HeatmapData, radius: 40, maxIntensity: 100});
            pm25_heatmap.setMap(map_pm25);

            // The pm 10 map
            var map_pm100 =  new google.maps.Map( document.getElementById('map_pm100'),);
            pm100_HeatmapData_generate(map_pm100);
            var pm100_heatmap = new google.maps.visualization.HeatmapLayer({ data: pm100_HeatmapData, radius: 40, maxIntensity: 100});
            pm100_heatmap.setMap(map_pm100);

        }
</script>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php print($google_api_key); ?>&callback=initMap&libraries=visualization"></script>
</html>

