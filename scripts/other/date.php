<?php

$date = "2016-08-06T20:45:00Z";
$date = new DateTime($date);
$date->sub(new DateInterval("PT2H"));
echo $date->getTimestamp() . "<br/>";
echo $date->format('Y-m-d\TH:i:s');

?>