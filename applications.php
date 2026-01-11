<?php
require_once 'includes/functions.php';
requireLogin();

$applications = getUserApplications($_SESSION['user_id']);

$content = 'applications-content.php';
$title = 'My Applications';
$active_applications = 'active'; // For nav highlighting

require_once("theme/templates.php");
?> 