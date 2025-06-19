# Complete CRUD Operations for List Entities - API Usage Examples

This document demonstrates the complete CRUD (Create, Read, Update, Delete) operations for all list entities in the Educ-Map API.

## Authentication Required

For Create, Update, and Delete operations, you need admin authentication:

```bash
# Login as admin to get access token
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "admin@example.com",
    "password": "password"
  }'

# Use the returned token in Authorization header for protected routes
# Authorization: Bearer {your-token-here}
```

## 1. Domains API

### List Domains (Public)
```bash
# Get all domains
curl -X GET http://localhost:8000/api/domains

# Search domains
curl -X GET "http://localhost:8000/api/domains?search=Informatique"

# Sort domains by name
curl -X GET "http://localhost:8000/api/domains?sort_by=name&sort_direction=asc"
```

### Get Single Domain (Public)
```bash
curl -X GET http://localhost:8000/api/domains/1
```

### Create Domain (Admin Only)
```bash
curl -X POST http://localhost:8000/api/domains \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "name": "Intelligence Artificielle",
    "description": "Domaine spécialisé en IA et machine learning"
  }'
```

### Update Domain (Admin Only)
```bash
curl -X PUT http://localhost:8000/api/domains/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "name": "Intelligence Artificielle Avancée",
    "description": "Description mise à jour"
  }'
```

### Delete Domain (Admin Only)
```bash
curl -X DELETE http://localhost:8000/api/domains/1 \
  -H "Authorization: Bearer {token}"
```

## 2. Grades API

### List Grades (Public)
```bash
# Get all grades sorted by level
curl -X GET "http://localhost:8000/api/grades?sort_by=level&sort_direction=asc"

# Search grades
curl -X GET "http://localhost:8000/api/grades?search=Licence"
```

### Create Grade (Admin Only)
```bash
curl -X POST http://localhost:8000/api/grades \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "name": "Doctorat",
    "level": 8,
    "description": "Niveau doctoral (Bac+8)"
  }'
```

### Update Grade (Admin Only)
```bash
curl -X PUT http://localhost:8000/api/grades/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "name": "Master Pro",
    "level": 5,
    "description": "Master professionnel (Bac+5)"
  }'
```

### Delete Grade (Admin Only)
```bash
curl -X DELETE http://localhost:8000/api/grades/1 \
  -H "Authorization: Bearer {token}"
```

## 3. Mentions API

### List Mentions (Public)
```bash
# Get all mentions
curl -X GET http://localhost:8000/api/mentions

# Search mentions
curl -X GET "http://localhost:8000/api/mentions?search=Génie"
```

### Create Mention (Admin Only)
```bash
curl -X POST http://localhost:8000/api/mentions \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "name": "Cybersécurité",
    "description": "Spécialisation en sécurité informatique"
  }'
```

### Update Mention (Admin Only)
```bash
curl -X PUT http://localhost:8000/api/mentions/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "name": "Cybersécurité Avancée",
    "description": "Spécialisation avancée en sécurité"
  }'
```

### Delete Mention (Admin Only)
```bash
curl -X DELETE http://localhost:8000/api/mentions/1 \
  -H "Authorization: Bearer {token}"
```

## 4. Labels API

### List Labels (Public)
```bash
# Get all labels
curl -X GET http://localhost:8000/api/labels

# Search labels
curl -X GET "http://localhost:8000/api/labels?search=Excellence"
```

### Create Label (Admin Only)
```bash
curl -X POST http://localhost:8000/api/labels \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "name": "Excellence",
    "color": "#FF5733",
    "description": "Label d\'excellence académique"
  }'
```

### Update Label (Admin Only)
```bash
curl -X PUT http://localhost:8000/api/labels/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "name": "Excellence Premium",
    "color": "#33FF57",
    "description": "Label d\'excellence premium"
  }'
```

### Delete Label (Admin Only)
```bash
curl -X DELETE http://localhost:8000/api/labels/1 \
  -H "Authorization: Bearer {token}"
```

## 5. Categories API (Already existed, now with full CRUD)

### Create Category (Admin Only)
```bash
curl -X POST http://localhost:8000/api/categories \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "category_name": "École Polytechnique",
    "description": "Établissement d\'enseignement supérieur technique"
  }'
```

### Update Category (Admin Only)
```bash
curl -X PUT http://localhost:8000/api/categories/1 \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {token}" \
  -d '{
    "category_name": "Grande École d\'Ingénieurs",
    "description": "Institution d\'excellence en ingénierie"
  }'
```

### Delete Category (Admin Only)
```bash
curl -X DELETE http://localhost:8000/api/categories/1 \
  -H "Authorization: Bearer {token}"
```

## Response Formats

### Success Response (200/201)
```json
{
  "data": {
    "id": 1,
    "name": "Intelligence Artificielle",
    "description": "Domaine spécialisé en IA",
    "created_at": "2025-06-19T17:00:00.000000Z",
    "updated_at": "2025-06-19T17:00:00.000000Z"
  }
}
```

### Validation Error Response (422)
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "The name field is required."
    ]
  }
}
```

### Conflict Response (409) - Cannot delete with dependencies
```json
{
  "message": "Cannot delete domain. It has associated program offerings."
}
```

### Unauthorized Response (401)
```json
{
  "message": "Unauthenticated."
}
```

### Forbidden Response (403)
```json
{
  "message": "This action is unauthorized."
}
```

## Security & Validation

- **Authentication**: Required for Create, Update, Delete operations
- **Authorization**: Only users with `ROLE_ADMIN` can perform CUD operations
- **Validation**: 
  - Names are required and must be unique
  - Color fields must be valid hex codes (#RRGGBB)
  - Grade levels must be between 1-10
- **Referential Integrity**: Cannot delete entities that have associated records
