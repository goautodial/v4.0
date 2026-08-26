<?php
declare(strict_types=1);

require_once(__DIR__ . '/APIHandler.php');
require_once(__DIR__ . '/Session.php');

$user = \creamy\CreamyUser::currentUser();
if ($user->getUserRole() == CRM_DEFAULTS_USER_ROLE_AGENT) {
    http_response_code(403);
    exit;
}

$groupId = filter_input(INPUT_GET, 'groupid', FILTER_UNSAFE_RAW);
if (!is_string($groupId) || !preg_match('/^[A-Za-z0-9_-]+$/', $groupId)) {
    http_response_code(400);
    exit;
}

$api = \creamy\APIHandler::getInstance();
$agentsRank = $api->API_getAllAgentRank($groupId);
$agentCount = isset($agentsRank->user) && is_countable($agentsRank->user)
    ? count($agentsRank->user)
    : 0;

$escape = static function ($value): string {
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
};

for ($index = 0; $index < $agentCount; $index++) {
    $checkboxField = $agentsRank->checkbox_fields[$index] ?? '';
    $rankField = $agentsRank->rank_fields[$index] ?? '';
    $gradeField = $agentsRank->grade_fields[$index] ?? '';
    $rankValue = $agentsRank->values_rank[$index] ?? '';
    $gradeValue = $agentsRank->values_grade[$index] ?? '';
    $isChecked = !empty($agentsRank->checkbox_ischecked[$index]);

    echo '<tr>';
    echo '<td>' . $escape(($agentsRank->user[$index] ?? '') . ' - ' . ($agentsRank->full_name[$index] ?? '')) . '</td>';
    echo '<td>' . $escape($agentsRank->user_group[$index] ?? '') . '</td>';
    echo '<td><center><label class="c-checkbox" for="' . $escape($checkboxField) . '">';
    echo '<input type="checkbox" id="' . $escape($checkboxField) . '" name="' . $escape($checkboxField) . '" value="YES"' . ($isChecked ? ' checked' : '') . ' />';
    echo '<span class="fa fa-check"></span></label></center></td>';
    echo '<td><select class="form-control" name="' . $escape($rankField) . '">';
    for ($rank = 9; $rank >= -9; $rank--) {
        echo '<option value="' . $rank . '"' . ((string) $rankValue === (string) $rank ? ' selected' : '') . '> ' . $rank . '</option>';
    }
    echo '</select></td>';
    echo '<td><select class="form-control" name="' . $escape($gradeField) . '">';
    for ($grade = 10; $grade >= 0; $grade--) {
        echo '<option value="' . $grade . '"' . ((string) $gradeValue === (string) $grade ? ' selected' : '') . '> ' . $grade . '</option>';
    }
    echo '</select></td>';
    echo '</tr>';
}
