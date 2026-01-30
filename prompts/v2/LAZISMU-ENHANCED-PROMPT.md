# 🤖 LazisMU Tamantirto - AI Agent Development Prompt (Enhanced v2)

## 📋 **CONTEXT & PROJECT OVERVIEW**

```
PROJECT NAME: LazisMU Tamantirto
TYPE: Full Website (Donation Platform + Company Profile + Blog)
CLIENT: Lembaga Amil Zakat Muhammadiyah Tamantirto
BUDGET: Rp 2.000.000
TIMELINE: 4-6 weeks (faster is better)
TARGET: Production-ready MVP
PARADIGM: Clean Code + SOLID Principles + Laravel 12 Best Practices
```

---

## 🎯 **PROJECT REQUIREMENTS**

### **Primary Features:**

**1. DONATION SYSTEM**
- Campaign management dengan multiple categories (Pendidikan, Kesehatan, Masjid, dll)
- Donation types: Zakat, Infaq, Sedekah, Wakaf, Fidyah
- Payment methods: QRIS - GoPay Merchant (upload foto), Bank Transfer (manually)
- Auto-verification (if not suspicious) + Manual verification
- Donation tracking dengan transaction ID
- Email + WhatsApp notifications
- Zakat calculator
- Real-time progress tracking

**2. COMPANY PROFILE**
- Static pages (About, Visi Misi, Sejarah, Kontak, dll)
- Flexible page templates
- SEO-ready (meta tags)
- Homepage dengan dynamic sections

**3. BLOG/ARTIKEL SYSTEM**
- Multiple categories (Berita, Kegiatan, Inspirasi, dll)
- Featured posts
- View counter
- SEO optimization
- Rich text editor

**4. ORGANIZATION STRUCTURE**
- Hierarchical structure (Ketua → Divisi → Staff)
- Team member profiles
- Photo gallery
- Organizational chart visualization

**5. CMS FEATURES**
- Media library (central file management)
- Menu builder (header, footer, mobile)
- Site settings (contact, social media, logo, dll)
- **RBAC with granular permissions**
- Audit logging

---

## 🏗️ **TECH STACK**

```yaml
Backend:
  Framework: Laravel 12.x
  UI: Livewire 3 Volt (reactive components)
  Admin: Flux UI (40+ components)
  PHP: 8.3+ (required for Laravel 12)
  
Frontend:
  CSS: Tailwind CSS v3
  JS: Alpine.js (minimal)
  Icons: Lucide Icons
  
Database:
  Primary: MySQL 8.0 (production) | SQLite (development)
  Tables: 20 (8 donation + 8 company profile + 4 RBAC)
  
Laravel 12 Features:
  Auth: Laravel Breeze with RBAC
  Storage: Local (shared hosting ready)
  Queue: Database (for notifications)
  Cache: Database
  Validation: Laravel 12 enhanced validation rules
  Collections: New Laravel 12 collection methods
  Events: Improved event broadcasting
  
Authorization:
  RBAC: Spatie Permission v6 (Laravel 12 compatible)
  Policies: Resource-based authorization
  Gates: Custom permission checks
  
Payment:
  Method: Manual upload (QRIS, Transfer)
  Gateway: None (upload proof only)
  
Notifications:
  Email: SMTP (configurable)
  WhatsApp: Fonnte API (optional)
```

---

## 🔐 **RBAC SYSTEM (Enhanced)**

### **Permission Structure:**

```php
// Permission naming convention: {resource}.{action}
// Example: campaign.create, donation.verify, blog.publish

Permissions (40 total):
├── Campaign Management (8)
│   ├── campaign.view_any
│   ├── campaign.view
│   ├── campaign.create
│   ├── campaign.update
│   ├── campaign.delete
│   ├── campaign.restore
│   ├── campaign.force_delete
│   └── campaign.publish
│
├── Donation Management (10)
│   ├── donation.view_any
│   ├── donation.view
│   ├── donation.verify
│   ├── donation.reject
│   ├── donation.export
│   ├── donation.view_sensitive_data
│   ├── donation.send_notification
│   ├── donation.manual_create
│   ├── donation.refund
│   └── donation.view_reports
│
├── Blog Management (8)
│   ├── blog.view_any
│   ├── blog.view
│   ├── blog.create
│   ├── blog.update
│   ├── blog.delete
│   ├── blog.restore
│   ├── blog.force_delete
│   └── blog.publish
│
├── Page Management (6)
│   ├── page.view_any
│   ├── page.view
│   ├── page.create
│   ├── page.update
│   ├── page.delete
│   └── page.publish
│
├── Organization Management (4)
│   ├── organization.manage_structure
│   ├── organization.manage_members
│   ├── organization.view_hierarchy
│   └── organization.export
│
├── Media Management (3)
│   ├── media.upload
│   ├── media.delete
│   └── media.organize
│
└── System Management (1)
    ├── system.manage_settings
    ├── system.manage_menus
    ├── system.view_audit_logs
    ├── system.manage_users
    ├── system.manage_roles
    └── system.backup_database
```

