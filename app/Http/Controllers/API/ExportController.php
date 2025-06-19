<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\IndexEstablishmentRequest;
use App\Models\Establishment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Tag(
 *     name="Export",
 *     description="Export endpoints for data download"
 * )
 */
class ExportController extends Controller
{
    /**
     * Export establishments data as CSV.
     *
     * @param IndexEstablishmentRequest $request
     * @return Response
     *
     * @OA\Get(
     *     path="/api/admin/export/establishments.csv",
     *     summary="Export establishments data as CSV",
     *     tags={"Export"},
     *     security={{"sanctum": {}}},
     *     @OA\Parameter(
     *         name="region",
     *         in="query",
     *         description="Filter by region name (exact match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="Filter by category name (exact match)",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="CSV file download",
     *         @OA\MediaType(
     *             mediaType="text/csv",
     *             @OA\Schema(type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    public function establishmentsCsv(IndexEstablishmentRequest $request): Response
    {
        try {
            // Use the same filtering logic as the regular index method
            $query = $this->buildEstablishmentQuery($request);

            // Remove pagination for export
            $establishments = $query->get();

            // Generate CSV content
            $csvContent = $this->generateEstablishmentsCsv($establishments);

            // Create response with appropriate headers
            $response = response($csvContent, 200, [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => 'attachment; filename="establishments_' . date('Y-m-d_H-i-s') . '.csv"',
                'Cache-Control' => 'no-cache, no-store, must-revalidate',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);

            // Add BOM for proper UTF-8 handling in Excel
            $response->setContent("\xEF\xBB\xBF" . $csvContent);

            return $response;

        } catch (\Exception $e) {
            Log::error('Failed to export establishments CSV', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'message' => 'Erreur lors de l\'export des données.'
            ], 500);
        }
    }

    /**
     * Export establishments data as JSON.
     *
     * @param IndexEstablishmentRequest $request
     * @return JsonResponse
     *
     * @OA\Get(
     *     path="/api/admin/export/establishments.json",
     *     summary="Export establishments data as JSON",
     *     tags={"Export"},
     *     security={{"sanctum": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="JSON file download",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Access denied"
     *     )
     * )
     */
    public function establishmentsJson(IndexEstablishmentRequest $request): JsonResponse
    {
        try {
            // Use the same filtering logic as the regular index method
            $query = $this->buildEstablishmentQuery($request);

            // Remove pagination for export and load all relationships
            $establishments = $query->with([
                'category',
                'departments',
                'programOfferings.domain',
                'programOfferings.grade',
                'programOfferings.mention',
                'programOfferings.accreditations.reference',
                'labels'
            ])->get();

            $exportData = [
                'export_date' => now()->toISOString(),
                'total_count' => $establishments->count(),
                'filters_applied' => $request->all(),
                'data' => $establishments->toArray()
            ];

            return response()->json($exportData)
                ->header('Content-Disposition', 'attachment; filename="establishments_' . date('Y-m-d_H-i-s') . '.json"');

        } catch (\Exception $e) {
            Log::error('Failed to export establishments JSON', [
                'error' => $e->getMessage(),
                'filters' => $request->all()
            ]);

            return response()->json([
                'message' => 'Erreur lors de l\'export des données.'
            ], 500);
        }
    }

    /**
     * Build the establishment query with filters (reused from EstablishmentController).
     */
    private function buildEstablishmentQuery(IndexEstablishmentRequest $request)
    {
        $query = Establishment::with(['category', 'labels']);

        // Apply filters from the original controller logic
        if ($request->filled('region')) {
            $query->where('region', $request->region);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        if ($request->filled('abbreviation')) {
            $query->where('abbreviation', 'like', '%' . $request->abbreviation . '%');
        }

        if ($request->filled('city')) {
            $query->where('city', 'like', '%' . $request->city . '%');
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('category_name', $request->category);
            });
        }

        if ($request->filled('domain')) {
            $query->whereHas('programOfferings.domain', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->domain . '%');
            });
        }

        if ($request->filled('label')) {
            $query->whereHas('labels', function ($q) use ($request) {
                $q->where('name', $request->label);
            });
        }

        if ($request->filled('reference_start_date') || $request->filled('reference_end_date')) {
            $query->whereHas('programOfferings.accreditations.reference', function ($q) use ($request) {
                if ($request->filled('reference_start_date')) {
                    $q->where('main_date', '>=', $request->reference_start_date);
                }
                if ($request->filled('reference_end_date')) {
                    $q->where('main_date', '<=', $request->reference_end_date);
                }
            });
        }

        if ($request->boolean('has_recent_accreditation')) {
            $query->whereHas('programOfferings.accreditations', function ($q) {
                $q->where('is_recent', true);
            });
        }

        return $query;
    }

    /**
     * Generate CSV content for establishments.
     */
    private function generateEstablishmentsCsv($establishments): string
    {
        $csvData = [];

        // Header row
        $csvData[] = [
            'ID',
            'Nom',
            'Abréviation',
            'Description',
            'Catégorie',
            'Adresse',
            'Région',
            'Ville',
            'Latitude',
            'Longitude',
            'Téléphone',
            'Email',
            'Site Web',
            'Nombre d\'étudiants',
            'Taux de réussite (%)',
            'Taux d\'insertion professionnelle (%)',
            'Première année d\'habilitation',
            'Labels',
            'Date de création',
            'Dernière modification'
        ];

        // Data rows
        foreach ($establishments as $establishment) {
            $labels = $establishment->labels->pluck('name')->implode(', ');

            $csvData[] = [
                $establishment->id,
                $establishment->name,
                $establishment->abbreviation,
                $establishment->description,
                $establishment->category?->category_name,
                $establishment->address,
                $establishment->region,
                $establishment->city,
                $establishment->latitude,
                $establishment->longitude,
                $establishment->phone,
                $establishment->email,
                $establishment->website,
                $establishment->student_count,
                $establishment->success_rate,
                $establishment->professional_insertion_rate,
                $establishment->first_habilitation_year,
                $labels,
                $establishment->created_at?->format('Y-m-d H:i:s'),
                $establishment->updated_at?->format('Y-m-d H:i:s')
            ];
        }

        // Convert to CSV string
        $output = fopen('php://temp', 'r+');
        foreach ($csvData as $row) {
            fputcsv($output, $row, ';'); // Use semicolon separator for better Excel compatibility
        }
        rewind($output);
        $csvContent = stream_get_contents($output);
        fclose($output);

        return $csvContent;
    }
}
