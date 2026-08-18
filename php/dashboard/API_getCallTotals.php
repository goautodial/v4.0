<?php
/**
 * @file        API_getCallTotals.php
 * @brief       Displays dashboard call totals
 * @copyright   Copyright (c) 2020 GOautodial Inc.
 *
 * @par <b>License</b>:
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Affero General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU Affero General Public License for more details.
 *
 *  You should have received a copy of the GNU Affero General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

require_once(__DIR__ . '/APIHandler.php');

function dashboardCallValue(mixed $response): int|float
{
    if (!is_object($response)) {
        return 0;
    }

    $value = $response->data ?? 0;

    if ($value === null || $value === '') {
        return 0;
    }

    return is_numeric($value) ? $value + 0 : 0;
}

$api = \creamy\APIHandler::getInstance();
$requests = [
    'ringingCalls' => [
        'folder' => 'goDashboard',
        'postfields' => [
            'goAction' => 'goGetRingingCalls',
        ],
    ],
    'incomingQueue' => [
        'folder' => 'goDashboard',
        'postfields' => [
            'goAction' => 'goGetIncomingQueue',
        ],
    ],
    'answeredCalls' => [
        'folder' => 'goDashboard',
        'postfields' => [
            'goAction' => 'goGetTotalAnsweredCalls',
        ],
    ],
    'droppedCalls' => [
        'folder' => 'goDashboard',
        'postfields' => [
            'goAction' => 'goGetTotalDroppedCalls',
        ],
    ],
    'inboundCalls' => [
        'folder' => 'goDashboard',
        'postfields' => [
            'goAction' => 'goGetTotalCalls',
            'type' => 'inbound',
        ],
    ],
    'outboundCalls' => [
        'folder' => 'goDashboard',
        'postfields' => [
            'goAction' => 'goGetTotalCalls',
            'type' => 'outbound',
        ],
    ],
    'liveOutbound' => [
        'folder' => 'goDashboard',
        'postfields' => [
            'goAction' => 'goGetLiveOutbound',
        ],
    ],
];

$responses = $api->API_RequestBatch($requests);
$callValues = [];

foreach (array_keys($requests) as $key) {
    $callValues[$key] = dashboardCallValue($responses[$key] ?? null);
}

$totalCalls = $callValues['inboundCalls'] + $callValues['outboundCalls'];
$callValues['droppedPercentage'] = $totalCalls > 0
    ? ($callValues['droppedCalls'] / $totalCalls) * 100
    : 0;

$callTotals = [
    'ringingCalls' => number_format($callValues['ringingCalls']),
    'incomingQueue' => number_format($callValues['incomingQueue']),
    'answeredCalls' => number_format($callValues['answeredCalls']),
    'droppedCalls' => number_format($callValues['droppedCalls']),
    'inboundCalls' => $callValues['inboundCalls'],
    'outboundCalls' => $callValues['outboundCalls'],
    'liveOutbound' => number_format($callValues['liveOutbound']),
    'droppedPercentage' => $callValues['droppedPercentage'],
];

header('Content-Type: application/json');
echo json_encode($callTotals);
