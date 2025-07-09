<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\GlobalSearchRequest;
use App\Http\Resources\API\EstablishmentResource;
use App\Models\Establishment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class GlobalSearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(GlobalSearchRequest $request)
    {
        $validated = $request->getValidatedWithDefaults();

        $query = Establishment::query();

        // Text search
        if (!empty($validated['q'])) {
            $query->where(function (Builder $q) use ($validated) {
                $search = '%' . $validated['q'] . '%';
                $q->where('name', 'like', $search)
                    ->orWhere('abbreviation', 'like', $search)
                    ->orWhere('description', 'like', $search)
                    ->orWhere('address', 'like', $search)
                    ->orWhere('region', 'like', $search)
                    ->orWhere('city', 'like', $search)
                    ->orWhere('phone', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhere('website', 'like', $search)
                    ->orWhere('logo_url', 'like', $search)
                    ->orWhereRaw('CAST(student_count AS TEXT) LIKE ?', [$search])
                    ->orWhereRaw('CAST(success_rate AS TEXT) LIKE ?', [$search])
                    ->orWhereRaw('CAST(professional_insertion_rate AS TEXT) LIKE ?', [$search])
                    ->orWhereRaw('CAST(first_habilitation_year AS TEXT) LIKE ?', [$search])
                    // Category name
                    ->orWhereHas('category', function ($cat) use ($search) {
                        $cat->where('name', 'like', $search);
                    })
                    // Departments name
                    ->orWhereHas('departments', function ($dept) use ($search) {
                        $dept->where('name', 'like', $search);
                    })
                    // Labels name
                    ->orWhereHas('labels', function ($label) use ($search) {
                        $label->where('name', 'like', $search);
                    })
                    // Program Offerings: domain, grade, mention
                    ->orWhereHas('programOfferings.domain', function ($domain) use ($search) {
                        $domain->where('name', 'like', $search);
                    })
                    ->orWhereHas('programOfferings.grade', function ($grade) use ($search) {
                        $grade->where('name', 'like', $search);
                    })
                    ->orWhereHas('programOfferings.mention', function ($mention) use ($search) {
                        $mention->where('name', 'like', $search);
                    });
            });
        }

        // Direct text filters
        if (!empty($validated['name'])) {
            $query->where('name', 'like', '%' . $validated['name'] . '%');
        }
        if (!empty($validated['abbreviation'])) {
            $query->where('abbreviation', 'like', '%' . $validated['abbreviation'] . '%');
        }
        if (!empty($validated['description'])) {
            $query->where('description', 'like', '%' . $validated['description'] . '%');
        }

        // Location filters
        if (!empty($validated['region'])) {
            $query->where('region', 'like', '%' . $validated['region'] . '%');
        }
        if (!empty($validated['city'])) {
            $query->where('city', 'like', '%' . $validated['city'] . '%');
        }
        if (!empty($validated['address'])) {
            $query->where('address', 'like', '%' . $validated['address'] . '%');
        }

        // Category and classification
        if (!empty($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
        }
        if (!empty($validated['category_name'])) {
            $query->whereHas('category', function (Builder $q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['category_name'] . '%');
            });
        }
        if (!empty($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Academic filters
        if (!empty($validated['domain_id'])) {
            $query->whereHas('domains', function (Builder $q) use ($validated) {
                $q->where('domains.id', $validated['domain_id']);
            });
        }
        if (!empty($validated['domain_name'])) {
            $query->whereHas('domains', function (Builder $q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['domain_name'] . '%');
            });
        }
        if (!empty($validated['grade_id'])) {
            $query->whereHas('grades', function (Builder $q) use ($validated) {
                $q->where('grades.id', $validated['grade_id']);
            });
        }
        if (!empty($validated['grade_name'])) {
            $query->whereHas('grades', function (Builder $q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['grade_name'] . '%');
            });
        }
        if (!empty($validated['mention_id'])) {
            $query->whereHas('mentions', function (Builder $q) use ($validated) {
                $q->where('mentions.id', $validated['mention_id']);
            });
        }
        if (!empty($validated['mention_name'])) {
            $query->whereHas('mentions', function (Builder $q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['mention_name'] . '%');
            });
        }
        if (!empty($validated['label_id'])) {
            $query->whereHas('labels', function (Builder $q) use ($validated) {
                $q->where('labels.id', $validated['label_id']);
            });
        }
        if (!empty($validated['label_name'])) {
            $query->whereHas('labels', function (Builder $q) use ($validated) {
                $q->where('name', 'like', '%' . $validated['label_name'] . '%');
            });
        }

        // Indicators and metrics
        if (isset($validated['student_count_min'])) {
            $query->where('student_count', '>=', $validated['student_count_min']);
        }
        if (isset($validated['student_count_max'])) {
            $query->where('student_count', '<=', $validated['student_count_max']);
        }
        if (isset($validated['success_rate_min'])) {
            $query->where('success_rate', '>=', $validated['success_rate_min']);
        }
        if (isset($validated['success_rate_max'])) {
            $query->where('success_rate', '<=', $validated['success_rate_max']);
        }
        if (isset($validated['professional_insertion_rate_min'])) {
            $query->where('professional_insertion_rate', '>=', $validated['professional_insertion_rate_min']);
        }
        if (isset($validated['professional_insertion_rate_max'])) {
            $query->where('professional_insertion_rate', '<=', $validated['professional_insertion_rate_max']);
        }
        if (isset($validated['first_habilitation_year_min'])) {
            $query->where('first_habilitation_year', '>=', $validated['first_habilitation_year_min']);
        }
        if (isset($validated['first_habilitation_year_max'])) {
            $query->where('first_habilitation_year', '<=', $validated['first_habilitation_year_max']);
        }

        // Accreditation filters
        if (!empty($validated['has_recent_accreditation'])) {
            $query->whereHas('accreditations', function (Builder $q) {
                $q->where('start_date', '>=', now()->subYear());
            });
        }
        if (!empty($validated['accreditation_date_from'])) {
            $query->whereHas('accreditations', function (Builder $q) use ($validated) {
                $q->where('start_date', '>=', $validated['accreditation_date_from']);
            });
        }
        if (!empty($validated['accreditation_date_to'])) {
            $query->whereHas('accreditations', function (Builder $q) use ($validated) {
                $q->where('end_date', '<=', $validated['accreditation_date_to']);
            });
        }
        if (!empty($validated['reference_type'])) {
            $query->whereHas('references', function (Builder $q) use ($validated) {
                $q->where('type', 'like', '%' . $validated['reference_type'] . '%');
            });
        }

        // Program offerings
        if (!empty($validated['has_programs'])) {
            $query->has('programOfferings');
        }
        if (isset($validated['program_count_min'])) {
            $query->has('programOfferings', '>=', $validated['program_count_min']);
        }
        if (isset($validated['program_count_max'])) {
            $query->has('programOfferings', '<=', $validated['program_count_max']);
        }
        if (!empty($validated['tuition_fees'])) {
            $query->whereHas('programOfferings', function (Builder $q) use ($validated) {
                $q->where('tuition_fees', 'like', '%' . $validated['tuition_fees'] . '%');
            });
        }
        if (!empty($validated['program_duration'])) {
            $query->whereHas('programOfferings', function (Builder $q) use ($validated) {
                $q->where('duration', 'like', '%' . $validated['program_duration'] . '%');
            });
        }

        // Partnerships and features
        if (isset($validated['has_international_partnerships'])) {
            $query->where('has_international_partnerships', $validated['has_international_partnerships']);
        }
        if (!empty($validated['international_partnerships'])) {
            $query->where('international_partnerships', 'like', '%' . $validated['international_partnerships'] . '%');
        }

        // Geographic filters
        if (!empty($validated['has_coordinates'])) {
            $query->whereNotNull('latitude')->whereNotNull('longitude');
        }
        if (isset($validated['latitude_min'])) {
            $query->where('latitude', '>=', $validated['latitude_min']);
        }
        if (isset($validated['latitude_max'])) {
            $query->where('latitude', '<=', $validated['latitude_max']);
        }
        if (isset($validated['longitude_min'])) {
            $query->where('longitude', '>=', $validated['longitude_min']);
        }
        if (isset($validated['longitude_max'])) {
            $query->where('longitude', '<=', $validated['longitude_max']);
        }
        if (!empty($validated['radius_km']) && !empty($validated['center_lat']) && !empty($validated['center_lng'])) {
            $query->whereRaw(
                'ST_Distance_Sphere(point(longitude, latitude), point(?, ?)) <= ?',
                [$validated['center_lng'], $validated['center_lat'], $validated['radius_km'] * 1000]
            );
        }

        // Contact information
        if (!empty($validated['has_email'])) {
            $query->whereNotNull('email');
        }
        if (!empty($validated['has_phone'])) {
            $query->whereNotNull('phone_number');
        }
        if (!empty($validated['has_website'])) {
            $query->whereNotNull('website');
        }
        if (!empty($validated['email_domain'])) {
            $query->where('email', 'like', '%' . $validated['email_domain']);
        }

        // Include relationships
        if (!empty($validated['include'])) {
            $includes = explode(',', $validated['include']);
            $query->with(array_intersect($includes, [
                'category',
                'domains',
                'grades',
                'mentions',
                'labels',
                'accreditations',
                'references',
                'programOfferings',
                'user'
            ]));
        }


        // Sorting
        $query->orderBy($validated['sort_by'], $validated['sort_order']);

        // Pagination
        $establishments = $query->paginate($validated['per_page']);

        return EstablishmentResource::collection($establishments);
    }
}
