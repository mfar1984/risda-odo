# RISDA Odometer Development Guidelines

## Code Standards & Best Practices

### 1. Laravel Conventions
- **PSR-4 Autoloading**: Ikut namespace conventions
- **Eloquent Naming**: Model names singular, table names plural
- **Route Naming**: Gunakan resource route naming conventions
- **Controller Actions**: RESTful action naming (index, create, store, show, edit, update, destroy)

### 2. Database Design Rules

#### Migration Guidelines
```php
// ✅ Good - Descriptive migration names
2025_09_24_create_risda_bahagians_table.php
2025_09_24_add_organisation_id_to_users_table.php

// ❌ Bad - Generic names
2025_09_24_create_table.php
2025_09_24_update_users.php
```

#### Foreign Key Conventions
```php
// ✅ Good - Clear relationship naming
$table->foreignId('risda_bahagian_id')->constrained('risda_bahagians')->onDelete('cascade');

// ❌ Bad - Unclear relationships
$table->integer('parent_id');
```

### 3. Model Relationships

#### Relationship Naming
```php
// ✅ Good - Clear relationship methods
public function risdaBahagian(): BelongsTo
{
    return $this->belongsTo(RisdaBahagian::class);
}

public function risdaStesens(): HasMany
{
    return $this->hasMany(RisdaStesen::class);
}

// ❌ Bad - Generic naming
public function parent()
public function children()
```

#### Global Scopes untuk Data Isolation
```php
// ✅ Required - Automatic data filtering
protected static function booted()
{
    static::addGlobalScope(new OrganisationScope);
}
```

### 4. Controller Guidelines

#### Data Isolation Implementation
```php
// ✅ Good - Always check organisation access
public function index()
{
    $user = auth()->user();
    $vehicles = Vehicle::whereHas('risdaStesen', function($query) use ($user) {
        $query->where('risda_bahagian_id', $user->organisation_id);
    })->get();
    
    return view('vehicles.index', compact('vehicles'));
}

// ❌ Bad - No organisation filtering
public function index()
{
    $vehicles = Vehicle::all(); // Exposes all data!
    return view('vehicles.index', compact('vehicles'));
}
```

#### Validation Rules
```php
// ✅ Good - Organisation-aware validation
public function store(Request $request)
{
    $request->validate([
        'risda_stesen_id' => [
            'required',
            'exists:risda_stesens,id',
            new BelongsToUserOrganisation(auth()->user())
        ]
    ]);
}
```

### 5. View Guidelines

#### Component Usage
```blade
{{-- ✅ Good - Use reusable components --}}
<x-forms.text-input 
    id="nama_stesen" 
    name="nama_stesen" 
    value="{{ old('nama_stesen') }}"
    required 
/>

{{-- ❌ Bad - Hardcoded HTML --}}
<input type="text" name="nama_stesen" class="form-control">
```

#### Consistent Styling
```blade
{{-- ✅ Good - Use component classes --}}
<x-buttons.primary-button type="submit">
    Simpan
</x-buttons.primary-button>

{{-- ❌ Bad - Inline styles --}}
<button style="background: blue; color: white;">Simpan</button>
```

## Security Guidelines

### 1. Data Access Control

#### Always Verify Organisation Access
```php
// ✅ Good - Check before any data operation
public function show(Vehicle $vehicle)
{
    $this->authorize('view', $vehicle);
    return view('vehicles.show', compact('vehicle'));
}

// ❌ Bad - Direct access without checks
public function show(Vehicle $vehicle)
{
    return view('vehicles.show', compact('vehicle'));
}
```

#### Policy Implementation
```php
// VehiclePolicy.php
public function view(User $user, Vehicle $vehicle)
{
    return $this->hasOrganisationAccess($user, $vehicle->risdaStesen);
}

private function hasOrganisationAccess(User $user, RisdaStesen $stesen)
{
    switch($user->organisation_type) {
        case 'stesen':
            return $user->organisation_id === $stesen->id;
        case 'bahagian':
            return $user->organisation_id === $stesen->risda_bahagian_id;
        // ... other cases
    }
}
```

### 2. Input Validation

#### Sanitization Rules
```php
// ✅ Good - Comprehensive validation
$request->validate([
    'nama_stesen' => 'required|string|max:255|regex:/^[a-zA-Z0-9\s\-\.]+$/',
    'no_telefon' => 'required|string|regex:/^[0-9\-\+\s\(\)]+$/',
    'email' => 'required|email|max:255|unique:risda_stesens,email',
    'poskod' => 'required|string|size:5|regex:/^[0-9]{5}$/',
]);
```

