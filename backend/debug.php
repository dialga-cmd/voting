<?php
require_once "config.php";

echo "<pre>";
echo "Users:\n";
foreach ($conn->query("SELECT id, username FROM users") as $row) {
    print_r($row);
}

echo "\nParticipants:\n";
foreach ($conn->query("SELECT id, name, poll_id FROM participants") as $row) {
    print_r($row);
}
echo "</pre>";