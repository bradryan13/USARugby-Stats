<?php
header('Location: ' . $_SERVER['HTTP_REFERER']);
include_once './include.php';
use Source\APSource;
if (editCheck(1)) {
  include './config.php';
  if ($auth_disabled) {
    $client = APSource::BasicAuthFactory($_SESSION['user'], $_SESSION['password']);
  }
  else {
    $client = APSource::SessionSourceFactory();
  }

  $members = $client->getGroupMembers($config['admin_group_uuid']);
  $added = 0;
  foreach ($members as $member) {
    $user = $db->getUser($member->uuid);
    if (!$user) {
      $user_info = array(
        'login' => '',
        'team' => 0,
        'access' => 1,
        'uuid' => $member->uuid,
      );

      if ($db->addUser($user_info)) {
        $added ++;
      }
    }
  }

  $_SESSION['alert_message'] = "Created " . $added . " users.";
}