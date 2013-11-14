<?php
session_start();
require_once("framework.php");
$system = new System();
$system->registerConfirm = true;
$session = $system->session;
?>

