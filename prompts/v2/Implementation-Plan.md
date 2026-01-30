LazisMU Tamantirto - Implementation Plan
🎯 Goal: Build a full Donation Platform + Company Profile + Blog System for Lembaga Amil Zakat Muhammadiyah Tamantirto using Laravel 12, Livewire 3 Volt, and Flux UI.

User Review Required
IMPORTANT

The following decisions require your input before proceeding:

Database Engine: The prompt mentions MySQL 8.0 for production and SQLite for development. Should I configure for SQLite now and you'll switch to MySQL for production?

Spatie Permission Package: Need to install spatie/laravel-permission for RBAC. This is NOT in 
composer.json
 yet. Proceed with installation?

Rich Text Editor: The prompt mentions TinyMCE or Trix. Which one do you prefer for blog/page content?

Image Optimization: Should I install intervention/image for image processing?

Queue Driver: Use database queue for development (simpler setup)?

System Overview
Database - 20 Tables
Core Services
Admin Panel
Public Frontend
Homepage
Campaign List
Campaign Detail
Donation Wizard
Blog/Articles
Static Pages
Organization Chart
Dashboard
Campaign CRUD
Donation Verification
Blog CRUD
Page CMS
Settings
User Management
DonationService
NotificationService
MediaService
Users + RBAC
Campaigns + Categories
Donations + Logs
Blog + Categories
Pages + Menus
Organization + Team
Media + Settings + Audit
Proposed Changes
Phase 1: Foundation
[NEW] RBAC Package Installation
Install Spatie Permission via Composer:

composer require spatie/laravel-permission
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
[NEW] Enums (8 files)
[NEW] 
CampaignType.php
PHP 8.3+ backed enum for donation types: zakat, infaq, sedekah, wakaf, fidyah

[NEW] 
CampaignStatus.php
Campaign lifecycle: draft, active, completed, paused, cancelled

[NEW] 
DonationStatus.php
Donation lifecycle: pending, verified, rejected, expired, pending_manual

[NEW] 
PaymentMethod.php
Payment options: qris, bank_transfer, gopay, manual

[NEW] 
UserRole.php
User roles: super_admin, admin, editor, viewer

[NEW] 
PageStatus.php
Page states: draft, published

[NEW] 
PostStatus.php
Blog post states: draft, published, scheduled

[NEW] 
MenuLocation.php
Menu positions: header, footer, mobile

[NEW] Migrations (16 new files)
Based on the ERD in 
prompts/v1/lazismu-erd-fullsite.sql
:

Campaign System
Table	Description
campaign_categories	Categories like Pendidikan, Kesehatan, etc.
campaigns	UUID primary key, target/current amounts
campaign_images	Gallery for each campaign
Donation System
Table	Description
donations	Transaction records with verification status
payment_logs	Payment tracking metadata
donation_notifications	Email/WhatsApp queue
Company Profile
Table	Description
pages	CMS pages (About, Visi Misi, etc.)
blog_categories	Article categories
blog_posts	News, activities, inspiration
organization_structure	Hierarchical positions
team_members	People in organization
media_library	Central file storage
menus	Navigation management
settings	Site configuration
audit_logs	Activity tracking
[MODIFY] 
User.php
Add HasRoles trait from Spatie Permission
Add last_login_at, last_login_ip, is_active fields
Add helper methods: isSuperAdmin(), canVerifyDonation(), etc.
[NEW] Models (15 new files)
All models in app/Models/:

CampaignCategory.php
Campaign.php (UUID, soft deletes, relationships)
CampaignImage.php
Donation.php (auto-generated tracking code)
PaymentLog.php
DonationNotification.php
Page.php
BlogCategory.php
BlogPost.php
OrganizationStructure.php (self-referential hierarchy)
TeamMember.php
MediaLibrary.php
Menu.php (self-referential for nested menus)
Setting.php (key-value with typing)
AuditLog.php
[NEW] Seeders (8 files)
Seeder	Description
RolePermissionSeeder	40 permissions, 4 roles, default super admin
CampaignCategorySeeder	6 categories (Pendidikan, Kesehatan, etc.)
BlogCategorySeeder	6 categories (Berita, Kegiatan, etc.)
PageSeeder	10 static pages
MenuSeeder	Header, footer, mobile navigation
SettingSeeder	Site config (name, contact, social, donation)
OrganizationStructureSeeder	Sample hierarchy
DefaultUserSeeder	Super admin user
Phase 2: Services & Business Logic
[NEW] Services (5 files)
[NEW] 
DonationService.php
createFromRequest() - Process new donation
generateTrackingCode() - Unique transaction ID
checkSuspicious() - Auto-verification logic
calculateCampaignProgress() - Progress percentage
[NEW] 
DonationVerificationService.php
verify() - Mark as verified, update campaign total
reject() - Mark as rejected with reason
requiresManualReview() - Check if suspicious
[NEW] 
NotificationService.php
sendDonationConfirmation() - Email to donor
sendVerificationNotification() - After verification
sendWhatsAppNotification() - Via Fonnte API (optional)
[NEW] 
MediaService.php
upload() - Handle file uploads
optimize() - Image optimization
delete() - Remove files
[NEW] 
SettingService.php
get() - Get setting value
set() - Update setting
getGroup() - Get all settings in a group
[NEW] Form Requests (10 files)
All in app/Http/Requests/:

Campaign: StoreCampaignRequest, UpdateCampaignRequest
Donation: StoreDonationRequest, VerifyDonationRequest
Blog: StoreBlogPostRequest, UpdateBlogPostRequest
Page: StorePageRequest, UpdatePageRequest
Settings: UpdateSettingRequest
User: StoreUserRequest, UpdateUserRequest
[NEW] Policies (6 files)
All in app/Policies/:

CampaignPolicy.php - RBAC for campaigns
DonationPolicy.php - RBAC for donations
BlogPostPolicy.php - RBAC for blog
PagePolicy.php - RBAC for pages
UserPolicy.php - RBAC for user management
SettingPolicy.php - RBAC for settings
Phase 3: Admin Panel (Livewire Volt + Flux UI)
[NEW] Admin Layout
[NEW] 
admin.blade.php
Responsive sidebar with collapsible navigation
Top header with user dropdown
Dark/light mode toggle
Flux UI components integration
[NEW] Admin Volt Components (12 files)
All in resources/views/livewire/admin/:

Component	Description
dashboard.blade.php	Stats cards, charts, recent donations
campaigns/index.blade.php	Campaign list with filters
campaigns/create.blade.php	Create campaign form
campaigns/edit.blade.php	Edit campaign form
donations/index.blade.php	Donation list with status filters
donations/show.blade.php	Verification panel
blog/index.blade.php	Blog post list
blog/create.blade.php	Create post form
pages/index.blade.php	Page CMS list
settings/index.blade.php	Settings management
users/index.blade.php	User & role management
audit-logs/index.blade.php	Audit log viewer
Phase 4: Public Frontend (Orange Theme)
[NEW] Public Layout
[NEW] 
public.blade.php
Mobile-first responsive design
Orange color palette (#ea580c)
Bottom navigation for mobile
Sticky header
[NEW] Public Volt Components (10 files)
All in resources/views/livewire/:

Component	Description
home.blade.php	Hero, featured campaigns, stats
campaigns/index.blade.php	Campaign list with search/filter
campaigns/show.blade.php	Campaign detail with tabs
donations/create.blade.php	4-step donation wizard
donations/tracking.blade.php	Track donation status
blog/index.blade.php	Blog list
blog/show.blade.php	Blog post detail
pages/show.blade.php	Dynamic page render
organization.blade.php	Org chart visualization
zakat-calculator.blade.php	Zakat calculation tool
Verification Plan
Automated Tests
Feature Tests - Test all CRUD operations
php artisan test --filter=CampaignTest
php artisan test --filter=DonationTest
php artisan test --filter=RBACTest
Unit Tests - Test service layer logic
php artisan test --filter=DonationServiceTest
php artisan test --filter=VerificationServiceTest
Policy Tests - Verify RBAC works correctly
php artisan test --filter=PolicyTest
Manual Verification
Donation Flow

Create campaign → Submit donation → Verify auto-verification → Check tracking
RBAC Testing

Login as Editor → Verify cannot delete campaigns
Login as Admin → Verify can verify donations
Login as Super Admin → Verify full access
Browser Testing

Test donation wizard on mobile
Test admin panel responsive layout
Implementation Order
2026-01-07
2026-01-09
2026-01-11
2026-01-13
2026-01-15
2026-01-17
2026-01-19
2026-01-21
2026-01-23
2026-01-25
2026-01-27
2026-01-29
Spatie Permission
Enums
Migrations
Models
Seeders
Services
Form Requests
Policies
Admin Layout
Admin Components
Public Layout
Public Components
Testing
Polish
Phase 1
Phase 2
Phase 3
Phase 4
Phase 5
LazisMU Implementation Timeline
Ready to Proceed?
After you confirm:

Database engine choice (SQLite/MySQL)
Spatie Permission installation approval
Rich text editor choice (TinyMCE/Trix)
Image optimization package choice
I will begin implementation starting with Phase 1: Foundation.