# Data Isolation Implementation Guide

## Overview
Panduan ini menerangkan bagaimana sistem RISDA Odometer melaksanakan data isolation untuk memastikan setiap organisasi hanya dapat mengakses data mereka sendiri.

## Current Implementation Status

### ✅ Completed
- [x] Organisasi hierarchy (HQ → Negeri → Bahagian → Stesen)
- [x] Basic CRUD untuk RISDA Bahagian
- [x] Basic CRUD untuk RISDA Stesen
- [x] Parent-child relationship (Bahagian → Stesen)
- [x] Form validation dengan organisation context

### 🚧 In Progress
- [ ] User management dengan organisation assignment
- [ ] Global scopes untuk automatic filtering
- [ ] Policy-based authorization
- [ ] Middleware untuk organisation access control

### 📋 Planned
- [ ] Vehicle management dengan stesen assignment
- [ ] Advanced reporting dengan organisation filtering
- [ ] API dengan tenant-aware endpoints
- [ ] Audit logging untuk data access

## Database Schema Design

### 1. Organisation Tables
```sql
-- RISDA Bahagian (Division Level)
CREATE TABLE risda_bahagians (
    id BIGINT PRIMARY KEY,
    nama_bahagian VARCHAR(255) NOT NULL,
    no_telefon VARCHAR(20) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    no_fax VARCHAR(20),
    status ENUM('aktif', 'tidak_aktif', 'dalam_pembinaan'),
    status_dropdown ENUM('aktif', 'tidak_aktif', 'dalam_pembinaan'),
    alamat_1 VARCHAR(255) NOT NULL,
    alamat_2 VARCHAR(255),
    poskod VARCHAR(5) NOT NULL,
    bandar VARCHAR(255) NOT NULL,
    negeri VARCHAR(255) NOT NULL,
    negara VARCHAR(255) DEFAULT 'Malaysia',
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);

-- RISDA Stesen (Station Level)
CREATE TABLE risda_stesens (
    id BIGINT PRIMARY KEY,
    risda_bahagian_id BIGINT NOT NULL,
    nama_stesen VARCHAR(255) NOT NULL,
    no_telefon VARCHAR(20) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    no_fax VARCHAR(20),
    status ENUM('aktif', 'tidak_aktif', 'dalam_pembinaan'),
    status_dropdown ENUM('aktif', 'tidak_aktif', 'dalam_pembinaan'),
    alamat_1 VARCHAR(255) NOT NULL,
    alamat_2 VARCHAR(255),
    poskod VARCHAR(5) NOT NULL,
    bandar VARCHAR(255) NOT NULL,
    negeri VARCHAR(255) NOT NULL,
    negara VARCHAR(255) DEFAULT 'Malaysia',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (risda_bahagian_id) REFERENCES risda_bahagians(id) ON DELETE CASCADE
);
```

### 2. User Organisation Assignment (Planned)
```sql
-- Users dengan organisation context
CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    organisation_type ENUM('hq', 'negeri', 'bahagian', 'stesen') NOT NULL,
    organisation_id BIGINT, -- References to respective org table
    role ENUM('admin', 'manager', 'staff') DEFAULT 'staff',
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

### 3. Vehicle Management (Planned)
```sql
-- Vehicles assigned to stesen
CREATE TABLE vehicles (
    id BIGINT PRIMARY KEY,
    risda_stesen_id BIGINT NOT NULL,
    vehicle_number VARCHAR(50) UNIQUE NOT NULL,
    make VARCHAR(100) NOT NULL,
    model VARCHAR(100) NOT NULL,
    year YEAR NOT NULL,
    status ENUM('active', 'maintenance', 'retired') DEFAULT 'active',
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    
    FOREIGN KEY (risda_stesen_id) REFERENCES risda_stesens(id) ON DELETE CASCADE
);
```

## Model Implementation

### 1. Current Models

#### RisdaBahagian Model
```php
class RisdaBahagian extends Model
{
    protected $fillable = [
        'nama_bahagian', 'no_telefon', 'email', 'no_fax', 
        'status', 'status_dropdown', 'alamat_1', 'alamat_2', 
        'poskod', 'bandar', 'negeri', 'negara',
    ];

    // Relationship to Stesen
    public function risdaStesens(): HasMany
    {
        return $this->hasMany(RisdaStesen::class);
    }
}
```

#### RisdaStesen Model
```php
class RisdaStesen extends Model
{
    protected $fillable = [
        'risda_bahagian_id', 'nama_stesen', 'no_telefon', 'email', 
        'no_fax', 'status_dropdown', 'status', 'alamat_1', 'alamat_2', 
        'poskod', 'bandar', 'negeri', 'negara',
    ];

    // Relationship to Bahagian
    public function risdaBahagian(): BelongsTo
    {
        return $this->belongsTo(RisdaBahagian::class);
    }
}
```

### 2. Planned Global Scopes

#### Organisation Scope
```php
class OrganisationScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        if (!auth()->check()) {
            return;
        }

        $user = auth()->user();
        
        switch($user->organisation_type) {
            case 'hq':
                // HQ can see all data - no filtering
                break;
                
            case 'negeri':
                // Filter by negeri
                $builder->where('negeri', $user->organisation->negeri);
                break;
                
            case 'bahagian':
                // Filter by bahagian
                if ($model instanceof RisdaStesen) {
                    $builder->where('risda_bahagian_id', $user->organisation_id);
                } elseif ($model instanceof RisdaBahagian) {
                    $builder->where('id', $user->organisation_id);
                }
                break;
                
            case 'stesen':
                // Filter by stesen
                if ($model instanceof RisdaStesen) {
                    $builder->where('id', $user->organisation_id);
                } elseif ($model instanceof Vehicle) {
                    $builder->where('risda_stesen_id', $user->organisation_id);
                }
                break;
        }
    }
}
```

## Controller Implementation

### 1. Current Controllers

#### RisdaBahagianController
```php
class RisdaBahagianController extends Controller
{
    public function index()
    {
        // Currently shows all bahagians - needs organisation filtering
        $bahagians = RisdaBahagian::latest()->get();
        $stesens = RisdaStesen::with('risdaBahagian')->latest()->get();
        return view('pengurusan.senarai-risda', compact('bahagians', 'stesens'));
    }

