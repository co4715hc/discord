<?php

namespace models;
use mysqli;

class Message
{
    public static function getChannelMessages(int $channelId, int $lastId, mysqli $conn)
    {
        $query = "SELECT 
                      m.message_id, 
                      UNIX_TIMESTAMP(m.message_timestamp) as message_timestamp, 
                      m.message_content, 
                      u.user_id, 
                      u.user_name, 
                      u.user_display_name,
                      u.user_avatar_url
                  FROM message m
                           JOIN channel c ON m.channel_id = c.channel_id
                           JOIN user u ON m.user_id = u.user_id
                  WHERE m.channel_id = ? AND
                        m.message_id > ?
                  ORDER BY m.message_id;";
        $statement = $conn->prepare($query);
        $statement->bind_param("ii", $channelId, $lastId);
        $statement->execute();
        $result = $statement->get_result();

        $messages = [];
        while ($row = $result->fetch_assoc()) {
            $row['message_content'] = htmlentities($row['message_content']);
            $row['user_name'] = htmlentities($row['user_name']);
            $row['user_display_name'] = htmlentities($row['user_display_name']);
//            $row['user_avatar_url'] = htmlentities($row['user_avatar_url']);
            $messages[] = $row;
        }
        return $messages;
    }

    public static function sendMessage(int $userId, string $channelId, string $message, mysqli $conn)
    {
        $query = "INSERT INTO message 
                            (message_timestamp, message_content, user_id, channel_id) 
                  VALUES (CURRENT_TIMESTAMP, ?, ?, ?)";
        $statement = $conn->prepare($query);
        $statement->bind_param("sii", $message, $userId, $channelId);
        return $statement->execute();
    }
}