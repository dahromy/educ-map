<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\ContactFormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Contact",
 *     description="Contact form submission endpoint"
 * )
 */
class ContactController extends Controller
{
    /**
     * Submit a contact form.
     *
     * @param ContactFormRequest $request
     * @return JsonResponse
     *
     * @OA\Post(
     *     path="/api/contact",
     *     summary="Submit a contact form",
     *     tags={"Contact"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "email", "subject", "message"},
     *             @OA\Property(property="name", type="string", maxLength=255, example="John Doe"),
     *             @OA\Property(property="email", type="string", format="email", maxLength=255, example="john@example.com"),
     *             @OA\Property(property="subject", type="string", maxLength=255, example="Information Request"),
     *             @OA\Property(property="message", type="string", maxLength=2000, example="I would like more information about..."),
     *             @OA\Property(property="phone", type="string", maxLength=20, example="+261 34 12 345 67"),
     *             @OA\Property(property="organization", type="string", maxLength=255, example="University of Madagascar")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Contact form submitted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Votre message a été envoyé avec succès.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error sending message",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Une erreur s'est produite lors de l'envoi du message.")
     *         )
     *     )
     * )
     */
    public function submit(ContactFormRequest $request): JsonResponse
    {
        try {
            $data = $request->validated();

            // Log the contact form submission
            Log::info('Contact form submitted', [
                'name' => $data['name'],
                'email' => $data['email'],
                'subject' => $data['subject'],
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            // Send email notification to admin
            $this->sendContactEmail($data);

            return response()->json([
                'message' => 'Votre message a été envoyé avec succès. Nous vous répondrons dans les plus brefs délais.'
            ]);

        } catch (\Exception $e) {
            Log::error('Contact form submission failed', [
                'error' => $e->getMessage(),
                'data' => $request->validated()
            ]);

            return response()->json([
                'message' => 'Une erreur s\'est produite lors de l\'envoi du message. Veuillez réessayer plus tard.'
            ], 500);
        }
    }

    /**
     * Send contact email to admin
     */
    private function sendContactEmail(array $data): void
    {
        // Get admin email from config or use default
        $adminEmail = config('mail.admin_email', 'admin@educ-map.mg');

        Mail::send('emails.contact', $data, function ($message) use ($data, $adminEmail) {
            $message->to($adminEmail)
                ->subject('Nouveau message de contact - Educ-Map: ' . $data['subject'])
                ->replyTo($data['email'], $data['name']);
        });
    }
}
