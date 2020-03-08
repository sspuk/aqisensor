<?php

$time_start = strtotime("2020-03-02 00:01");
$time_end = $time_start + 5 * 24 * 60 * 60;

$school = [0,0,0,0,0,0,0,1,3,0,0,0,0,0,0,2,3,0,0,0,0,0,0,0];
$shopping = [0,0,0,0,0,0,0,0,0,3,3,1,2,3,0,0,1,2,3,0,0,0,0,0];
$busyroad = [0,0,0,0,0,0,1,2,3,4,2,1,2,3,2,2,3,4,5,3,2,1,0,0];

$points[0]["id"] = 1;
$points[0]["weight"] = $busyroad;
$points[1]["id"] = 2;
$points[1]["weight"] = $school;
$points[2]["id"] = 3;
$points[2]["weight"] = $school;
$points[3]["id"] = 4;
$points[3]["weight"] = $shopping;

print "
DELETE FROM machines;
DELETE FROM records;

INSERT INTO machines(mc_id, identifier, location) VALUES(1, 'H8 V1 Roundabout', POINT(51.98967,-0.785079));
INSERT INTO machines(mc_id, identifier, location) VALUES(2, 'Giles Brook school entrance', POINT(51.994142,-0.791986));
INSERT INTO machines(mc_id, identifier, location) VALUES(3, 'Priory Rise school entrance', POINT(51.992539,-0.800418));
INSERT INTO machines(mc_id, identifier, location) VALUES(4, 'Morrisons supermarket car park', POINT(52.004311,-0.793932));

";


for($i = 0; $i<count($points); $i++) {
        for ($ts = $time_start; $ts < $time_end; $ts += 15 * 60) {
                $t = strftime("%F %R", $ts);
                $mc_id = $points[$i]["id"];
                $hour = intval(strftime("%H", $ts));
                $pm25 = 10 + rand(0,10) + $points[$i]["weight"][$hour] * rand(5, 15);
                $pm100 = 10 + rand(0,10) + $points[$i]["weight"][$hour] * rand(5, 15);
                print "INSERT INTO records(mc_id, time, pm25, pm100) VALUES($mc_id,'$t',$pm25,$pm100);\n";
        }
}
  ?>

