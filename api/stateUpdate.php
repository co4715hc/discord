<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET')
    require_once 'handlers/getStateUpdate.php';
else
    http_response_code(405);