### **Role Definitions:**

```php
Roles (4):
├── Super Admin
│   └── Permissions: ALL (*)
│
├── Admin
│   └── Permissions: 
│       - All campaign.* 
│       - All donation.* (except refund)
│       - All blog.*
│       - All page.*
│       - organization.manage_*
│       - media.*
│       - system.manage_menus
│       - system.view_audit_logs
│
├── Editor
│   └── Permissions:
│       - campaign.view_any, view, create, update
│       - donation.view_any, view, verify (auto-verified only)
│       - blog.view_any, view, create, update
│       - page.view_any, view, create, update
│       - media.upload, organize
│
└── Viewer (optional)
    └── Permissions:
        - campaign.view_any, view
        - donation.view_any, view
        - blog.view_any, view
        - page.view_any, view
```

### **RBAC Implementation:**

```php
// File: app/Models/User.php
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    
    protected $guard_name = 'web';
    
    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }
    
    public function canVerifyDonation(): bool
    {
        return $this->hasPermissionTo('donation.verify');
    }
    
    public function canPublishContent(): bool
    {
        return $this->hasAnyPermission([
            'campaign.publish',
            'blog.publish',
            'page.publish'
        ]);
    }
}

// File: app/Policies/CampaignPolicy.php
class CampaignPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('campaign.view_any');
    }
    
    public function create(User $user): bool
    {
        return $user->can('campaign.create');
    }
    
    public function update(User $user, Campaign $campaign): bool
    {
        // Editor can only update their own campaigns
        if ($user->hasRole('editor')) {
            return $user->can('campaign.update') 
                && $campaign->created_by === $user->id;
        }
        
        return $user->can('campaign.update');
    }
    
    public function publish(User $user, Campaign $campaign): bool
    {
        return $user->can('campaign.publish');
    }
}

// File: database/seeders/RolePermissionSeeder.php
class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        // Create permissions
        $this->createPermissions();
        
        // Create roles and assign permissions
        $this->createSuperAdminRole();
        $this->createAdminRole();
        $this->createEditorRole();
        $this->createViewerRole();
        
        // Create default super admin user
        $this->createDefaultSuperAdmin();
    }
    
    private function createPermissions(): void
    {
        $permissions = [
            // Campaign permissions
            'campaign.view_any',
            'campaign.view',
            'campaign.create',
            'campaign.update',
            'campaign.delete',
            'campaign.restore',
            'campaign.force_delete',
            'campaign.publish',
            
            // Add all other permissions...
        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }
    }
    
    private function createSuperAdminRole(): void
    {
        $role = Role::firstOrCreate(['name' => 'super_admin']);
        $role->givePermissionTo(Permission::all());
    }
    
    private function createDefaultSuperAdmin(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@lazismu-tamantirto.org'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('SuperAdmin123!'),
                'email_verified_at' => now(),
            ]
        );
        
        $user->assignRole('super_admin');
    }
}
```

---

## 🧹 **CLEAN CODE PRINCIPLES (by @s4.codes)**

### **1. Every Function Should Tell a Story**

