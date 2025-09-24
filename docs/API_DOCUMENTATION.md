# RISDA Odometer API Documentation

## Overview
API endpoints untuk sistem RISDA Odometer dengan data isolation berdasarkan organisasi.

## Authentication

### Bearer Token Authentication
```http
Authorization: Bearer {api_token}
```

### Organisation Context
Setiap API request akan automatically filtered berdasarkan organisation context dari authenticated user.

## Base URL
```
Production: https://risda-odometer.gov.my/api/v1
Development: http://localhost:8000/api/v1
```

## Response Format

### Success Response
```json
{
    "success": true,
    "data": {
        // Response data
    },
    "message": "Operation successful",
    "meta": {
        "organisation_type": "bahagian",
        "organisation_id": 1,
        "timestamp": "2025-09-24T10:30:00Z"
    }
}
```

### Error Response
```json
{
    "success": false,
    "error": {
        "code": "VALIDATION_ERROR",
        "message": "The given data was invalid.",
        "details": {
            "nama_stesen": ["The nama stesen field is required."]
        }
    },
    "meta": {
        "timestamp": "2025-09-24T10:30:00Z"
    }
}
```

## RISDA Bahagian Endpoints

### List Bahagians
```http
GET /api/v1/bahagians
```

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_bahagian": "RISDA Bahagian Klang",
            "no_telefon": "03-3371-2345",
            "email": "klang@risda.gov.my",
            "no_fax": "03-3371-2346",
            "status": "aktif",
            "alamat": {
                "alamat_1": "Jalan Tengku Kelana",
                "alamat_2": "Taman Klang Jaya",
                "poskod": "41000",
                "bandar": "Klang",
                "negeri": "Selangor",
                "negara": "Malaysia"
            },
            "stesen_count": 3,
            "created_at": "2025-09-24T10:00:00Z",
            "updated_at": "2025-09-24T10:00:00Z"
        }
    ],
    "meta": {
        "total": 1,
        "per_page": 15,
        "current_page": 1,
        "last_page": 1
    }
}
```

### Get Bahagian Details
```http
GET /api/v1/bahagians/{id}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nama_bahagian": "RISDA Bahagian Klang",
        "no_telefon": "03-3371-2345",
        "email": "klang@risda.gov.my",
        "no_fax": "03-3371-2346",
        "status": "aktif",
        "alamat": {
            "alamat_1": "Jalan Tengku Kelana",
            "alamat_2": "Taman Klang Jaya",
            "poskod": "41000",
            "bandar": "Klang",
            "negeri": "Selangor",
            "negara": "Malaysia"
        },
        "stesens": [
            {
                "id": 1,
                "nama_stesen": "RISDA Stesen Klang Utara",
                "status": "aktif"
            }
        ],
        "created_at": "2025-09-24T10:00:00Z",
        "updated_at": "2025-09-24T10:00:00Z"
    }
}
```

### Create Bahagian
```http
POST /api/v1/bahagians
Content-Type: application/json
```

**Request Body:**
```json
{
    "nama_bahagian": "RISDA Bahagian Baru",
    "no_telefon": "03-1234-5678",
    "email": "baru@risda.gov.my",
    "no_fax": "03-1234-5679",
    "status_dropdown": "aktif",
    "alamat_1": "Jalan Baru 123",
    "alamat_2": "Taman Baru",
    "poskod": "50000",
    "bandar": "Kuala Lumpur",
    "negeri": "Kuala Lumpur",
    "negara": "Malaysia"
}
```

### Update Bahagian
```http
PUT /api/v1/bahagians/{id}
Content-Type: application/json
```

### Delete Bahagian
```http
DELETE /api/v1/bahagians/{id}
```

## RISDA Stesen Endpoints

### List Stesens
```http
GET /api/v1/stesens
```

**Query Parameters:**
- `bahagian_id` (optional): Filter by bahagian ID
- `status` (optional): Filter by status (aktif, tidak_aktif, dalam_pembinaan)
- `negeri` (optional): Filter by negeri

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nama_stesen": "RISDA Stesen Klang Utara",
            "no_telefon": "03-3371-3000",
            "email": "stesen.klang.utara@risda.gov.my",
            "no_fax": "03-3371-3001",
            "status": "aktif",
            "bahagian": {
                "id": 1,
                "nama_bahagian": "RISDA Bahagian Klang"
            },
            "alamat": {
                "alamat_1": "Lot 456, Jalan Klang Utara",
                "alamat_2": "Taman Klang Utama",
                "poskod": "41000",
                "bandar": "Klang",
                "negeri": "Selangor",
                "negara": "Malaysia"
            },
            "vehicle_count": 5,
            "created_at": "2025-09-24T10:00:00Z",
            "updated_at": "2025-09-24T10:00:00Z"
        }
    ]
}
```

