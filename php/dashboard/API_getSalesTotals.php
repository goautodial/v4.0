<?php
/**
 * @file        API_getSalesTotals.php
 * @brief       Displays dashboard sales totals
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

$api = \creamy\APIHandler::getInstance();
$salesTypes = [
    'totalSales' => 'all-daily',
    'outSales' => 'out-daily',
    'inSales' => 'in-daily',
    'inSalesHour' => 'in-hourly',
    'outSalesHour' => 'out-hourly',
];
$requests = [];

foreach ($salesTypes as $key => $type) {
    $requests[$key] = [
        'folder' => 'goDashboard',
        'postfields' => [
            'goAction' => 'goGetTotalSales',
            'type' => $type,
        ],
    ];
}

$responses = $api->API_RequestBatch($requests);
$salesTotals = [];

foreach (array_keys($salesTypes) as $key) {
    $sales = is_object($responses[$key] ?? null) ? ($responses[$key]->data ?? 0) : 0;
    $salesTotals[$key] = empty($sales) ? 0 : $sales;
}

header('Content-Type: application/json');
echo json_encode($salesTotals);
