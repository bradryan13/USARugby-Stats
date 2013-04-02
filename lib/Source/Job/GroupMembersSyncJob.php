<?php

namespace Source\Job;

use Kue\Job;
use Source\APSource;
use Source\DataSource;

class GroupMembersSyncJob implements Job
{
    private $sessionattributes;
    private $group_uuid;

    public function __construct($sessionattributes, $group_uuid) {
        $this->sessionattributes = $sessionattributes;
        $this->group_uuid = $group_uuid;
    }

    public function run()
    {
        $db = new DataSource();
        $existing_players = $db->getTeamPlayers($this->group_uuid);
        $attributes = $this->sessionattributes;
        $user = $db->getUser($attributes['user_uuid']);
        $client = APSource::SourceFactory($attributes);

        $added = 0;
        $members = $client->getGroupMembers($this->group_uuid);

        foreach ($members as $member) {
            $member_roles = $client->groupsGetRoles($this->group_uuid, $member->uuid);
            $roles = array();
            foreach ($member_roles as $role) {
                $roles[] = $role->name;
            }
            $now = date('Y-m-d H:i:s');
            if (!empty($member->picture)) {
                $picture_url = substr($member->picture, strpos($member->picture, '/sites/default/'));
            }
            else {
                $picture_url = '/sites/default/files/imagecache/profile_mini/sites/all/themes/allplayers960/images/default_profile.png';
            }
            $player_info = array(
                'user_create' => $user['login'],
                'last_update' => $now,
                'uuid' => $member->uuid,
                'team_uuid' => $this->group_uuid,
                'firstname' => mysql_real_escape_string($member->fname),
                'lastname' => mysql_real_escape_string($member->lname),
                'picture_url' => $picture_url,
                'roles' => serialize($roles)
            );
            if (!$existing_players || !key_exists($member->uuid, $existing_players)) {
                $db->addupdatePlayer($player_info);
                $added++;
            }
            else {
                if (!empty($existing_players[$member->uuid]['id']) && is_numeric($existing_players[$member->uuid]['id'])) {
                    $player_info['id'] = $existing_players[$member->uuid]['id'];
                    $db->addupdatePlayer($player_info);
                }
            }
        }
        return $added;
    }
}

