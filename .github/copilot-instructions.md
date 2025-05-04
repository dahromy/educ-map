# Project: Educ-Map (Laravel API Application)

## 1. Project Goal & Context
Build a full backend API using **Laravel 12** for "Educ-Map". This application serves as a comprehensive directory and interactive map of accredited higher education institutions and their programs in Madagascar. The API will be consumed by a separate frontend application (e.g., React, Vue, Angular, Mobile).

## 2. Technology Stack
- **Backend:** Laravel 12, PHP 8.4
- **API:** Laravel API Resources and Controllers
- **ORM:** Laravel Eloquent ORM
- **Database:** PostgreSQL 14 (potentially with PostGIS extension for geospatial features)
- **Authentication:** Laravel Sanctum (for API authentication with tokens)
- **Admin (Optional):** Laravel Filament (for backend data management alongside the API)
- **Mapping Frontend (Context):** The API needs to provide data suitable for libraries like Leaflet or Mapbox GL JS.

## 3. Existing Data Model (Eloquent Models)
The application uses the following Eloquent models (already defined with properties and basic relationships based on a previous schema design):
- `Category`
- `Establishment` (includes basic info, location fields like `address`, `latitude`, `longitude`, contact details, indicators like `student_count`, `success_rate`, `professional_insertion_rate`, `first_habilitation_year`, timestamps `created_at`, `updated_at`)
- `Department` (linked to Establishment)
- `Domain` (Field of Study)
- `Grade` (Academic Level)
- `Mention` (Program/Major/Specialization)
- `Reference` (Accreditation decree details, including `main_date`, `document_url`)
- `ProgramOffering` (Links Establishment/Department, Domain, Grade, Mention; includes `tuition_fees_info`, `program_duration_info`)
- `Accreditation` (Junction between ProgramOffering and Reference, includes `reference_type`, `accreditation_date`, `is_recent` flag)
- `DoctoralSchoolAffiliation` (Links Doctoral School Establishments to affiliated institution names)
- `Label` (e.g., "Excellence")
- `EstablishmentLabel` (Junction between Establishment and Label)
- `User` (Extends Laravel's default User model; includes `roles`, `associated_establishment` link)
- `SearchHistory` (Linked to User; stores text query and JSON filters)
- `FaqItem` (Question, Answer, Category)
- `OfficialDocument` (Title, Description, URL/Path, Type, optional link to Reference)

## 4. Core API Features to Implement (Based on Requirements)

Please assist with generating code, suggesting best practices, and implementing the following features using Laravel conventions where possible:

**4.1. Core Read Endpoints (Public Access)**
    - **Establishment List (GET /api/establishments):**
        - Create API Resource and Controller for Establishments.
        - Implement basic filters using query parameters for: `region` (exact), `category.category_name` (exact), `name` (partial), `abbreviation` (partial).
        - Implement sorting by `name`.
        - Use appropriate API Resources for list view (name, abbreviation, location, category, logo_url).
        - Implement pagination using Laravel's built-in pagination.
    - **Establishment Detail (GET /api/establishments/{id}):**
        - Return single Establishment with detailed information.
        - Use specific API Resource to include *all* details: related departments, program offerings (with their domains, grades, mentions, accreditations, references), labels, contact info, indicators, etc. Ensure nested data is readable.
    - **Domain/Grade/Mention/Category Lists (GET /api/domains, etc.):**
        - Simple collection endpoints to provide lists for populating frontend filters.
    - **"Nouveautés" Endpoint (GET /api/establishments/recent):**
        - Custom controller method or route.
        - Fetch Establishments recently added (`created_at` within X days) OR Establishments with recent accreditations (`Accreditation.is_recent = true`). Define criteria clearly. Return limited data suitable for a sidebar.

**4.2. Advanced Search & Filtering Endpoint (GET /api/establishments)**
    - Extend the existing Establishment list endpoint filters:
        - Filter by `domains.name` (partial match, requiring joins).
        - Filter by specific `labels.name`.
        - Filter by `references.main_date` range for habilitation date.
        - **(Future/Advanced):** Geospatial filter (radius search around lat/lon) - requires PostGIS setup and potentially a custom filter. Advise on setup if possible.
    - Implement sorting by `references.main_date` (requires join) and `student_count`.

**4.3. Map Data Endpoint (GET /api/map/markers or similar)**
    - Custom controller method or route.
    - Should return a lightweight list of `establishment_id`, `name`, `latitude`, `longitude`, `category_name` for efficient map marker generation.

**4.4. Comparison Endpoint (GET /api/compare?ids[]=1&ids[]=5&ids[]=10)**
    - Custom controller method that accepts an array of Establishment IDs.
    - Fetches the requested Establishments.
    - Returns a specific Resource containing only the comparison fields: `name`, `abbreviation`, `student_count`, `success_rate`, `professional_insertion_rate`, `tuition_fees_info`, `program_duration_info`. Handle potential variations in program fees/duration within an establishment (e.g., average, range, or specific program).

**4.5. Authentication & User Accounts**
    - Setup Laravel Sanctum for API authentication.
    - Create endpoints for:
        - User registration (`POST /api/register`) with validation.
        - User login (`POST /api/login`) returning token.
        - Get current user profile (`GET /api/me`) (requires authentication).
        - Update user profile (PUT/PATCH `/api/me`).
    - Implement role-based access control in middleware, policies, or gates (protecting admin/establishment routes).

**4.6. Search History (Requires Authentication)**
    - **Save Search (POST /api/me/searches):** Endpoint to save `search_query_text` and `search_filters` (JSON) for the logged-in user. Link to `User` entity.
    - **List Searches (GET /api/me/searches):** Retrieve saved searches for the logged-in user.
    - **Delete Search (DELETE /api/me/searches/{id}):** Allow user to delete a saved search.

**4.7. Establishment Management API (Requires ROLE_ESTABLISHMENT + Ownership Check)**
    - Enable PUT/PATCH operations on `/api/establishments/{id}`.
    - Use Policies or Gate to ensure users can only edit their `associated_establishment`.
    - Define validation rules for updatable fields (address, contact info, indicators, logo_url, description fields, etc.).
    - **(Optional):** Discuss/implement a validation/moderation workflow where changes are submitted for admin approval instead of being saved directly.

**4.8. Admin API (Requires ROLE_ADMIN)**
    - Enable full CRUD (POST, PUT, DELETE) on most core entities (`Category`, `Establishment`, `Domain`, `Grade`, `Mention`, `Reference`, `Label`, `FaqItem`, `OfficialDocument`). Secure these operations.
    - **User Management:** Endpoint(s) to list, view, create, edit (roles, associate establishment), and delete users.
    - **Statistics Endpoint(s) (GET /api/admin/stats/...):**
        - Endpoint returning counts per category.
        - Endpoint potentially returning counts of habilitations per year (requires querying `Reference` or `Accreditation` dates).
        - Endpoint potentially returning geographical distribution data (counts per region).
    - **Validation Endpoint(s):** If implementing the establishment update validation workflow, create endpoints for admins to list pending changes and approve/reject them.

**4.9. Export/Reporting API (Requires ROLE_ADMIN or specific roles)**
    - Create custom controller methods (e.g., `GET /api/export/establishments.{format}` where `format` is csv, xlsx, pdf).
    - Accept filter parameters (reuse from advanced search).
    - Use appropriate libraries (like PhpSpreadsheet via Laravel Excel) to generate the file content and return it with the correct headers (`Content-Type`, `Content-Disposition`).

**4.10. Help & Support API**
    - **FAQ List (GET /api/faq_items):** Public endpoint to list FAQ items, possibly filterable by `category`.
    - **Official Documents List (GET /api/official_documents):** Public endpoint to list documents, possibly filterable by `document_type`.
    - **Contact Form Submission (POST /api/contact):** Public endpoint accepting contact details and message. Implement validation using Laravel's Form Request Validation. Use Laravel Mail to send the email notification.

## 5. General Instructions & Goals
- **Generate Code:** Provide complete code snippets for models (if refinement needed), API Resources, Controllers, Form Requests, Policies, custom Eloquent queries (using query builder), and basic PHPUnit tests (feature tests for API endpoints are highly valuable).
- **Best Practices:** Follow Laravel conventions. Emphasize security (input validation, authorization), performance (database query optimization, appropriate eager loading), and maintainability.
- **Laravel Features:** Leverage built-in features like Query Builder, Eloquent relationships, API Resources, Request Validation, and Policies extensively. Advise when custom solutions are more appropriate.
- **Security:** Pay close attention to access control for different roles (`ROLE_USER`, `ROLE_ADMIN`, `ROLE_ESTABLISHMENT`) and ownership checks using Laravel's Gate and Policies.
- **Error Handling:** Ensure proper API error responses (e.g., 400 for bad requests, 401/403 for auth errors, 404 for not found, 500 for server errors).

## 6. Final Check
Please acknowledge you understand this project context, the entities involved, and the required API features. Let's start building step-by-step, likely beginning with the core read endpoints for Establishments.