### Get Stesen Details
```http
GET /api/v1/stesens/{id}
```

### Create Stesen
```http
POST /api/v1/stesens
Content-Type: application/json
```

**Request Body:**
```json
{
    "risda_bahagian_id": 1,
    "nama_stesen": "RISDA Stesen Baru",
    "no_telefon": "03-1234-5678",
    "email": "stesen.baru@risda.gov.my",
    "no_fax": "03-1234-5679",
    "status_dropdown": "aktif",
    "alamat_1": "Jalan Stesen 123",
    "alamat_2": "Taman Stesen",
    "poskod": "50000",
    "bandar": "Kuala Lumpur",
    "negeri": "Kuala Lumpur",
    "negara": "Malaysia"
}
```

### Update Stesen
```http
PUT /api/v1/stesens/{id}
```

### Delete Stesen
```http
DELETE /api/v1/stesens/{id}
```

## Vehicle Endpoints (Planned)

### List Vehicles
```http
GET /api/v1/vehicles
```

### Get Vehicle Details
```http
GET /api/v1/vehicles/{id}
```

### Create Vehicle
```http
POST /api/v1/vehicles
```

### Update Vehicle
```http
PUT /api/v1/vehicles/{id}
```

### Delete Vehicle
```http
DELETE /api/v1/vehicles/{id}
```

## Organisation Hierarchy Endpoints

### Get Organisation Tree
```http
GET /api/v1/organisation/tree
```

**Response:**
```json
{
    "success": true,
    "data": {
        "organisation_type": "bahagian",
        "organisation_id": 1,
        "organisation_name": "RISDA Bahagian Klang",
        "children": [
            {
                "type": "stesen",
                "id": 1,
                "name": "RISDA Stesen Klang Utara",
                "vehicle_count": 5
            },
            {
                "type": "stesen",
                "id": 2,
                "name": "RISDA Stesen Klang Selatan",
                "vehicle_count": 3
            }
        ],
        "parent": {
            "type": "negeri",
            "id": 1,
            "name": "RISDA Negeri Selangor"
        }
    }
}
```

## Error Codes

| Code | Description |
|------|-------------|
| `VALIDATION_ERROR` | Request validation failed |
| `UNAUTHORIZED` | Authentication required |
| `FORBIDDEN` | Access denied to resource |
| `NOT_FOUND` | Resource not found |
| `ORGANISATION_MISMATCH` | Resource doesn't belong to user's organisation |
| `INACTIVE_ORGANISATION` | Organisation is not active |
| `DUPLICATE_RESOURCE` | Resource already exists |
| `DEPENDENCY_EXISTS` | Cannot delete resource with dependencies |

## Rate Limiting

- **Authenticated requests**: 1000 requests per hour
- **Unauthenticated requests**: 100 requests per hour
- **Bulk operations**: 50 requests per hour

## Data Isolation Rules

### HQ Level Users
- Can access all data across all organisations
- Can create/update/delete any resource
- No filtering applied

### Negeri Level Users
- Can access data within their negeri only
- Can manage bahagians and stesens in their negeri
- Automatic filtering by negeri

### Bahagian Level Users
- Can access data within their bahagian only
- Can manage stesens under their bahagian
- Can view vehicles in their stesens
- Automatic filtering by bahagian

### Stesen Level Users
- Can access data within their stesen only
- Can manage vehicles in their stesen
- Cannot access other stesen data
- Automatic filtering by stesen

## Webhook Events (Planned)

### Organisation Events
- `organisation.created`
- `organisation.updated`
- `organisation.deleted`
- `organisation.status_changed`

### Vehicle Events
- `vehicle.created`
- `vehicle.updated`
- `vehicle.deleted`
- `vehicle.status_changed`

---

**Document Version**: 1.0  
**Last Updated**: 2025-09-24  
**Next Review**: 2025-12-24
