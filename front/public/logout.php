<?php
session_start();
require '../includes/api_client.php';

api('logout.php', 'POST');
session_destroy();
header("Location: index.php");
exit;
