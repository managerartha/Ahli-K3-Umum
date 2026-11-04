<?php
require_once dirname(__DIR__) . '/includes/auth.php';

start_session();
$_SESSION = [];
session_regenerate_id(true);
session_destroy();
header('Location: login.php');
exit;