```php
// ❌ BAD: Function does too much, unclear intent
class DonationController
{
    public function process(Request $request)
    {
        $data = $request->validate([...]);
        $campaign = Campaign::find($data['campaign_id']);
        
        if ($data['amount'] < 10000 || $data['amount'] > 1000000) {
            $suspicious = true;
        }
        
        if (!preg_match('/^(\+62|62|0)[0-9]{9,12}$/', $data['phone'])) {
            $suspicious = true;
        }
        
        $donation = Donation::create([...]);
        
        if (!$suspicious) {
            $donation->update(['status' => 'verified']);
        }
        
        Mail::to($data['email'])->send(new DonationReceived($donation));
        
        return redirect()->back();
    }
}

// ✅ GOOD: Each function tells a clear story
class DonationController
{
    public function __construct(
        private DonationService $donationService,
        private NotificationService $notificationService
    ) {}
    
    public function store(StoreDonationRequest $request): RedirectResponse
    {
        $donation = $this->donationService->createFromRequest($request);
        
        $this->notificationService->sendDonationConfirmation($donation);
        
        return redirect()
            ->route('donation.tracking', $donation->tracking_code)
            ->with('success', 'Terima kasih atas donasi Anda!');
    }
}

class DonationService
{
    public function createFromRequest(StoreDonationRequest $request): Donation
    {
        $validatedData = $request->validated();
        
        $campaign = $this->findCampaignOrFail($validatedData['campaign_id']);
        
        $donation = $this->buildDonation($validatedData, $campaign);
        
        $this->checkAndMarkSuspiciousIfNeeded($donation);
        
        $donation->save();
        
        return $donation;
    }
    
    private function findCampaignOrFail(string $campaignId): Campaign
    {
        return Campaign::findOrFail($campaignId);
    }
    
    private function buildDonation(array $data, Campaign $campaign): Donation
    {
        return new Donation([
            'campaign_id' => $campaign->id,
            'donor_name' => $data['donor_name'],
            'donor_email' => $data['donor_email'],
            'donor_phone' => $this->formatPhoneNumber($data['donor_phone']),
            'amount' => $data['amount'],
            'payment_method' => $data['payment_method'],
            'tracking_code' => $this->generateUniqueTrackingCode(),
            'status' => DonationStatus::PENDING,
        ]);
    }
    
    private function checkAndMarkSuspiciousIfNeeded(Donation $donation): void
    {
        if ($this->isSuspicious($donation)) {
            $donation->status = DonationStatus::PENDING_MANUAL_VERIFICATION;
            $donation->suspicious_reason = $this->getSuspiciousReasons($donation);
        } else {
            $donation->status = DonationStatus::VERIFIED;
        }
    }
    
    private function isSuspicious(Donation $donation): bool
    {
        return $this->hasInvalidAmount($donation)
            || $this->hasInvalidPhoneFormat($donation)
            || $this->hasMultipleDonationsInShortPeriod($donation);
    }
}
```

### **2. Extract Low-Level Details**

```php
// ❌ BAD: Business logic buried in low-level details
class Campaign extends Model
{
    public function getProgressAttribute()
    {
        $raised = $this->donations()->where('status', 'verified')->sum('amount');
        $target = $this->target_amount;
        
        if ($target == 0) {
            return 0;
        }
        
        $percentage = ($raised / $target) * 100;
        
        if ($percentage > 100) {
            return 100;
        }
        
        return round($percentage, 2);
    }
}

// ✅ GOOD: Business logic clear, details extracted
class Campaign extends Model
{
    public function calculateProgress(): float
    {
        $amountRaised = $this->totalVerifiedDonations();
        
        return $this->calculateProgressPercentage($amountRaised);
    }
    
    private function totalVerifiedDonations(): int
    {
        return $this->verifiedDonations()->sum('amount');
    }
    
    private function calculateProgressPercentage(int $raised): float
    {
        if ($this->hasNoTarget()) {
            return 0.0;
        }
        
        $percentage = ($raised / $this->target_amount) * 100;
        
        return $this->capProgressAt100Percent($percentage);
    }
    
    private function hasNoTarget(): bool
    {
        return $this->target_amount === 0;
    }
    
    private function capProgressAt100Percent(float $percentage): float
    {
        return round(min($percentage, 100), 2);
    }
    
    // Eloquent scope for reusability
    public function scopeVerifiedDonations($query)
    {
        return $query->donations()->where('status', DonationStatus::VERIFIED);
    }
}
```

### **3. Intention-Revealing Names**

```php
// ❌ BAD: Unclear names
class CmpCtrl
{
    public function get($id)
    {
        $c = Campaign::find($id);
        $d = $c->donations()->where('s', 'v')->get();
        $t = $d->sum('a');
        
        return view('c.show', compact('c', 'd', 't'));
    }
}

// ✅ GOOD: Names reveal intention
class CampaignController
{
    public function show(string $campaignId): View
    {
        $campaign = $this->findCampaignWithRelations($campaignId);
        
        $verifiedDonations = $campaign->verifiedDonations;
        
        $totalRaised = $this->calculateTotalRaised($verifiedDonations);
        
        return view('campaigns.show', [
            'campaign' => $campaign,
            'donations' => $verifiedDonations,
            'totalRaised' => $totalRaised,
            'progressPercentage' => $campaign->calculateProgress(),
        ]);
    }
    
    private function findCampaignWithRelations(string $campaignId): Campaign
    {
        return Campaign::with([
            'category',
            'images',
            'verifiedDonations' => fn($query) => $query->latest()->limit(10)
        ])->findOrFail($campaignId);
    }
    
    private function calculateTotalRaised(Collection $donations): int
    {
        return $donations->sum('amount');
    }
}
```

