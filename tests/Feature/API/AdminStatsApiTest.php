<?php

namespace Tests\Feature\API;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminStatsApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test admin stats overview endpoint.
     */
    public function test_admin_stats_overview(): void
    {
        $admin = User::factory()->create(['roles' => 'ROLE_ADMIN']);

        $response = $this->actingAs($admin, 'sanctum')
            ->get('/api/admin/stats/overview');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'total_establishments',
                'total_users',
                'total_references',
                'total_accreditations',
                'recent_establishments',
                'recent_accreditations'
            ]);
    }

    /**
     * Test non-admin cannot access stats.
     */
    public function test_non_admin_cannot_access_stats(): void
    {
        $user = User::factory()->create(['roles' => 'ROLE_USER']);

        $response = $this->actingAs($user, 'sanctum')
            ->get('/api/admin/stats/overview');

        $response->assertStatus(403);
    }

    /**
     * Test establishments by category stats.
     */
    public function test_establishments_by_category_stats(): void
    {
        $admin = User::factory()->create(['roles' => 'ROLE_ADMIN']);

        $response = $this->actingAs($admin, 'sanctum')
            ->get('/api/admin/stats/establishments-by-category');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'category_id',
                    'category_name',
                    'establishments_count'
                ]
            ]);
    }

    /**
     * Test geographical distribution stats.
     */
    public function test_geographical_distribution_stats(): void
    {
        $admin = User::factory()->create(['roles' => 'ROLE_ADMIN']);

        $response = $this->actingAs($admin, 'sanctum')
            ->get('/api/admin/stats/geographical-distribution');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'region',
                    'establishments_count'
                ]
            ]);
    }

    /**
     * Test habilitations by year stats.
     */
    public function test_habilitations_by_year_stats(): void
    {
        $admin = User::factory()->create(['roles' => 'ROLE_ADMIN']);

        $response = $this->actingAs($admin, 'sanctum')
            ->get('/api/admin/stats/habilitations-by-year');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'year',
                    'habilitations_count'
                ]
            ]);
    }

    /**
     * Test users by role stats.
     */
    public function test_users_by_role_stats(): void
    {
        $admin = User::factory()->create(['roles' => 'ROLE_ADMIN']);

        $response = $this->actingAs($admin, 'sanctum')
            ->get('/api/admin/stats/users-by-role');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => [
                    'role',
                    'users_count'
                ]
            ]);
    }

    /**
     * Test recent activity stats.
     */
    public function test_recent_activity_stats(): void
    {
        $admin = User::factory()->create(['roles' => 'ROLE_ADMIN']);

        $response = $this->actingAs($admin, 'sanctum')
            ->get('/api/admin/stats/recent-activity');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'new_establishments_last_7_days',
                'new_establishments_last_30_days',
                'new_users_last_7_days',
                'new_users_last_30_days',
                'updated_establishments_last_7_days',
                'updated_establishments_last_30_days'
            ]);
    }
}
