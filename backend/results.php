<?php
require "config.php";
$result = $conn->query("SELECT name, votes FROM candidates ORDER BY votes DESC");
echo json_encode($result->fetchAll(PDO::FETCH_ASSOC));
?>