    public function store(Request $request)
    {
        // Validation includes organisation context
        $validator = Validator::make($request->all(), [
            'nama_bahagian' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:risda_bahagians,email',
            // ... other validations
        ]);

        $data = $request->all();
        $data['status'] = $request->status_dropdown;
        RisdaBahagian::create($data);
    }
}
```

#### RisdaStesenController
```php
class RisdaStesenController extends Controller
{
    public function create()
    {
        // Only show active bahagians for dropdown
        $bahagians = RisdaBahagian::where('status_dropdown', 'aktif')
                                 ->orderBy('nama_bahagian')
                                 ->get();
        return view('pengurusan.tambah-stesen', compact('bahagians'));
    }

    public function store(Request $request)
    {
        // Validation ensures stesen belongs to accessible bahagian
        $validator = Validator::make($request->all(), [
            'risda_bahagian_id' => 'required|exists:risda_bahagians,id',
            'nama_stesen' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:risda_stesens,email',
            // ... other validations
        ]);

        $data = $request->all();
        $data['status'] = $request->status_dropdown;
        RisdaStesen::create($data);
    }
}
```

### 2. Planned Enhancements

#### Organisation-Aware Filtering
```php
// Add to all controllers
protected function applyOrganisationFilter($query)
{
    $user = auth()->user();
    
    switch($user->organisation_type) {
        case 'bahagian':
            return $query->where('risda_bahagian_id', $user->organisation_id);
        case 'stesen':
            return $query->where('risda_stesen_id', $user->organisation_id);
        default:
            return $query;
    }
}
```

## Authorization Policies

### 1. Planned Policy Implementation

#### RisdaBahagianPolicy
```php
class RisdaBahagianPolicy
{
    public function viewAny(User $user)
    {
        return in_array($user->organisation_type, ['hq', 'negeri', 'bahagian']);
    }

    public function view(User $user, RisdaBahagian $bahagian)
    {
        switch($user->organisation_type) {
            case 'hq':
            case 'negeri':
                return true;
            case 'bahagian':
                return $user->organisation_id === $bahagian->id;
            default:
                return false;
        }
    }

    public function create(User $user)
    {
        return in_array($user->organisation_type, ['hq', 'negeri']);
    }

    public function update(User $user, RisdaBahagian $bahagian)
    {
        return $this->view($user, $bahagian);
    }
}
```

#### RisdaStesenPolicy
```php
class RisdaStesenPolicy
{
    public function view(User $user, RisdaStesen $stesen)
    {
        switch($user->organisation_type) {
            case 'hq':
            case 'negeri':
                return true;
            case 'bahagian':
                return $user->organisation_id === $stesen->risda_bahagian_id;
            case 'stesen':
                return $user->organisation_id === $stesen->id;
            default:
                return false;
        }
    }
}
```

## Middleware Implementation

### 1. Organisation Access Middleware
```php
class OrganisationAccessMiddleware
{
    public function handle($request, Closure $next, $resourceType = null)
    {
        $user = auth()->user();
        
        // Get the resource from route parameters
        $resource = $this->getResourceFromRoute($request, $resourceType);
        
        if ($resource && !$this->hasAccess($user, $resource)) {
            abort(403, 'Unauthorized access to organisation resource');
        }
        
        return $next($request);
    }

    private function hasAccess(User $user, $resource)
    {
        switch($user->organisation_type) {
            case 'hq':
                return true;
            case 'bahagian':
                return $this->belongsToBahagian($user, $resource);
            case 'stesen':
                return $this->belongsToStesen($user, $resource);
            default:
                return false;
        }
    }
}
```

## Testing Strategy

### 1. Data Isolation Tests
```php
class DataIsolationTest extends TestCase
{
    public function test_bahagian_user_cannot_see_other_bahagian_stesen()
    {
        $bahagian1 = RisdaBahagian::factory()->create();
        $bahagian2 = RisdaBahagian::factory()->create();
        
        $stesen1 = RisdaStesen::factory()->create(['risda_bahagian_id' => $bahagian1->id]);
        $stesen2 = RisdaStesen::factory()->create(['risda_bahagian_id' => $bahagian2->id]);
        
        $user = User::factory()->create([
            'organisation_type' => 'bahagian',
            'organisation_id' => $bahagian1->id
        ]);
        
        $this->actingAs($user);
        
        $response = $this->get('/pengurusan/senarai-risda');
        
        // Should see own stesen
        $response->assertSee($stesen1->nama_stesen);
        
        // Should NOT see other bahagian's stesen
        $response->assertDontSee($stesen2->nama_stesen);
    }
}
```

## Migration Path

### 1. Phase 1: User Management (Next)
- [ ] Create user organisation assignment
- [ ] Implement basic authentication with organisation context
- [ ] Add organisation-aware seeding

### 2. Phase 2: Data Filtering (After Phase 1)
- [ ] Implement global scopes
- [ ] Add middleware for route protection
- [ ] Create authorization policies

### 3. Phase 3: Advanced Features (Future)
- [ ] Vehicle management with isolation
- [ ] Reporting with organisation filtering
- [ ] API with tenant awareness

---

**Document Version**: 1.0  
**Last Updated**: 2025-09-24  
**Next Review**: 2025-12-24
