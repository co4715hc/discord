<?php

namespace models;

class Channel
{
    public static function getChannels(int $serverId, $lastUpdated, $conn)
    {
        $query = "SELECT channel_id, ca.channel_category_id, channel_name, channel_category_name, channel_topic
                  FROM channel ch
                      JOIN channel_category ca on ch.channel_category_id = ca.channel_category_id
                  WHERE SERVER_ID = ? AND  EXISTS(SELECT 1 FROM channel where last_updated > ?)
                  ORDER BY channel_category_id, channel_id;";
        $statement = $conn->prepare($query);
        $statement->bind_param("is", $serverId, $lastUpdated);
        $statement->execute();
        $result = $statement->get_result();

        $channels = [];
        while ($row = $result->fetch_assoc()) {
            $row['channel_name'] = htmlentities($row['channel_name']);
            $channels[] = $row;
        }
        return $channels;
    }

    public static function getFirstChannelId($serverId, $conn)
    {
        $query = "SELECT channel_id 
                FROM channel join channel_category on channel.channel_category_id = channel_category.channel_category_id
                WHERE server_id = ? ORDER BY channel_id LIMIT 1;";
        $statement = $conn->prepare($query);
        $statement->bind_param("i", $serverId);
        $statement->execute();
        $result = $statement->get_result();
        $row = $result->fetch_assoc();
        return $row['channel_id'];
    }
}