### 3. SQL Injection Prevention
```php
// ✅ Good - Use Eloquent ORM
$vehicles = Vehicle::where('status', $status)->get();

// ❌ Bad - Raw SQL without binding
$vehicles = DB::select("SELECT * FROM vehicles WHERE status = '$status'");
```

## Testing Guidelines

### 1. Unit Testing
```php
// ✅ Good - Test data isolation
public function test_user_can_only_see_own_organisation_vehicles()
{
    $user = User::factory()->create(['organisation_type' => 'stesen', 'organisation_id' => 1]);
    $ownVehicle = Vehicle::factory()->create(['risda_stesen_id' => 1]);
    $otherVehicle = Vehicle::factory()->create(['risda_stesen_id' => 2]);
    
    $this->actingAs($user);
    $response = $this->get('/vehicles');
    
    $response->assertSee($ownVehicle->name);
    $response->assertDontSee($otherVehicle->name);
}
```

### 2. Feature Testing
```php
// ✅ Good - Test complete workflows
public function test_stesen_user_can_create_vehicle()
{
    $user = User::factory()->stesenUser()->create();
    $this->actingAs($user);
    
    $response = $this->post('/vehicles', [
        'name' => 'Test Vehicle',
        'risda_stesen_id' => $user->organisation_id
    ]);
    
    $response->assertRedirect('/vehicles');
    $this->assertDatabaseHas('vehicles', ['name' => 'Test Vehicle']);
}
```

## Performance Guidelines

### 1. Database Optimization
```php
// ✅ Good - Eager loading with constraints
$stesens = RisdaStesen::with(['risdaBahagian' => function($query) {
    $query->select('id', 'nama_bahagian');
}])->get();

// ❌ Bad - N+1 query problem
$stesens = RisdaStesen::all();
foreach($stesens as $stesen) {
    echo $stesen->risdaBahagian->nama_bahagian; // N+1 queries!
}
```

### 2. Caching Strategy
```php
// ✅ Good - Cache with organisation context
$cacheKey = "vehicles.stesen.{$user->organisation_id}";
$vehicles = Cache::remember($cacheKey, 3600, function() use ($user) {
    return Vehicle::forOrganisation($user)->get();
});
```

## UI/UX Guidelines

### 1. Design Consistency
- **Font**: Poppins, 11px-14px range
- **Border Radius**: 0px-2px maximum
- **Colors**: Use RISDA color scheme
- **Components**: Always use established component system

### 2. Form Design
```blade
{{-- ✅ Good - Consistent form structure --}}
<div style="display: flex; gap: 20px;">
    <div style="flex: 1;">
        <x-forms.input-label for="field1" value="Label 1" />
        <x-forms.text-input id="field1" name="field1" class="mt-1 block w-full" />
    </div>
    <div style="flex: 1;">
        <x-forms.input-label for="field2" value="Label 2" />
        <x-forms.text-input id="field2" name="field2" class="mt-1 block w-full" />
    </div>
</div>
```

### 3. Error Handling
```blade
{{-- ✅ Good - Consistent error display --}}
<x-forms.input-error class="mt-2" :messages="$errors->get('field_name')" />
```

## Deployment Guidelines

### 1. Environment Configuration
```env
# ✅ Required - Organisation-specific settings
APP_ORGANISATION_LEVELS=hq,negeri,bahagian,stesen
APP_DEFAULT_ORGANISATION_TYPE=stesen
APP_ENABLE_CROSS_TENANT_ACCESS=false
```

### 2. Database Migrations
```bash
# ✅ Good - Always backup before migration
php artisan backup:run
php artisan migrate --force

# ✅ Good - Verify data integrity after migration
php artisan app:verify-data-integrity
```

### 3. Cache Management
```bash
# ✅ Required - Clear all caches on deployment
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

## Code Review Checklist

### 1. Security Review
- [ ] Data isolation implemented correctly
- [ ] Input validation comprehensive
- [ ] Authorization checks in place
- [ ] No hardcoded credentials

### 2. Performance Review
- [ ] No N+1 query problems
- [ ] Appropriate caching implemented
- [ ] Database indexes optimized
- [ ] Memory usage reasonable

### 3. Code Quality Review
- [ ] Follows Laravel conventions
- [ ] Proper error handling
- [ ] Comprehensive testing
- [ ] Documentation updated

---

**Document Version**: 1.0  
**Last Updated**: 2025-09-24  
**Next Review**: 2025-12-24
