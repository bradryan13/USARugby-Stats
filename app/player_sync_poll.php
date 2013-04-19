<?php
include_once './config.php';
include_once './include_mini.php';
Resque::setBackend('redis://redis:' . $config['redis_password'] . '@' . $config['redis_host']);
$status = new Resque_Job_Status($_POST['token']);
print $status->get();
?>
