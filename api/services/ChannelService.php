<?php

namespace services;

use models\Channel;

require_once 'models/Channel.php';

class ChannelService
{
    public static function getChannels(int $serverId, $lastUpdated, $conn): array
    {
        return Channel::getChannels($serverId, $lastUpdated, $conn);
    }

    public static function getFirstChannelId(mixed $serverId, $conn)
    {
        return Channel::getFirstChannelId($serverId, $conn);
    }
}