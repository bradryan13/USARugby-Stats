<?php
include_once './config.php';
include_once './include_mini.php';
$status = new Resque_Job_Status($_POST['token']);
print $status->get();
?>
