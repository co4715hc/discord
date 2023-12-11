<?php

namespace models;

class Server
{
    public static function getServers($lastUpdated, $conn)
    {
        $query = "SELECT server_id, server_name, server_icon_url 
                  FROM server 
                  WHERE EXISTS(SELECT 1 FROM server where last_updated > ?);";
        $statement = $conn->prepare($query);
        $statement->bind_param("s", $lastUpdated);
        $statement->execute();
        $result = $statement->get_result();

        $servers = [];
        while ($row = $result->fetch_assoc()) {
            $row['server_name'] = htmlentities($row['server_name']);
            $servers[] = $row;
        }
        return $servers;
    }

    public static function getCurrentTimestamp($conn)
    {
        $statement = $conn->prepare("SELECT CURRENT_TIMESTAMP");
        $statement->execute();
        $result = $statement->get_result();
        $row = $result->fetch_assoc();
        return $row['CURRENT_TIMESTAMP'];
    }
}