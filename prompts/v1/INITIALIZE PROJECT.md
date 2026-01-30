# 🤖 LazisMU Tamantirto - AI Agent Development Prompt

## 📋 **CONTEXT & PROJECT OVERVIEW**

```
PROJECT NAME: LazisMU Tamantirto
TYPE: Full Website (Donation Platform + Company Profile + Blog)
CLIENT: Lembaga Amil Zakat Muhammadiyah Tamantirto
BUDGET: Rp 2.000.000
TIMELINE: 4-6 weeks (faster is better)
TARGET: Production-ready MVP
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
- User roles (admin, editor)
- Audit logging

---

## 🏗️ **TECH STACK**

```yaml
Backend:
  Framework: Laravel 12.x
  UI: Livewire 3 (reactive components)
  Admin: Flux UI (40+ components)
  
Frontend:
  CSS: Tailwind CSS v3
  JS: Alpine.js (minimal)
  Icons: Lucide Icons
  
Database:
  Primary: MySQL 8.0 (production ready) | SQLite (development ready)
  Tables: 17 (8 donation + 8 company profile + 1 audit)
  
Features:
  Auth: Laravel Breeze (admin + editor)
  Storage: Local (shared hosting ready)
  Queue: Database (for notifications)
  Cache: Database
  
Payment:
  Method: Manual upload (QRIS, Transfer)
  Gateway: None (upload proof only)
  
Notifications:
  Email: SMTP (configurable)
  WhatsApp: Fonnte API (optional)
```

---

## 🗄️ **DATABASE SCHEMA**

### **ERD Reference:**
*(Paste DBML code ke dbdiagram.io untuk visualisasi)*

### **Table Groups:**

**A. DONATION SYSTEM (8 tables):**
1. `users` - Admin/editor accounts
2. `campaign_categories` - Categories (Pendidikan, Kesehatan, dll)
3. `campaigns` - Campaign programs (UUID)
4. `campaign_images` - Gallery images
5. `donations` - Donation transactions (no user account)
6. `payment_logs` - Payment tracking
7. `donation_notifications` - Email/WhatsApp queue
8. *(audit_logs - shared)*

**B. COMPANY PROFILE (8 tables):**
1. `pages` - Static pages (CMS)
2. `blog_categories` - Blog categories
3. `blog_posts` - Articles/news
4. `organization_structure` - Jabatan hierarchy
5. `team_members` - People in organization
6. `media_library` - Central file storage
7. `menus` - Navigation management
8. `settings` - Site configuration

**C. SHARED (1 table):**
1. `audit_logs` - Activity tracking

### **Key Relationships:**
```
users → campaigns (creator)
users → blog_posts (author)
users → pages (creator)
campaigns → donations (receives)
organization_structure → team_members (has many)
blog_categories → blog_posts (has many)
pages → menus (linked)
```

---

## 📁 **REQUIRED FILE STRUCTURE**

```
app/
├── Models/
│   ├── Campaign.php
│   ├── Donation.php
│   ├── Page.php
│   ├── BlogPost.php
│   ├── OrganizationStructure.php
│   ├── TeamMember.php
│   ├── Menu.php
│   └── Setting.php
│
├── Services/
│   ├── DonationService.php (auto-verification logic)
│   ├── NotificationService.php (email + WhatsApp)
│   └── MediaService.php (file upload handling)
│
├── Http/
│   ├── Controllers/
│   │   ├── Public/
│   │   │   ├── HomeController.php
│   │   │   ├── CampaignController.php
│   │   │   ├── DonationController.php
│   │   │   ├── BlogController.php
│   │   │   └── PageController.php
│   │   └── Admin/
│   │       ├── DashboardController.php
│   │       ├── CampaignController.php
│   │       ├── DonationController.php
│   │       ├── BlogController.php
│   │       ├── PageController.php
│   │       └── SettingController.php
│   │
│   └── Livewire/
│       ├── Public/
│       │   ├── CampaignList.php
│       │   ├── DonationWizard.php
│       │   └── BlogList.php
│       └── Admin/
│           ├── CampaignTable.php
│           ├── DonationVerification.php
│           └── MediaUploader.php
│
├── Enums/
│   ├── CampaignType.php (zakat, infaq, sedekah, wakaf, fidyah)
│   ├── CampaignStatus.php
│   ├── DonationStatus.php
│   └── UserRole.php
│
└── Helpers/
    └── helpers.php (feature flags, formatting, dll)

resources/
├── views/
│   ├── layouts/
│   │   ├── public.blade.php (orange theme)
│   │   └── admin.blade.php (Mary UI)
│   │
│   ├── components/
│   │   ├── public/
│   │   │   ├── campaign-card.blade.php
│   │   │   ├── blog-card.blade.php
│   │   │   └── team-card.blade.php
│   │   └── admin/
│   │
│   ├── pages/
│   │   ├── home.blade.php
│   │   ├── campaigns/
│   │   ├── blog/
│   │   └── about.blade.php
│   │
│   └── admin/
│       ├── dashboard.blade.php
│       ├── campaigns/
│       ├── donations/
│       └── settings/
│
└── css/
    ├── public.css (orange theme)
    └── admin.css (Mary UI)

