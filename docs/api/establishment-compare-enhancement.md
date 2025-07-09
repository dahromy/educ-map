# Enhanced Establishment Compare API

## Overview

The establishment compare API has been enhanced to provide comprehensive comparison data between educational establishments. The API now returns detailed information about each establishment including location, contact details, academic offerings, and more.

## Endpoint

```
GET /api/establishments/compare
```

## Parameters

- `ids[]` (required): Array of establishment IDs to compare (minimum 2, maximum 5)

## Enhanced Response Structure

The API now returns the following detailed information for each establishment:

### Basic Information
- `id`: Establishment ID
- `name`: Full establishment name
- `abbreviation`: Institution abbreviation
- `description`: Detailed description
- `logo_url`: URL to the institution logo
- `category`: Institution category (id, name)

### Location Details
- `address`: Physical address
- `region`: Administrative region
- `city`: City location
- `latitude`: GPS latitude coordinate
- `longitude`: GPS longitude coordinate

### Contact Information
- `phone`: Contact phone number
- `email`: Official email address
- `website`: Institution website URL

### Key Performance Indicators
- `student_count`: Total number of students
- `success_rate`: Academic success rate (percentage)
- `professional_insertion_rate`: Graduate employment rate (percentage)
- `first_habilitation_year`: Year of first accreditation
- `status`: Current operational status
- `international_partnerships`: Description of international collaborations

### Academic Offerings
- `total_programs`: Total number of programs offered
- `departments_count`: Number of departments
- `domains_offered`: Array of study domains/fields
- `grades_offered`: Array of academic levels offered
- `tuition_fees`: Array of tuition fee information
- `program_durations`: Array of program duration information

### Labels and Certifications
- `labels`: Array of institutional labels/certifications with:
  - `id`: Label ID
  - `name`: Label name
  - `color`: Label color code
  - `description`: Label description

### Recent Accreditation Status
- `has_recent`: Boolean indicating recent accreditation
- `accreditation_date`: Date of most recent accreditation
- `reference_type`: Type of accreditation reference

### Timestamps
- `created_at`: Record creation timestamp
- `updated_at`: Last update timestamp

## Example Usage

```bash
# Compare two establishments
curl -X GET "https://api.example.com/api/establishments/compare?ids[]=1&ids[]=2"

# Compare multiple establishments
curl -X GET "https://api.example.com/api/establishments/compare?ids[]=1&ids[]=2&ids[]=3"
```

## Example Response

```json
{
  "data": [
    {
      "id": 1,
      "name": "École Supérieure des Sciences Agronomiques",
      "abbreviation": "ESSA",
      "description": "Leading agricultural sciences institution...",
      "logo_url": "https://example.com/logos/essa.png",
      "category": {
        "id": 1,
        "name": "Public University"
      },
      "location": {
        "address": "Campus d'Ankatso, BP 3044",
        "region": "Analamanga",
        "city": "Antananarivo",
        "latitude": -18.916779,
        "longitude": 47.520526
      },
      "contact": {
        "phone": "+261 20 22 123 45",
        "email": "info@essa.mg",
        "website": "https://essa.mg"
      },
      "indicators": {
        "student_count": 2500,
        "success_rate": 85.5,
        "professional_insertion_rate": 78.2,
        "first_habilitation_year": 2010,
        "status": "Active",
        "international_partnerships": "Partnership with French agricultural schools"
      },
      "academic_offerings": {
        "total_programs": 15,
        "departments_count": 5,
        "domains_offered": ["Agriculture", "Food Science", "Environmental Science"],
        "grades_offered": ["License", "Master", "PhD"],
        "tuition_fees": ["500,000 Ar/year", "750,000 Ar/year"],
        "program_durations": ["3 years", "2 years", "4 years"]
      },
      "labels": [
        {
          "id": 1,
          "name": "Excellence",
          "color": "#FFD700",
          "description": "Institution of excellence"
        }
      ],
      "recent_accreditation": {
        "has_recent": true,
        "accreditation_date": "2024-01-15",
        "reference_type": "Degree Authorization"
      },
      "created_at": "2024-01-01T00:00:00Z",
      "updated_at": "2024-07-09T15:30:00Z"
    }
  ]
}
```

## Validation Rules

- `ids` must be an array with 2-5 elements
- Each ID must be an integer and must exist in the establishments table
- The endpoint is publicly accessible (no authentication required)

## Database Changes

The following fields have been added to the establishments table:
- `status`: Current operational status (nullable string)
- `international_partnerships`: Description of international collaborations (nullable text)

## Testing

Comprehensive tests have been added to verify:
- Correct response structure
- Validation of input parameters
- Handling of establishments with and without program offerings
- Proper loading of related data (categories, labels, domains, etc.)

## Migration

To apply the database changes:

```bash
php artisan migrate
```

## Performance Considerations

The enhanced API includes eager loading of related models to minimize database queries:
- Categories
- Labels
- Program offerings with domains, grades, mentions, departments
- Accreditations

This ensures efficient data retrieval even with complex relational data.