### **4. Avoid Disinformation**

```php
// ❌ BAD: Misleading names
$campaignList = Campaign::first(); // Returns single item, not list!
$activeCampaign = Campaign::all(); // Returns all, not just active!
$donationCount = Donation::get(); // Returns collection, not count!

// ✅ GOOD: Accurate names
$firstCampaign = Campaign::first();
$allCampaigns = Campaign::all();
$activeCampaigns = Campaign::active()->get();
$donationCount = Donation::count();
$donations = Donation::get();
```

### **5. Make Meaningful Distinctions**

```php
// ❌ BAD: Meaningless distinctions
class CampaignInfo {}
class CampaignData {}
class CampaignObject {}

function getCampaigns() {}
function getCampaignList() {}
function retrieveCampaigns() {}

// ✅ GOOD: Meaningful distinctions
class Campaign {} // The domain model
class CampaignViewModel {} // For view presentation
class CampaignResource {} // For API responses
class CampaignDTO {} // For data transfer

function findActiveCampaigns(): Collection {}
function searchCampaignsByKeyword(string $keyword): Collection {}
function getFeaturedCampaigns(): Collection {}
```

### **6. Use Pronounceable Names**

```php
// ❌ BAD: Unpronounceable
$genymdhms = now(); // generation year-month-day-hour-minute-second
$modymdhms = now(); // modification year-month-day-hour-minute-second
$prcntg = 85; // percentage

// ✅ GOOD: Pronounceable
$createdAt = now();
$updatedAt = now();
$progressPercentage = 85;
```

### **7. Use Searchable Names**

```php
// ❌ BAD: Magic numbers and unclear constants
if ($donation->amount > 1000000) { // What is 1000000?
    // ...
}

if ($user->role == 1) { // What is role 1?
    // ...
}

// ✅ GOOD: Searchable constants
class DonationLimit
{
    public const MINIMUM_AMOUNT = 10_000;
    public const MAXIMUM_AMOUNT_FOR_AUTO_VERIFY = 1_000_000;
    public const SUSPICIOUS_THRESHOLD = 5_000_000;
}

if ($donation->amount > DonationLimit::MAXIMUM_AMOUNT_FOR_AUTO_VERIFY) {
    $donation->markAsRequiringManualVerification();
}

// Or use Enums (Laravel 12 best practice)
enum UserRole: string
{
    case SUPER_ADMIN = 'super_admin';
    case ADMIN = 'admin';
    case EDITOR = 'editor';
    case VIEWER = 'viewer';
}

if ($user->hasRole(UserRole::ADMIN->value)) {
    // ...
}
```

### **8. Avoid Encoding**

```php
// ❌ BAD: Hungarian notation, type encoding
$strDonorName = "John Doe";
$intAmount = 100000;
$arrCampaigns = Campaign::all();
$objDonation = new Donation();

interface ICampaignRepository {}
class CampaignRepositoryImpl {}

// ✅ GOOD: Clean names with type hints
$donorName = "John Doe"; // string
$amount = 100_000; // int
$campaigns = Campaign::all(); // Collection
$donation = new Donation(); // Donation

interface CampaignRepository {}
class EloquentCampaignRepository implements CampaignRepository {}
```

### **9. Avoid Mental Mapping**

```php
// ❌ BAD: Reader must mentally map 'c' to campaign
foreach ($campaigns as $c) {
    $d = $c->donations;
    $t = $d->sum('amount');
    
    if ($t > 1000000) {
        $c->featured = true;
        $c->save();
    }
}

// ✅ GOOD: Clear, no mental mapping needed
foreach ($campaigns as $campaign) {
    $donations = $campaign->donations;
    $totalRaised = $donations->sum('amount');
    
    if ($this->shouldBeMarkedAsFeatured($totalRaised)) {
        $campaign->markAsFeatured();
    }
}

// Even better: Extract to meaningful method
class Campaign extends Model
{
    public function updateFeaturedStatusBasedOnDonations(): void
    {
        $totalRaised = $this->calculateTotalRaised();
        
        if ($this->shouldBeMarkedAsFeatured($totalRaised)) {
            $this->markAsFeatured();
        }
    }
    
    private function calculateTotalRaised(): int
    {
        return $this->donations->sum('amount');
    }
    
    private function shouldBeMarkedAsFeatured(int $totalRaised): bool
    {
        return $totalRaised >= CampaignLimit::FEATURED_THRESHOLD;
    }
    
    private function markAsFeatured(): void
    {
        $this->update(['featured' => true]);
    }
}

// Usage becomes a story
$campaign->updateFeaturedStatusBasedOnDonations();
```

