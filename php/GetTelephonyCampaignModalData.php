<?php

declare(strict_types=1);

require_once(__DIR__ . '/APIHandler.php');

$api = \creamy\APIHandler::getInstance();
$pageData = $api->API_getTelephonyCampaignModalData();

$option = static function (string $value, string $label): array {
    return [
        'value' => $value,
        'label' => $label,
    ];
};

$response = [
    'result' => 'success',
    'ingroups' => [],
    'ivrs' => [],
    'users' => [],
    'voicemails' => [],
    'carriers' => [],
];

$ingroups = $pageData['ingroup'] ?? null;
for ($index = 0; $index < (isset($ingroups->group_id) && is_countable($ingroups->group_id) ? count($ingroups->group_id) : 0); $index++) {
    $groupId = (string) ($ingroups->group_id[$index] ?? '');
    if ($groupId === '') {
        continue;
    }

    $response['ingroups'][] = $option($groupId, (string) ($ingroups->group_name[$index] ?? $groupId));
}

$ivrs = $pageData['ivr'] ?? null;
for ($index = 0; $index < (isset($ivrs->menu_id) && is_countable($ivrs->menu_id) ? count($ivrs->menu_id) : 0); $index++) {
    $menuId = (string) ($ivrs->menu_id[$index] ?? '');
    if ($menuId === '') {
        continue;
    }

    $response['ivrs'][] = $option($menuId, (string) ($ivrs->menu_name[$index] ?? $menuId));
}

$users = $pageData['users'] ?? null;
for ($index = 0; $index < (isset($users->user_id) && is_countable($users->user_id) ? count($users->user_id) : 0); $index++) {
    $userId = (string) ($users->user_id[$index] ?? '');
    if ($userId === '') {
        continue;
    }

    $response['users'][] = $option($userId, (string) ($users->full_name[$index] ?? $userId));
}

$voicemails = $pageData['voicemails'] ?? null;
for ($index = 0; $index < (isset($voicemails->voicemail_id) && is_countable($voicemails->voicemail_id) ? count($voicemails->voicemail_id) : 0); $index++) {
    $voicemailId = (string) ($voicemails->voicemail_id[$index] ?? '');
    if ($voicemailId === '') {
        continue;
    }

    $response['voicemails'][] = $option($voicemailId, (string) ($voicemails->fullname[$index] ?? $voicemailId));
}

$carriers = $pageData['carriers'] ?? null;
for ($index = 0; $index < (isset($carriers->carrier_id) && is_countable($carriers->carrier_id) ? count($carriers->carrier_id) : 0); $index++) {
    if (($carriers->active[$index] ?? '') !== 'Y') {
        continue;
    }

    $carrierId = (string) ($carriers->carrier_id[$index] ?? '');
    if ($carrierId === '') {
        continue;
    }

    $dialplanEntries = explode("\n", (string) ($carriers->dialplan_entry[$index] ?? ''));
    $prefixParts = explode(',', $dialplanEntries[0] ?? '');
    $prefixPattern = ltrim($prefixParts[0] ?? '', 'exten => _ ');
    $dotPosition = strpos($prefixPattern, '.');
    $dialPrefix = $dotPosition === false ? $prefixPattern : substr($prefixPattern, 0, $dotPosition);
    $dialPrefix = str_replace('N', '', str_replace('X', '', $dialPrefix));

    if ($dialPrefix === '') {
        continue;
    }

    $response['carriers'][] = $option($dialPrefix, (string) ($carriers->carrier_name[$index] ?? $carrierId));
}

header('Content-Type: application/json');
echo json_encode($response);
