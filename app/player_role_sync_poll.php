<?php
include_once './config.php';
include_once './include_mini.php';
Resque::setBackend($config['redis_host']);
Resque::redis()->auth($config['redis_password']);
$tokens = $_POST['tokens'];
$done = 0;
$total = 0;
foreach (json_decode($tokens) as $player_id => $token) {
	$status = new Resque_Job_Status($token);
	if ($status->get() == '4') {
		$done += 1;
	}
	$total += 1;
}
print $done / $total * 100;
?>