---

## 📁 **CLEAN CODE FILE STRUCTURE**

```
app/
├── Models/
│   ├── Campaign.php
│   ├── Donation.php
│   ├── BlogPost.php
│   └── ... (domain models only)
│
├── Services/ (Business Logic Layer)
│   ├── Donation/
│   │   ├── DonationService.php
│   │   ├── DonationVerificationService.php
│   │   └── DonationCalculatorService.php
│   ├── Notification/
│   │   ├── NotificationService.php
│   │   ├── EmailNotificationService.php
│   │   └── WhatsAppNotificationService.php
│   └── Media/
│       ├── MediaService.php
│       └── ImageOptimizationService.php
│
├── Actions/ (Single Purpose Actions)
│   ├── Campaign/
│   │   ├── CreateCampaignAction.php
│   │   ├── UpdateCampaignAction.php
│   │   ├── PublishCampaignAction.php
│   │   └── ArchiveCampaignAction.php
│   └── Donation/
│       ├── ProcessDonationAction.php
│       ├── VerifyDonationAction.php
│       └── RefundDonationAction.php
│
├── DataTransferObjects/ (DTOs)
│   ├── DonationData.php
│   ├── CampaignData.php
│   └── NotificationData.php
│
├── ViewModels/ (For Complex Views)
│   ├── CampaignDetailViewModel.php
│   ├── DonationDashboardViewModel.php
│   └── BlogPostViewModel.php
│
├── Repositories/ (Optional: For complex queries)
│   ├── CampaignRepository.php
│   └── DonationRepository.php
│
├── Policies/
│   ├── CampaignPolicy.php
│   ├── DonationPolicy.php
│   ├── BlogPostPolicy.php
│   └── ... (one policy per model)
│
├── Http/
│   ├── Controllers/
│   │   ├── Public/
│   │   │   ├── HomeController.php
│   │   │   ├── CampaignController.php (thin controllers)
│   │   │   └── DonationController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── CampaignController.php
│   │       └── DonationVerificationController.php
│   │
│   ├── Requests/ (Form Requests)
│   │   ├── StoreCampaignRequest.php
│   │   ├── UpdateCampaignRequest.php
│   │   ├── StoreDonationRequest.php
│   │   └── VerifyDonationRequest.php
│   │
│   └── Resources/ (API Resources)
│       ├── CampaignResource.php
│       └── DonationResource.php
│
├── Enums/ (Laravel 12 Backed Enums)
│   ├── CampaignStatus.php
│   ├── CampaignType.php
│   ├── DonationStatus.php
│   ├── PaymentMethod.php
│   └── UserRole.php
│
└── Constants/ (App-wide constants)
    ├── DonationLimit.php
    ├── CampaignLimit.php
    └── MediaLimit.php
```

---

## 🔧 **LARAVEL 12 SPECIFIC FEATURES**

### **1. PHP 8.3+ Features**

```php
// Typed class constants
class DonationLimit
{
    public const int MINIMUM_AMOUNT = 10_000;
    public const int MAXIMUM_AUTO_VERIFY = 1_000_000;
}

// Readonly classes
readonly class DonationData
{
    public function __construct(
        public string $campaignId,
        public string $donorName,
        public string $donorEmail,
        public int $amount,
        public PaymentMethod $paymentMethod,
    ) {}
}
```

### **2. Enhanced Enums**

```php
enum DonationStatus: string
{
    case PENDING = 'pending';
    case VERIFIED = 'verified';
    case REJECTED = 'rejected';
    case PENDING_MANUAL = 'pending_manual_verification';
    case REFUNDED = 'refunded';
    
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Menunggu Verifikasi',
            self::VERIFIED => 'Terverifikasi',
            self::REJECTED => 'Ditolak',
            self::PENDING_MANUAL => 'Perlu Verifikasi Manual',
            self::REFUNDED => 'Dikembalikan',
        };
    }
    
    public function color(): string
    {
        return match($this) {
            self::PENDING => 'yellow',
            self::VERIFIED => 'green',
            self::REJECTED => 'red',
            self::PENDING_MANUAL => 'orange',
            self::REFUNDED => 'gray',
        };
    }
    
    public function canBeVerified(): bool
    {
        return in_array($this, [self::PENDING, self::PENDING_MANUAL]);
    }
}

// Usage in Model
class Donation extends Model
{
    protected function casts(): array
    {
        return [
            'status' => DonationStatus::class,
            'payment_method' => PaymentMethod::class,
        ];
    }
    
    public function isVerified(): bool
    {
        return $this->status === DonationStatus::VERIFIED;
    }
    
    public function canBeModified(): bool
    {
        return $this->status->canBeVerified();
    }
}
```

