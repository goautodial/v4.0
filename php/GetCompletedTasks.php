<?php
declare(strict_types=1);

require_once(__DIR__ . '/UIHandler.php');
require_once(__DIR__ . '/Session.php');

$user = \creamy\CreamyUser::currentUser();
if ($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT || !$user->userHasBasicPermission()) {
    http_response_code(403);
    exit;
}

$ui = \creamy\UIHandler::getInstance();
echo $ui->getCompletedTasksAsTable($user->getUserId(), $user->getUserRole());
