<?php

namespace App\OpenApi\Schemas;

use OpenApi\Annotations as OA;

/**
 * @OA\Schema(
 *     schema="AdminStatsOverview",
 *     type="object",
 *     title="Admin Statistics Overview",
 *     description="General statistics overview for administrators",
 *     @OA\Property(
 *         property="total_establishments",
 *         type="integer",
 *         description="Total number of establishments",
 *         example=150
 *     ),
 *     @OA\Property(
 *         property="total_users",
 *         type="integer",
 *         description="Total number of users",
 *         example=25
 *     ),
 *     @OA\Property(
 *         property="total_references",
 *         type="integer",
 *         description="Total number of references",
 *         example=200
 *     ),
 *     @OA\Property(
 *         property="total_accreditations",
 *         type="integer",
 *         description="Total number of accreditations",
 *         example=300
 *     ),
 *     @OA\Property(
 *         property="recent_establishments",
 *         type="integer",
 *         description="Number of establishments added in the last 30 days",
 *         example=5
 *     ),
 *     @OA\Property(
 *         property="recent_accreditations",
 *         type="integer",
 *         description="Number of recent accreditations",
 *         example=12
 *     )
 * )
 */
class AdminStatsOverviewSchema
{
}