### **3. Model Casts (Laravel 12)**

```php
class Campaign extends Model
{
    protected function casts(): array
    {
        return [
            'target_amount' => 'integer',
            'start_date' => 'date',
            'end_date' => 'date',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
            'status' => CampaignStatus::class,
            'type' => CampaignType::class,
            'settings' => 'array',
            'created_at' => 'datetime',
        ];
    }
}
```

### **4. Validation (Laravel 12)**

```php
class StoreDonationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Public can donate
    }
    
    public function rules(): array
    {
        return [
            'campaign_id' => [
                'required',
                'uuid',
                'exists:campaigns,id',
            ],
            'donor_name' => [
                'required',
                'string',
                'min:3',
                'max:100',
            ],
            'donor_email' => [
                'required',
                'email:rfc,dns',
                'max:100',
            ],
            'donor_phone' => [
                'required',
                'regex:/^(\+62|62|0)[0-9]{9,12}$/',
            ],
            'amount' => [
                'required',
                'integer',
                'min:' . DonationLimit::MINIMUM_AMOUNT,
            ],
            'payment_method' => [
                'required',
                'in:' . implode(',', PaymentMethod::values()),
            ],
            'payment_proof' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png',
                'max:2048', // 2MB
            ],
            'is_anonymous' => [
                'boolean',
            ],
            'message' => [
                'nullable',
                'string',
                'max:500',
            ],
        ];
    }
    
    public function messages(): array
    {
        return [
            'donor_phone.regex' => 'Format nomor telepon tidak valid. Gunakan format: 08xx atau +62xx',
            'amount.min' => 'Donasi minimal adalah Rp ' . number_format(DonationLimit::MINIMUM_AMOUNT, 0, ',', '.'),
        ];
    }
    
    protected function prepareForValidation(): void
    {
        $this->merge([
            'donor_phone' => $this->normalizePhoneNumber($this->donor_phone),
        ]);
    }
    
    private function normalizePhoneNumber(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }
        
        // Remove spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);
        
        // Convert 08xx to +628xx
        if (str_starts_with($phone, '0')) {
            return '+62' . substr($phone, 1);
        }
        
        // Convert 62xx to +62xx
        if (str_starts_with($phone, '62')) {
            return '+' . $phone;
        }
        
        return $phone;
    }
}
```

### **5. Collections (Laravel 12)**

```php
class DonationService
{
    public function calculateDonationStatistics(Collection $donations): array
    {
        return [
            'total_amount' => $donations
                ->where('status', DonationStatus::VERIFIED)
                ->sum('amount'),
                
            'average_donation' => $donations
                ->where('status', DonationStatus::VERIFIED)
                ->avg('amount'),
                
            'total_donors' => $donations
                ->where('status', DonationStatus::VERIFIED)
                ->unique('donor_email')
                ->count(),
                
            'top_campaigns' => $donations
                ->where('status', DonationStatus::VERIFIED)
                ->groupBy('campaign_id')
                ->map(fn($group) => [
                    'campaign' => $group->first()->campaign,
                    'total' => $group->sum('amount'),
                    'count' => $group->count(),
                ])
                ->sortByDesc('total')
                ->take(5)
                ->values(),
                
            'donations_by_method' => $donations
                ->where('status', DonationStatus::VERIFIED)
                ->groupBy('payment_method')
                ->map(fn($group) => [
                    'count' => $group->count(),
                    'total' => $group->sum('amount'),
                ]),
        ];
    }
}
```

---

## 🗄️ **DATABASE SCHEMA (Enhanced with RBAC)**

```sql
-- RBAC Tables (Spatie Permission)
CREATE TABLE roles (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY roles_name_guard_name_unique (name, guard_name)
);

CREATE TABLE permissions (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    guard_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY permissions_name_guard_name_unique (name, guard_name)
);

CREATE TABLE model_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, model_id, model_type),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    INDEX model_has_permissions_model_id_model_type_index (model_id, model_type)
);

CREATE TABLE model_has_roles (
    role_id BIGINT UNSIGNED NOT NULL,
    model_type VARCHAR(255) NOT NULL,
    model_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (role_id, model_id, model_type),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    INDEX model_has_roles_model_id_model_type_index (model_id, model_type)
);

CREATE TABLE role_has_permissions (
    permission_id BIGINT UNSIGNED NOT NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    PRIMARY KEY (permission_id, role_id),
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- Enhanced users table
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    last_login_at TIMESTAMP NULL,
    last_login_ip VARCHAR(45) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    INDEX users_email_index (email),
    INDEX users_is_active_index (is_active)
);

-- Rest of tables remain the same...
-- (campaigns, donations, blog_posts, etc.)
```

