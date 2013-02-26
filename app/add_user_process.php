<?php
include_once './include_mini.php';

if (editCheck(1)) {
    $email = mysql_real_escape_string($request->get('login'));
    $access = $request->get('access');
    $proxy_user = $request->get('proxy_user');
    if (!isset($access) || !$access) {$access=4;}

    if ($access==4) {
        $team=-1;
    } else {
        $team = $request->get('team');
    }


    $users_with_email = $db->getUser(NULL, $email);

    if (!empty($users_with_email)) {
        echo "That login is already taken.  User not added.";
    }
    else {
        $user_info = array(
          'login' => $email,
          'team' => $team,
          'access' => $access,
          'uuid' => NULL,
          'proxy_user' => $proxy_user
        );
        $db->addUser($user_info);
      }
}
