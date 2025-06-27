<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Establishment;
use App\Models\Reference;
use App\Models\Accreditation;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * @OA\Tag(
 *     name="Admin Statistics",
 *     description="Statistics endpoints for administrators"
 * )
 */
class AdminStatsController extends Controller
{
    /**
     * Get general statistics overview.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/admin/stats/overview",
     *     summary="Get general statistics overview",
     *     tags={"Admin Statistics"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="total_establishments", type="integer"),
     *             @OA\Property(property="total_users", type="integer"),
     *             @OA\Property(property="total_references", type="integer"),
     *             @OA\Property(property="total_accreditations", type="integer"),
     *             @OA\Property(property="recent_establishments", type="integer", description="Added in last 30 days"),
     *             @OA\Property(property="recent_accreditations", type="integer", description="Recent accreditations")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    public function overview(): JsonResponse
    {
        $stats = [
            'total_establishments' => Establishment::count(),
            'total_users' => User::count(),
            'total_references' => Reference::count(),
            'total_accreditations' => Accreditation::count(),
            'recent_establishments' => Establishment::where('created_at', '>=', now()->subDays(30))->count(),
            'recent_accreditations' => Accreditation::where('is_recent', true)->count(),
        ];

        return response()->json($stats);
    }

    /**
     * Get establishments count by category.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/admin/stats/establishments-by-category",
     *     summary="Get establishments count by category",
     *     tags={"Admin Statistics"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="category_name", type="string"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="establishments_count", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    public function establishmentsByCategory(): JsonResponse
    {
        $stats = Category::withCount('establishments')
            ->orderBy('establishments_count', 'desc')
            ->get()
            ->map(function ($category) {
                return [
                    'category_id' => $category->id,
                    'category_name' => $category->category_name,
                    'establishments_count' => $category->establishments_count,
                ];
            });

        return response()->json($stats);
    }

    /**
     * Get geographical distribution of establishments.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/admin/stats/geographical-distribution",
     *     summary="Get geographical distribution of establishments",
     *     tags={"Admin Statistics"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="region", type="string"),
     *                 @OA\Property(property="establishments_count", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    public function geographicalDistribution(): JsonResponse
    {
        $stats = Establishment::select('region', DB::raw('COUNT(*) as establishments_count'))
            ->whereNotNull('region')
            ->groupBy('region')
            ->orderBy('establishments_count', 'desc')
            ->get();

        return response()->json($stats);
    }

    /**
     * Get habilitations count by year.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/admin/stats/habilitations-by-year",
     *     summary="Get habilitations count by year",
     *     tags={"Admin Statistics"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(
     *                 @OA\Property(property="year", type="integer"),
     *                 @OA\Property(property="habilitations_count", type="integer")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    public function habilitationsByYear(): JsonResponse
    {
        $stats = Reference::select(DB::raw('strftime("%Y", main_date) as year'), DB::raw('COUNT(*) as habilitations_count'))
            ->whereNotNull('main_date')
            ->groupBy(DB::raw('strftime("%Y", main_date)'))
            ->orderBy('year', 'desc')
            ->get();

        return response()->json($stats);
    }

    /**
     * Get user statistics by role.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/admin/stats/users-by-role",
     *     summary="Get user statistics by role",
     *     description="Returns count of users for each role type. Available roles: ROLE_USER (regular users), ROLE_ADMIN (administrators), ROLE_ESTABLISHMENT (institution users)",
     *     tags={"Admin Statistics"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             description="Array of role statistics",
     *             @OA\Items(
     *                 @OA\Property(
     *                     property="role",
     *                     ref="#/components/schemas/UserRole",
     *                     description="User role type"
     *                 ),
     *                 @OA\Property(
     *                     property="users_count",
     *                     type="integer",
     *                     description="Number of users with this role",
     *                     example=15
     *                 )
     *             ),
     *             example={
     *                 {"role": "ROLE_USER", "users_count": 150},
     *                 {"role": "ROLE_ADMIN", "users_count": 5},
     *                 {"role": "ROLE_ESTABLISHMENT", "users_count": 25}
     *             }
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied - Admin role required"
     *     )
     * )
     */
    public function usersByRole(): JsonResponse
    {
        $stats = User::select('roles', DB::raw('COUNT(*) as users_count'))
            ->whereNotNull('roles')
            ->groupBy('roles')
            ->orderBy('users_count', 'desc')
            ->get()
            ->map(function ($item) {
                return [
                    'role' => $item->roles,
                    'users_count' => $item->users_count,
                ];
            });

        return response()->json($stats);
    }

    /**
     * Get recent activity statistics.
     *
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/admin/stats/recent-activity",
     *     summary="Get recent activity statistics",
     *     tags={"Admin Statistics"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             @OA\Property(property="new_establishments_last_7_days", type="integer"),
     *             @OA\Property(property="new_establishments_last_30_days", type="integer"),
     *             @OA\Property(property="new_users_last_7_days", type="integer"),
     *             @OA\Property(property="new_users_last_30_days", type="integer"),
     *             @OA\Property(property="updated_establishments_last_7_days", type="integer"),
     *             @OA\Property(property="updated_establishments_last_30_days", type="integer")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    public function recentActivity(): JsonResponse
    {
        $stats = [
            'new_establishments_last_7_days' => Establishment::where('created_at', '>=', now()->subDays(7))->count(),
            'new_establishments_last_30_days' => Establishment::where('created_at', '>=', now()->subDays(30))->count(),
            'new_users_last_7_days' => User::where('created_at', '>=', now()->subDays(7))->count(),
            'new_users_last_30_days' => User::where('created_at', '>=', now()->subDays(30))->count(),
            'updated_establishments_last_7_days' => Establishment::where('updated_at', '>=', now()->subDays(7))
                ->where('updated_at', '!=', DB::raw('created_at'))
                ->count(),
            'updated_establishments_last_30_days' => Establishment::where('updated_at', '>=', now()->subDays(30))
                ->where('updated_at', '!=', DB::raw('created_at'))
                ->count(),
        ];

        return response()->json($stats);
    }
}