database/
├── migrations/
│   ├── 2025_01_01_000001_create_campaign_categories_table.php
│   ├── 2025_01_01_000002_create_campaigns_table.php
│   ├── ... (15 more)
│   └── 2025_01_01_000017_create_settings_table.php
│
└── seeders/
    ├── DatabaseSeeder.php
    ├── CampaignCategorySeeder.php
    ├── BlogCategorySeeder.php
    ├── PageSeeder.php
    ├── MenuSeeder.php
    └── SettingSeeder.php
```

---

## 🎨 **DESIGN REQUIREMENTS**

### **Public Frontend (Orange Theme):**
```yaml
Color Palette:
  Primary: '#ea580c' (Orange 600)
  Light: '#fb923c' (Orange 400)
  Dark: '#c2410c' (Orange 700)
  Success: '#10b981' (Green 500)
  
Layout:
  - Mobile-first (320px+)
  - Bottom navigation (5 items)
  - Sticky header
  - Smooth animations
  
Pages:
  - Homepage (hero + featured campaigns + blog + stats)
  - Campaign list (filter + search)
  - Campaign detail (tabs: cerita, donatur, update, galeri)
  - Donation wizard (4 steps)
  - Blog list & detail
  - Static pages (about, contact, dll)
  - Organization structure (visual chart)
```

### **Admin Panel (Mary UI):**
```yaml
Layout:
  - Sidebar navigation (collapsible)
  - Top header with search
  - Breadcrumb
  
Dashboard:
  - Stats cards (pending donations, active campaigns, total funds)
  - Charts (donations per day, per category)
  - Recent donations table
  - Quick actions
  
Features:
  - CRUD for all entities
  - Rich text editor (TinyMCE/Trix)
  - Media library modal
  - Settings page (grouped)
  - Audit log viewer
```

---

## ⚙️ **KEY FEATURES IMPLEMENTATION**

### **1. Auto-Verification Logic:**
```php
// In DonationService.php
public function checkSuspicious(array $data): array
{
    $reasons = [];
    
    // Amount too low/high
    if ($data['amount'] < 10000) {
        $reasons[] = 'Jumlah < Rp 10k';
    }
    if ($data['amount'] > 1000000) {
        $reasons[] = 'Jumlah > Rp 1jt';
    }
    
    // Invalid phone
    if (!preg_match('/^(\+62|62|0)[0-9]{9,12}$/', $data['donor_phone'])) {
        $reasons[] = 'Format telepon invalid';
    }
    
    // Multiple donations in 1 hour
    $recent = Donation::where('donor_email', $data['donor_email'])
        ->where('created_at', '>', now()->subHour())
        ->count();
    if ($recent > 3) {
        $reasons[] = 'Terlalu banyak donasi';
    }
    
    return [
        'is_suspicious' => count($reasons) > 0,
        'reasons' => $reasons,
    ];
}
```

### **2. Menu Builder:**
```php
// Hierarchical menu with nested children
// Support multiple locations (header, footer, mobile)
// Auto-generate from pages or manual entry
```

### **3. Settings Management:**
```php
// Grouped settings (general, contact, social, appearance, donation)
// Type-aware (text, number, boolean, json, image)
// Easy helper: setting('contact_email')
```

### **4. Organization Chart:**
```php
// Hierarchical structure dengan parent-child
// Level-based (1=Top, 2=Board, 3=Division, 4=Staff)
// Visual rendering dengan D3.js atau CSS
```

---

## 🔒 **SECURITY REQUIREMENTS**

```yaml
Authentication:
  - Admin/Editor only (no public registration)
  - Laravel Breeze
  - Session-based
  - Optional 2FA

Authorization:
  - Role-based (admin, editor, super_admin)
  - Gates & Policies
  - Admin: full access
  - Editor: content only (no settings/users)

Data Protection:
  - CSRF protection (all forms)
  - XSS prevention (Blade auto-escape)
  - SQL injection prevention (Eloquent)
  - File upload validation (type, size, virus scan)

Payment Security:
  - No card storage
  - Image proof only
  - Manual verification for suspicious
  - Audit trail

Privacy:
  - Anonymous donation option
  - GDPR-ready
  - Data encryption at rest (optional)
```

---

## 📝 **SEEDER DATA REQUIRED**

### **1. Campaign Categories (6):**
```
- Pendidikan
- Kesehatan  
- Masjid
- Bencana
- Pangan
- Sosial
```

### **2. Blog Categories (6):**
```
- Berita Terkini
- Kegiatan & Event
- Inspirasi
- Laporan Program
- Pengumuman
- Tips & Tutorial
```

### **3. Static Pages (10):**
```
- Beranda (Homepage)
- Tentang Kami
- Visi & Misi
- Sejarah
- Struktur Organisasi
- Program Kami
- Kontak
- FAQ
- Laporan Keuangan
- Galeri
```

### **4. Organization Structure (Example):**
```
Level 1: Ketua Umum
Level 2: Wakil Ketua, Sekretaris, Bendahara
Level 3: Divisi Program, Fundraising, Pendidikan, Kesehatan, Ekonomi, Humas
```

### **5. Site Settings (Key):**
```
General:
- site_name: LazisMU Tamantirto
- site_tagline: Lembaga Amil Zakat Terpercaya
- site_logo, site_icon

