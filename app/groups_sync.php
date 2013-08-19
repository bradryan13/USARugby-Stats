<?php
header('Location: ' . $_SERVER['HTTP_REFERER']);
include_once './include.php';

if (editCheck(1))
{
    // Enqueue Group Sync operation.
    Resque::enqueue('get_groups', 'GetGroups');

    $_SESSION['alert_message'] = "Group Sync enqueued.";
}