---

## 💡 **CODING STANDARDS (Enhanced)**

```php
// 1. SOLID Principles

// Single Responsibility Principle
class DonationVerificationService
{
    // Only handles donation verification
    public function verify(Donation $donation): VerificationResult {}
}

class DonationNotificationService
{
    // Only handles notifications
    public function sendConfirmation(Donation $donation): void {}
}

// Open/Closed Principle
interface PaymentProcessor
{
    public function process(Donation $donation): ProcessResult;
}

class QRISPaymentProcessor implements PaymentProcessor
{
    public function process(Donation $donation): ProcessResult
    {
        // QRIS specific logic
    }
}

class BankTransferProcessor implements PaymentProcessor
{
    public function process(Donation $donation): ProcessResult
    {
        // Bank transfer specific logic
    }
}

// Liskov Substitution Principle
class CampaignRepository
{
    public function find(string $id): Campaign
    {
        return Campaign::findOrFail($id);
    }
}

class ActiveCampaignRepository extends CampaignRepository
{
    public function find(string $id): Campaign
    {
        // Still returns Campaign, maintains parent contract
        return Campaign::active()->findOrFail($id);
    }
}

// Interface Segregation Principle
interface Verifiable
{
    public function verify(): void;
}

interface Notifiable
{
    public function sendNotification(): void;
}

interface Trackable
{
    public function getTrackingCode(): string;
}

class Donation extends Model implements Verifiable, Notifiable, Trackable
{
    // Only implements what it needs
}

// Dependency Inversion Principle
class DonationController
{
    // Depend on abstractions, not concrete classes
    public function __construct(
        private DonationServiceInterface $donationService,
        private NotificationServiceInterface $notificationService
    ) {}
}

// 2. Method Ordering
class Campaign extends Model
{
    // 1. Constants
    public const STATUS_DRAFT = 'draft';
    
    // 2. Properties
    protected $fillable = [];
    
    // 3. Lifecycle Methods
    protected static function booted(): void {}
    
    // 4. Accessors/Mutators
    protected function targetAmount(): Attribute {}
    
    // 5. Relationships
    public function donations(): HasMany {}
    
    // 6. Scopes
    public function scopeActive($query) {}
    
    // 7. Public Methods
    public function calculateProgress(): float {}
    
    // 8. Protected/Private Methods
    private function hasNoTarget(): bool {}
}

// 3. Type Hints (Always)
class DonationService
{
    public function create(array $data): Donation
    {
        // Type hinted parameters and return
    }
    
    private function validate(array $data): bool
    {
        // Type hinted
    }
}

// 4. Early Returns
public function verify(Donation $donation): VerificationResult
{
    if ($donation->isAlreadyVerified()) {
        return VerificationResult::alreadyVerified();
    }
    
    if ($donation->isSuspicious()) {
        return VerificationResult::requiresManualReview();
    }
    
    if ($donation->hasInvalidProof()) {
        return VerificationResult::invalidProof();
    }
    
    // Happy path at the end
    $donation->markAsVerified();
    
    return VerificationResult::success();
}

// 5. Guard Clauses
public function update(Campaign $campaign, array $data): Campaign
{
    if ($campaign->isArchived()) {
        throw new CampaignArchivedException();
    }
    
    if ($campaign->hasActiveDonations() && isset($data['target_amount'])) {
        throw new CannotModifyActiveTargetException();
    }
    
    // Main logic
    $campaign->update($data);
    
    return $campaign->fresh();
}
```

---

## 🚀 **DEVELOPMENT WORKFLOW (Enhanced)**

### **Phase 1: Foundation (Week 1)**
```
Day 1-2: Setup
- ✅ Install Laravel 12
- ✅ Install Flux UI
- ✅ Install Livewire 3 Volt
- ✅ Install Spatie Permission
- ✅ Configure database
- ✅ Setup Pint for code style

Day 3-4: Database
- ✅ Create all 20 migrations (RBAC + original tables)
- ✅ Create RolePermissionSeeder
- ✅ Create other seeders
- ✅ Test migrations on SQLite and MySQL

Day 5-7: Models & Services
- ✅ Create all models with relationships
- ✅ Implement enums
- ✅ Create service layer structure
- ✅ Write policies
- ✅ Setup audit logging
```

