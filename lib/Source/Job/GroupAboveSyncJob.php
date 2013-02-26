<?php

namespace Source\Job;

use Kue\Job;
use Source\APSource;
use Source\DataSource;

class GroupAboveSyncJob implements Job
{
    private $uuid;
    private $sessionattributes;

    public function __construct($uuid, $sessionattributes) {
        $this->uuid = $uuid;
        $this->sessionattributes = $sessionattributes;
    }

    public function run()
    {
        $db = new DataSource();
        $attributes = $this->sessionattributes;
        $client = APSource::SourceFactory($attributes);

        $group = $client->groupsGetGroup($this->uuid);
        if (!empty($group->groups_above_uuid)) {
            // Note this currently only related the first group above; this
            //  should be more robust and match on all or some type subset.
            $group_above = array_pop($group->groups_above_uuid);
            $db->updateTeamAbove($this->uuid, $group_above);
            $groupmembersync = new GroupMembersSyncJob($attributes, $group_above);
            $groupmembersync->run();
        }
        $groupmembersync = new GroupMembersSyncJob($attributes, $this->uuid);
        $groupmembersync->run();
    }
}