Contact:
- contact_address, contact_phone, contact_email
- contact_whatsapp, contact_maps_url

Social Media:
- social_facebook, social_instagram
- social_youtube, social_twitter

Donation:
- bank_accounts (JSON array)
- qris_image
- min_donation: 10000
- auto_verify_threshold: 1000000
```

---

## 🚀 **DEVELOPMENT WORKFLOW**

### **Phase 1: Foundation (Week 1)**
```
1. Setup Laravel 12 + Flux UI + Livewire 3 Volt
2. Create all migrations (17 tables)
3. Create all models with relationships
4. Create seeders (run initial data)
5. Setup layouts (public + admin)
```

### **Phase 2: Donation System (Week 2-3)**
```
1. Campaign CRUD (admin)
2. Campaign list & detail (public)
3. Donation wizard (4 steps)
4. Auto-verification logic
5. Admin verification panel
6. Notification system
7. Tracking page
```

### **Phase 3: Company Profile (Week 3-4)**
```
1. Page CMS (admin)
2. Blog CRUD (admin)
3. Blog list & detail (public)
4. Organization structure (admin + public)
5. Team members (admin)
6. Media library
7. Menu builder
8. Settings page
```

### **Phase 4: Polish (Week 5-6)**
```
1. Homepage integration
2. Search functionality
3. SEO optimization
4. Performance tuning
5. Security hardening
6. Testing
7. Documentation
8. Deployment prep
```

---

## ✅ **DELIVERABLES CHECKLIST**

```
Code:
□ All 17 migrations
□ All models with relationships & scopes
□ All seeders with sample data
□ Services (Donation, Notification, Media)
□ Public controllers & views
□ Admin controllers & views
□ Livewire components
□ Blade components

Features:
□ Donation system (complete flow)
□ Auto-verification with manual override
□ Email + WhatsApp notifications
□ Campaign management
□ Blog/article system
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
□ Manual testing checklist

Documentation:
□ README.md (setup instructions)
□ .env.example (with all keys)
□ API documentation (if any)
□ Admin guide (basic usage)
□ Deployment guide
```

---

## 💡 **CODING STANDARDS**

```php
// Use Laravel conventions
✅ Eloquent ORM (no raw queries)
✅ Form Requests for validation
✅ Resource Controllers
✅ Service classes for business logic
✅ Blade components for reusable UI
✅ Enums for constants
✅ Scopes for common queries
✅ Accessors/Mutators for data formatting

// Code style
✅ PSR-12 compliant (use Pint)
✅ DocBlocks for complex methods
✅ Type hints (params & returns)
✅ Meaningful variable names

// Database
✅ Migrations (no manual DB changes)
✅ Seeders for initial data
✅ Indexes on foreign keys & search fields
✅ Soft deletes where appropriate

// Security
✅ Validate ALL inputs
✅ Authorize ALL actions
✅ Sanitize ALL outputs
✅ Log ALL critical operations
```

---

## 📤 **OUTPUT FORMAT**

When generating code, please provide:

1. **File path** (e.g., `app/Models/Campaign.php`)
2. **Complete code** (no truncation)
3. **Explanation** (what it does)
4. **Usage example** (if applicable)

Example:
```
File: app/Models/Campaign.php

[Complete code here]

Explanation: This model handles campaign data with relationships to category, images, and donations. It includes scopes for active campaigns and an accessor for progress percentage.

Usage:
Campaign::active()->featured()->get();
$campaign->progress_percentage; // Returns calculated percentage
```

---

## 🎯 **SPECIFIC INSTRUCTIONS FOR AI AGENT**

When you receive this prompt, please:

1. **Confirm understanding** of the project scope
2. **Ask clarifying questions** if anything is unclear
3. **Start with migrations** (foundation first)
4. **Generate models** with complete relationships
5. **Create seeders** with realistic data
6. **Build services** before controllers
7. **Implement critical features first** (donation flow)
8. **Test as you go** (provide test examples)
9. **Document edge cases** and assumptions
10. **Flag security concerns** immediately

---

## ❓ **QUESTIONS TO ANSWER**

Before starting, please confirm:

1. Should we use **Livewire 3** for all dynamic components or mix with Alpine.js?
2. For the organization chart, prefer **D3.js**, **Chart.js**, or **pure CSS**?
3. Rich text editor: **TinyMCE** or **Trix**?
4. Image optimization: **Intervention Image** or built-in?
5. Queue driver: **database** or **sync** for development?

---

## 🚀 **READY TO START?**

Reply with:
- ✅ Confirmation of understanding
- ❓ Any questions or clarifications needed
- 🎯 Which component you'll build first
- 📋 Any assumptions you're making

Let's build LazisMU Tamantirto! 🔥
