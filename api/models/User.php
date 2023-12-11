<?php

namespace models;

use mysqli;

class User
{
    public static function getUserId(string $username, mysqli $conn)
    {
        $statement = $conn->prepare("SELECT user_id FROM user WHERE user_name = ?");
        $statement->bind_param("s", $username);
        $statement->execute();
        $result = $statement->get_result();
        $row = $result->fetch_assoc();
        return $row['user_id'];
    }

    public static function updateLastOnline($userId, $conn): bool
    {
        $query = "UPDATE user 
                  SET user_last_online = CURRENT_TIMESTAMP
                  WHERE user_id = ?";
        $statement = $conn->prepare($query);
        $statement->bind_param("i", $userId);
        return $statement->execute();
    }

    public static function getUsers($lastUpdated, $conn): array
    {
        $query = "SELECT user_id,
                         user_display_name, 
                         user_name, 
                         user_avatar_url, 
                         user_last_online, 
                         user_status,
                         UNIX_TIMESTAMP(user_created_date) as user_created_date,  
                         (user_last_online > NOW() - INTERVAL 10 SECOND) AS is_online
                  FROM user 
                  WHERE last_updated > ?
                  OR (user_last_online > NOW() - INTERVAL 30 SECOND);";
        $statement = $conn->prepare($query);
        $statement->bind_param("s", $lastUpdated);
        $statement->execute();
        $result = $statement->get_result();
        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        return $users;
    }

    public static function getUser($userId, $conn): array
    {
        $query = "SELECT user_id,
                         user_display_name, 
                         user_name, 
                         user_avatar_url, 
                         user_last_online, 
                         user_status,
                         user_created_date,
                         (user_last_online > NOW() - INTERVAL 10 SECOND) AS is_online
                  FROM user 
                  WHERE user_id = ?";
        $statement = $conn->prepare($query);
        $statement->bind_param("i", $userId);
        $statement->execute();
        $result = $statement->get_result();
        $row = $result->fetch_assoc();
        return $row;
    }

    public static function createUser($username, $displayName, $conn): bool
    {
        $query = "INSERT INTO user (user_name, user_display_name, user_avatar_url) VALUES (?, ?, 'images/avatar.png')";
        try {
            $statement = $conn->prepare($query);
            $statement->bind_param("ss", $username, $displayName);
            return $statement->execute();
        } catch (\Exception $e) {
            return false;
        }
    }
}