### **Phase 2: RBAC & Auth (Week 2)**
```
Day 8-10: Authentication & Authorization
- ✅ Setup Laravel Breeze
- ✅ Implement RBAC with Spatie Permission
- ✅ Create user management interface
- ✅ Create role & permission management
- ✅ Test all permission scenarios

Day 11-14: Admin Panel Foundation
- ✅ Setup Flux UI admin layout
- ✅ Create dashboard
- ✅ Implement audit log viewer
- ✅ Create user CRUD with role assignment
```

### **Phase 3: Donation System (Week 3)**
```
Day 15-17: Campaign Management
- ✅ Campaign CRUD (admin) with authorization
- ✅ Campaign list & detail (public)
- ✅ Image upload & gallery

Day 18-21: Donation Flow
- ✅ Donation wizard (4 steps)
- ✅ Auto-verification logic
- ✅ Admin verification panel
- ✅ Notification system
- ✅ Tracking page
```

### **Phase 4: Content Management (Week 4)**
```
Day 22-24: Blog System
- ✅ Blog CRUD with authorization
- ✅ Blog list & detail (public)
- ✅ Category management

Day 25-28: CMS Features
- ✅ Page CMS with authorization
- ✅ Organization structure
- ✅ Team members
- ✅ Media library
- ✅ Menu builder
- ✅ Settings management
```

### **Phase 5: Polish & Deploy (Week 5-6)**
```
Day 29-35: Integration & Testing
- ✅ Homepage integration
- ✅ Search functionality
- ✅ SEO optimization
- ✅ Performance tuning
- ✅ Security audit
- ✅ Write tests
- ✅ Code review (Clean Code principles)

Day 36-42: Documentation & Deployment
- ✅ Write documentation
- ✅ Deployment guide
- ✅ Training materials
- ✅ Deploy to production
```

---

## ✅ **DELIVERABLES CHECKLIST**

```
Code Quality:
□ All functions follow Clean Code principles
□ No function longer than 20 lines
□ All names are intention-revealing
□ No magic numbers (use constants/enums)
□ Type hints on all methods
□ PHPDoc for complex logic
□ PSR-12 compliant (verified with Pint)

RBAC Implementation:
□ All 40 permissions defined
□ 4 roles with proper permissions
□ Policies for all models
□ Gates for custom checks
□ Role seeder with default users
□ Permission middleware applied
□ Authorization tested

Laravel 12 Features:
□ PHP 8.3+ features used
□ Backed enums for all constants
□ Model casts() method
□ Enhanced validation
□ Type-hinted everything

Database:
□ 20 migrations (17 + 3 RBAC)
□ All relationships defined
□ Indexes on foreign keys
□ Soft deletes where needed
□ UUID for public-facing IDs

Features:
□ Donation system (complete)
□ Auto-verification with RBAC
□ Email + WhatsApp notifications
□ Campaign management
□ Blog system
□ Page CMS
□ Organization structure
□ Team members
□ Media library
□ Menu builder
□ Settings management
□ Search functionality
□ SEO optimization

Testing:
□ Feature tests (main flows)
□ Unit tests (critical logic)
□ Policy tests (all permissions)
□ Manual testing checklist

Documentation:
□ README.md (comprehensive)
□ .env.example (complete)
□ RBAC documentation
□ Clean Code examples
□ Admin guide
□ Deployment guide
```

---

## 📝 **SPECIFIC AI AGENT INSTRUCTIONS**

When you receive this prompt, please:

1. **Confirm Understanding**
   - Acknowledge RBAC requirements
   - Acknowledge Clean Code principles
   - Acknowledge Laravel 12 features

2. **Ask Clarifying Questions**
   - Any unclear requirements?
   - Specific Clean Code scenarios?
   - RBAC edge cases?

3. **Code Generation Priority**
   - Always extract complex logic to services
   - Always use intention-revealing names
   - Always type hint parameters & returns
   - Always follow Clean Code principles
   - Always implement proper RBAC checks

4. **Show Examples**
   - Before/After Clean Code examples
   - RBAC implementation examples
   - Laravel 12 feature usage examples

5. **Code Review**
   - Review each file against Clean Code principles
   - Check RBAC implementation
   - Verify type hints
   - Check for magic numbers
   - Verify function length (max 20 lines)

---

## 🎯 **READY TO START?**

Reply with:
- ✅ Confirmation of Clean Code understanding
- ✅ Confirmation of RBAC requirements
- ✅ Confirmation of Laravel 12 features
- ❓ Any questions or clarifications needed
- 🎯 Which component you'll build first
- 📋 Any assumptions you're making

**Let's build LazisMU Tamantirto with SOLID principles, Clean Code, and bulletproof RBAC! 🔥**