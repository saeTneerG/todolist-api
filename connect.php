<?php
$db_name = "to_do_list";
$db_user = "root";
$db_pass = "";
$db_host = "localhost";

$con = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($con->connect_error) {
    die("Connection failed: " . $con->connect_error);
}
?>