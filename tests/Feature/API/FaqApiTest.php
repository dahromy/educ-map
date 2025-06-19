<?php

namespace Tests\Feature\API;

use App\Models\FaqItem;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FaqApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the FAQ index endpoint returns public FAQ items.
     */
    public function test_faq_index_endpoint(): void
    {
        // Create some FAQ items
        FaqItem::factory()->count(3)->active()->create();
        FaqItem::factory()->count(2)->inactive()->create();

        $response = $this->get('/api/faq');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data') // Should only return active items
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'question',
                        'answer',
                        'category',
                        'sort_order',
                        'is_active',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ]);
    }

    /**
     * Test the FAQ index endpoint with category filter.
     */
    public function test_faq_index_with_category_filter(): void
    {
        FaqItem::factory()->create(['category' => 'Général', 'is_active' => true]);
        FaqItem::factory()->create(['category' => 'Inscription', 'is_active' => true]);
        FaqItem::factory()->create(['category' => 'Général', 'is_active' => true]);

        $response = $this->get('/api/faq?category=Général');

        $response->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }

    /**
     * Test the FAQ show endpoint.
     */
    public function test_faq_show_endpoint(): void
    {
        $faqItem = FaqItem::factory()->create();

        $response = $this->get("/api/faq/{$faqItem->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'question',
                    'answer',
                    'category',
                    'sort_order',
                    'is_active',
                    'created_at',
                    'updated_at'
                ]
            ]);
    }

    /**
     * Test creating FAQ item as admin.
     */
    public function test_admin_can_create_faq_item(): void
    {
        $admin = User::factory()->create(['roles' => 'ROLE_ADMIN']);

        $faqData = [
            'question' => 'How to register?',
            'answer' => 'You can register by following these steps...',
            'category' => 'Inscription',
            'sort_order' => 10,
            'is_active' => true
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->post('/api/admin/faq', $faqData);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'question',
                    'answer',
                    'category'
                ]
            ]);

        $this->assertDatabaseHas('faq_items', [
            'question' => 'How to register?',
            'category' => 'Inscription'
        ]);
    }

    /**
     * Test non-admin cannot create FAQ item.
     */
    public function test_non_admin_cannot_create_faq_item(): void
    {
        $user = User::factory()->create(['roles' => 'ROLE_USER']);

        $faqData = [
            'question' => 'How to register?',
            'answer' => 'You can register by following these steps...'
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->post('/api/admin/faq', $faqData);

        $response->assertStatus(403);
    }

    /**
     * Test updating FAQ item as admin.
     */
    public function test_admin_can_update_faq_item(): void
    {
        $admin = User::factory()->create(['roles' => 'ROLE_ADMIN']);
        $faqItem = FaqItem::factory()->create();

        $updateData = [
            'question' => 'Updated question?',
            'answer' => 'Updated answer.'
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->put("/api/admin/faq/{$faqItem->id}", $updateData);

        $response->assertStatus(200);

        $this->assertDatabaseHas('faq_items', [
            'id' => $faqItem->id,
            'question' => 'Updated question?',
            'answer' => 'Updated answer.'
        ]);
    }

    /**
     * Test deleting FAQ item as admin.
     */
    public function test_admin_can_delete_faq_item(): void
    {
        $admin = User::factory()->create(['roles' => 'ROLE_ADMIN']);
        $faqItem = FaqItem::factory()->create();

        $response = $this->actingAs($admin, 'sanctum')
            ->delete("/api/admin/faq/{$faqItem->id}");

        $response->assertStatus(204);

        $this->assertModelMissing($faqItem);
    }
}
