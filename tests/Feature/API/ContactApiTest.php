<?php

namespace Tests\Feature\API;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful contact form submission.
     */
    public function test_contact_form_submission_success(): void
    {
        Mail::fake(); // Fake the mail sending

        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Information Request',
            'message' => 'I would like more information about your programs.',
            'phone' => '+261 34 12 345 67',
            'organization' => 'University of Madagascar'
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/contact', $contactData);

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.'
            ]);
    }

    /**
     * Test contact form validation errors.
     */
    public function test_contact_form_validation_errors(): void
    {
        $contactData = [
            'name' => '', // Missing required field
            'email' => 'invalid-email', // Invalid email
            'subject' => '',
            'message' => ''
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/contact', $contactData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name', 'email', 'subject', 'message']);
    }

    /**
     * Test contact form with minimal required fields.
     */
    public function test_contact_form_minimal_fields(): void
    {
        Mail::fake(); // Fake the mail sending

        $contactData = [
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'subject' => 'Quick Question',
            'message' => 'Just a quick question about your services.'
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/contact', $contactData);

        $response->assertStatus(200);
    }

    /**
     * Test contact form with long message.
     */
    public function test_contact_form_message_too_long(): void
    {
        $contactData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'subject' => 'Long Message',
            'message' => str_repeat('This is a very long message. ', 150) // Exceeds 2000 chars
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->post('/api/contact', $contactData);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['message']);
    }
}
