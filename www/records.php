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
    $time_now  = time();
    $time_now -= $time_now % 3600;
    $time_from = strftime("%F %R", $time_now);
    $time_to = strftime("%F %R", $time_now + (1 * 60 * 60));
    $timerange = "$time_from - $time_to";
}
$timeprange = strftime("%F %R", $time_now - (1 * 60 * 60)) ." - ". strftime("%F %R", $time_now - (0 * 60 * 60));
$timenrange = strftime("%F %R", $time_now + (1 * 60 * 60)) ." - ". strftime("%F %R", $time_now + (2 * 60 * 60));
//print("<h1> $timeprange\n<br> ".strftime("%F %R", $time_now)." - ". strftime("%F %R", $time_now + (1 * 60 * 60)) ." <br> $timenrange</h1>");

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
        print("\n<br>");
        print(" mc_id:". $row["mc_id"]);
        print(" identifier:". $row["identifier"]);
        print(" latitude:". $row["latitude"]);
        print(" longitude:". $row["longitude"]);
        print(" Pm25:".$row["pm25"]);
        print(" Pm100:".$row["pm100"]);
    }

?>

<html>
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
        <a href="records.php?timerange=<?php print(urlencode($timeprange)); ?>">Previous Hour</a>
        <a href="records.php">Reset</a>
        <a href="records.php?timerange=<?php print(urlencode($timenrange)); ?>">Next Hour</a>
	    </center>
		<script>
			$(function()
				{
					$('input[name="timerange"]').daterangepicker({timePicker: true, startDate: moment("<?php print $time_from; ?>"), endDate: moment("<?php print $time_to; ?>"), locale: { format: 'YYYY/MM/DD hh:mm A'} });
				}
			);
		</script>
    </form>
</body>
</html>

