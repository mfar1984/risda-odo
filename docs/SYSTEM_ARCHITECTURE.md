# RISDA Odometer System Architecture

## Overview
RISDA Odometer adalah sistem pengurusan kenderaan yang direka untuk organisasi RISDA dengan struktur hierarki dan keperluan data isolation yang ketat.

## Organizational Hierarchy

```
HQ RISDA (Headquarters)
├── RISDA Negeri (State Level)
│   ├── RISDA Bahagian (Division Level)
│   │   ├── RISDA Stesen (Station Level)
│   │   │   ├── Staff
│   │   │   └── Vehicles
│   │   └── Staff
│   └── Staff
└── Staff
```

## Data Isolation Strategy

### 1. Multi-Tenant Architecture
- **Tenant Identification**: Setiap level organisasi mempunyai tenant ID yang unik
- **Data Segregation**: Data dipisahkan berdasarkan hierarki organisasi
- **Access Control**: Akses data dikawalan berdasarkan level organisasi pengguna

### 2. Database Design Principles

#### Primary Keys & Foreign Keys
```sql
-- Setiap table mempunyai organisasi reference
CREATE TABLE vehicles (
    id BIGINT PRIMARY KEY,
    risda_stesen_id BIGINT REFERENCES risda_stesens(id),
    -- other fields
);

CREATE TABLE users (
    id BIGINT PRIMARY KEY,
    organisation_type ENUM('hq', 'negeri', 'bahagian', 'stesen'),
    organisation_id BIGINT, -- References to respective org table
    -- other fields
);
```

#### Data Access Patterns
- **HQ Level**: Akses kepada semua data
- **Negeri Level**: Akses kepada data negeri dan ke bawah
- **Bahagian Level**: Akses kepada data bahagian dan ke bawah
- **Stesen Level**: Akses kepada data stesen sahaja

### 3. Implementation Strategy

#### Model Scopes
```php
// Global scope untuk automatic filtering
class VehicleScope implements Scope
{
    public function apply(Builder $builder, Model $model)
    {
        $user = auth()->user();
        
        switch($user->organisation_type) {
            case 'stesen':
                $builder->whereHas('risdaStesen', function($q) use ($user) {
                    $q->where('id', $user->organisation_id);
                });
                break;
            case 'bahagian':
                $builder->whereHas('risdaStesen.risdaBahagian', function($q) use ($user) {
                    $q->where('id', $user->organisation_id);
                });
                break;
            // ... other cases
        }
    }
}
```

#### Middleware Implementation
```php
class OrganisationAccessMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth()->user();
        $requestedResource = $request->route()->parameter('vehicle');
        
        if (!$this->hasAccess($user, $requestedResource)) {
            abort(403, 'Unauthorized access to resource');
        }
        
        return $next($request);
    }
}
```

## Security Considerations

### 1. Authentication & Authorization
- **Role-Based Access Control (RBAC)**
- **Organisation-Based Permissions**
- **API Token dengan Scope Limitation**

### 2. Data Validation
- **Input Sanitization**
- **Organisation Context Validation**
- **Cross-Tenant Data Prevention**

### 3. Audit Trail
- **User Action Logging**
- **Data Change Tracking**
- **Access Pattern Monitoring**

## Performance Optimization

### 1. Database Indexing
```sql
-- Composite indexes untuk efficient filtering
CREATE INDEX idx_vehicles_org ON vehicles(risda_stesen_id, status);
CREATE INDEX idx_users_org ON users(organisation_type, organisation_id);
```

### 2. Caching Strategy
- **Organisation Data Caching**
- **User Permission Caching**
- **Query Result Caching dengan Tenant Context**

### 3. Query Optimization
- **Eager Loading dengan Constraints**
- **Selective Field Loading**
- **Pagination dengan Tenant Awareness**

## Scalability Considerations

### 1. Horizontal Scaling
- **Database Sharding by Organisation**
- **Load Balancing dengan Session Affinity**
- **Microservices Architecture untuk Large Scale**

### 2. Vertical Scaling
- **Database Connection Pooling**
- **Memory Optimization**
- **CPU Intensive Task Queuing**

## Monitoring & Maintenance

### 1. System Health Monitoring
- **Database Performance Metrics**
- **Application Response Times**
- **Error Rate Tracking**

### 2. Data Integrity Checks
- **Cross-Reference Validation**
- **Orphaned Data Detection**
- **Consistency Verification**

## Backup & Recovery

### 1. Data Backup Strategy
- **Tenant-Specific Backups**
- **Incremental Backup Scheduling**
- **Cross-Region Backup Replication**

### 2. Disaster Recovery
- **RTO/RPO Definitions**
- **Failover Procedures**
- **Data Recovery Testing**

## Compliance & Governance

### 1. Data Privacy
- **Personal Data Protection**
- **Data Retention Policies**
- **Right to be Forgotten Implementation**

### 2. Regulatory Compliance
- **Government Data Requirements**
- **Audit Trail Maintenance**
- **Reporting Capabilities**

## Future Enhancements

### 1. Advanced Features
- **Real-time Data Synchronization**
- **Mobile Application Support**
- **IoT Device Integration**

### 2. Analytics & Reporting
- **Business Intelligence Dashboard**
- **Predictive Analytics**
- **Custom Report Builder**

---

**Document Version**: 1.0  
**Last Updated**: 2025-09-24  
**Next Review**: 2025-12-24
