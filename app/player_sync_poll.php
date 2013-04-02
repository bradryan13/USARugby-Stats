<?php
include_once './config.php';
include_once './include_mini.php';
Resque::setBackend($config['redis_host']);
Resque::redis()->auth($config['redis_password']);
$status = new Resque_Job_Status($_POST['token']);
print $status->get();
?>
