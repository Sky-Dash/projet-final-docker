<?php
require_once '../includes/helpers.php';
session_start();
session_destroy();
json_response(['success' => true]);
