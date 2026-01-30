// LazisMU Tamantirto - Full Website System
// Platform: Laravel 11 + MySQL 8.0
// Feature: Donation System + Company Profile + Blog
// Copy & paste to: https://dbdiagram.io

Project LazisMU_Tamantirto {
  database_type: 'MySQL'
  Note: 'LazisMU Tamantirto - Donation + Company Profile System'
}

// ============================================================
// ENUMS
// ============================================================

Enum campaign_type {
  zakat
  infaq
  sedekah
  wakaf
  fidyah
}

Enum campaign_status {
  draft
  active
  completed
  paused
  cancelled
}

Enum donation_status {
  pending
  verified
  rejected
  expired
}

Enum payment_method {
  qris
  bank_transfer
  gopay
  manual
}

Enum user_role {
  admin
  editor
  super_admin
}

Enum page_status {
  draft
  published
}

Enum post_status {
  draft
  published
  scheduled
}

Enum menu_location {
  header
  footer
  mobile
}

// ============================================================
// CORE: DONATION SYSTEM (8 Tables)
// ============================================================

Table users {
  id bigint [pk, increment]
  name varchar(255) [not null]
  email varchar(255) [unique, not null]
  password varchar(255) [not null]
  role user_role [not null, default: 'admin']
  photo varchar(255) [null]
  bio text [null]
  email_verified_at timestamp [null]
  last_login_at timestamp [null]
  remember_token varchar(100) [null]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    email [unique]
    role
  }
  
  Note: 'Admin/Editor users untuk manage content'
}

Table campaign_categories {
  id bigint [pk, increment]
  name varchar(100) [unique, not null]
  slug varchar(100) [unique, not null]
  icon varchar(50) [not null]
  color varchar(7) [not null]
  description text [null]
  is_active boolean [not null, default: true]
  sort_order int [not null, default: 0]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    slug [unique]
    (is_active, sort_order)
  }
}

Table campaigns {
  id char(36) [pk, note: 'UUID']
  category_id bigint [not null, ref: > campaign_categories.id]
  created_by bigint [not null, ref: > users.id]
  type campaign_type [not null]
  title varchar(255) [not null]
  slug varchar(255) [unique, not null]
  short_description text [not null]
  description longtext [not null]
  target_amount decimal(12,2) [not null]
  current_amount decimal(12,2) [not null, default: 0]
  start_date date [not null]
  end_date date [not null]
  status campaign_status [not null, default: 'draft']
  featured_image varchar(255) [null]
  is_featured boolean [not null, default: false]
  is_urgent boolean [not null, default: false]
  priority int [not null, default: 0]
  published_at timestamp [null]
  deleted_at timestamp [null]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    slug [unique]
    (category_id, type, status)
    (is_featured, is_urgent)
    (published_at, status)
  }
}

Table campaign_images {
  id bigint [pk, increment]
  campaign_id char(36) [not null, ref: > campaigns.id]
  image_path varchar(255) [not null]
  caption varchar(255) [null]
  sort_order int [not null, default: 0]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    (campaign_id, sort_order)
  }
}

Table donations {
  id bigint [pk, increment]
  campaign_id char(36) [not null, ref: > campaigns.id]
  transaction_id varchar(20) [unique, not null]
  donor_name varchar(100) [not null]
  donor_email varchar(255) [not null]
  donor_phone varchar(20) [not null]
  amount decimal(12,2) [not null]
  donation_type campaign_type [not null]
  payment_method payment_method [not null]
  bank_name varchar(50) [null]
  account_number varchar(50) [null]
  status donation_status [not null, default: 'pending']
  proof_image varchar(255) [not null]
  donor_message text [null]
  is_anonymous boolean [not null, default: false]
  admin_fee decimal(12,2) [not null, default: 0]
  is_suspicious boolean [not null, default: false]
  suspicious_reason text [null]
  verified_at timestamp [null]
  verified_by bigint [null, ref: > users.id]
  verification_notes text [null]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    transaction_id [unique]
    (campaign_id, status)
    (donor_email, created_at)
    (status, is_suspicious)
  }
}

Table payment_logs {
  id bigint [pk, increment]
  donation_id bigint [unique, not null, ref: - donations.id]
  payment_method varchar(50) [not null]
  payment_channel varchar(50) [not null]
  amount decimal(12,2) [not null]
  status varchar(20) [not null, default: 'pending']
  metadata json [null]
  ip_address varchar(45) [null]
  user_agent text [null]
  paid_at timestamp [null]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    donation_id [unique]
  }
}

Table donation_notifications {
  id bigint [pk, increment]
  donation_id bigint [not null, ref: > donations.id]
  type varchar(20) [not null]
  recipient varchar(255) [not null]
  status varchar(20) [not null, default: 'pending']
  content text [not null]
  retry_count int [not null, default: 0]
  error_message text [null]
  sent_at timestamp [null]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    (donation_id, type, status)
  }
}

// ============================================================
// NEW: COMPANY PROFILE SYSTEM (8 Tables)
// ============================================================

Table pages {
  id bigint [pk, increment]
  title varchar(255) [not null]
  slug varchar(255) [unique, not null]
  content longtext [not null]
  excerpt text [null]
  featured_image varchar(255) [null]
  status page_status [not null, default: 'draft']
  template varchar(50) [null, note: 'default, about, contact, custom']
  meta_title varchar(255) [null]
  meta_description text [null]
  is_homepage boolean [not null, default: false]
  sort_order int [not null, default: 0]
  created_by bigint [not null, ref: > users.id]
  published_at timestamp [null]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    slug [unique]
    (status, published_at)
    is_homepage
  }
  
  Note: '''
  Static pages: About, Visi Misi, Sejarah, Kontak, dll
  Template: flexible untuk custom layout
  '''
}

Table blog_categories {
  id bigint [pk, increment]
  name varchar(100) [unique, not null]
  slug varchar(100) [unique, not null]
  description text [null]
  color varchar(7) [null]
  is_active boolean [not null, default: true]
  sort_order int [not null, default: 0]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    slug [unique]
    (is_active, sort_order)
  }
  
  Note: 'Kategori artikel: Berita, Kegiatan, Inspirasi, dll'
}

Table blog_posts {
  id bigint [pk, increment]
  category_id bigint [not null, ref: > blog_categories.id]
  title varchar(255) [not null]
  slug varchar(255) [unique, not null]
  content longtext [not null]
  excerpt text [not null]
  featured_image varchar(255) [null]
  status post_status [not null, default: 'draft']
  is_featured boolean [not null, default: false]
  view_count int [not null, default: 0]
  meta_title varchar(255) [null]
  meta_description text [null]
  author_id bigint [not null, ref: > users.id]
  published_at timestamp [null]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    slug [unique]
    (category_id, status)
    (status, published_at)
    (is_featured, published_at)
    view_count
  }
  
  Note: '''
  Blog/Artikel untuk berita, kegiatan, inspirasi
  SEO-ready dengan meta tags
  '''
}

Table organization_structure {
  id bigint [pk, increment]
  name varchar(255) [not null, note: 'Nama jabatan/posisi']
  level int [not null, note: '1=Ketua, 2=Wakil, 3=Divisi, dst']
  parent_id bigint [null, ref: > organization_structure.id]
  description text [null]
  responsibilities text [null, note: 'Tugas dan tanggung jawab']
  sort_order int [not null, default: 0]
  is_active boolean [not null, default: true]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    (parent_id, level, sort_order)
    is_active
  }
  
  Note: '''
  Struktur organisasi hierarki
  Level 1: Ketua Umum
  Level 2: Wakil, Sekretaris, Bendahara
  Level 3: Divisi (Program, Fundraising, dll)
  '''
}

Table team_members {
  id bigint [pk, increment]
  structure_id bigint [not null, ref: > organization_structure.id]
  name varchar(255) [not null]
  photo varchar(255) [null]
  email varchar(255) [null]
  phone varchar(20) [null]
  bio text [null]
  joined_date date [null]
  is_active boolean [not null, default: true]
  sort_order int [not null, default: 0]
  social_media json [null, note: 'Facebook, Instagram, LinkedIn']
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    (structure_id, is_active, sort_order)
  }
  
  Note: '''
  Orang-orang di struktur organisasi
  Bisa multiple people per position
  '''
}

Table media_library {
  id bigint [pk, increment]
  filename varchar(255) [not null]
  original_name varchar(255) [not null]
  file_path varchar(255) [not null]
  file_type varchar(50) [not null, note: 'image, video, document']
  mime_type varchar(100) [not null]
  file_size int [not null, note: 'in bytes']
  width int [null, note: 'for images']
  height int [null, note: 'for images']
  alt_text varchar(255) [null]
  caption text [null]
  uploaded_by bigint [not null, ref: > users.id]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    (file_type, created_at)
    uploaded_by
  }
  
  Note: '''
  Central media management
  Used by pages, posts, campaigns
  '''
}

Table menus {
  id bigint [pk, increment]
  name varchar(255) [not null]
  location menu_location [not null]
  parent_id bigint [null, ref: > menus.id]
  label varchar(100) [not null]
  url varchar(255) [null, note: 'External URL or leave null for internal']
  page_id bigint [null, ref: > pages.id]
  target varchar(20) [null, note: '_self or _blank']
  icon varchar(50) [null]
  sort_order int [not null, default: 0]
  is_active boolean [not null, default: true]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    (location, parent_id, sort_order)
    is_active
  }
  
  Note: '''
  Navigation management
  Support nested menu (parent-child)
  '''
}

Table settings {
  id bigint [pk, increment]
  key varchar(100) [unique, not null]
  value text [null]
  type varchar(50) [not null, note: 'text, number, boolean, json, image']
  group_name varchar(100) [not null, note: 'general, contact, social, appearance']
  label varchar(255) [not null]
  description text [null]
  is_public boolean [not null, default: false, note: 'Show in frontend']
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  updated_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    key [unique]
    (group_name, key)
    is_public
  }
  
  Note: '''
  Site settings: Logo, contact info, social media, etc
  Groups: general, contact, social, appearance, donation
  '''
}

// ============================================================
// AUDIT & TRACKING (1 Table)
// ============================================================

Table audit_logs {
  id bigint [pk, increment]
  user_id bigint [not null, ref: > users.id]
  action varchar(50) [not null]
  model varchar(100) [not null]
  model_id varchar(100) [not null]
  changes json [null]
  ip_address varchar(45) [null]
  user_agent text [null]
  created_at timestamp [not null, default: `CURRENT_TIMESTAMP`]
  
  Indexes {
    (user_id, created_at)
    (model, model_id)
    action
  }
}

// ============================================================
// EXAMPLE DATA STRUCTURES
// ============================================================

Note settings_examples {
  '''
  GENERAL SETTINGS:
  - site_name: "LazisMU Tamantirto"
  - site_tagline: "Lembaga Amil Zakat Terpercaya"
  - site_logo: "/storage/logo.png"
  - site_icon: "/favicon.ico"
  
  CONTACT SETTINGS:
  - contact_address: "Jl. Tamantirto..."
  - contact_phone: "0274-123456"
  - contact_email: "info@lazismutamantirto.org"
  - contact_whatsapp: "6281234567890"
  
  SOCIAL MEDIA:
  - social_facebook: "https://facebook.com/..."
  - social_instagram: "@lazismutamantirto"
  - social_youtube: "https://youtube.com/..."
  - social_twitter: "@lazismutamantirto"
  
  DONATION SETTINGS:
  - bank_accounts: [{"bank":"BCA","account":"1234567890"}]
  - qris_image: "/storage/qris.png"
  - min_donation: 10000
  - auto_verify_threshold: 1000000
  '''
}

Note organization_example {
  '''
  STRUKTUR ORGANISASI:
  
  Level 1 (Top):
  ├─ Ketua Umum
  
  Level 2 (Board):
  ├─ Wakil Ketua
  ├─ Sekretaris Umum
  └─ Bendahara Umum
  
  Level 3 (Divisions):
  ├─ Divisi Program
  ├─ Divisi Fundraising
  ├─ Divisi Pendidikan
  ├─ Divisi Kesehatan
  ├─ Divisi Ekonomi
  └─ Divisi Humas & Media
  
  Level 4 (Staff):
  └─ Staff per divisi
  '''
}

Note pages_examples {
  '''
  STATIC PAGES:
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
  '''
}

Note blog_examples {
  '''
  BLOG CATEGORIES:
  - Berita Terkini
  - Kegiatan & Event
  - Inspirasi
  - Laporan Program
  - Pengumuman
  - Tips & Tutorial
  '''
}

// ============================================================
// SUMMARY
// ============================================================

Note project_summary {
  '''
  LazisMU Tamantirto - Full Website System
  
  FEATURES:
  ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
  
  1. DONATION SYSTEM (8 tables)
     ✓ Campaign management
     ✓ Donation tracking
     ✓ Auto-verification
     ✓ Payment gateway
     ✓ Notifications
  
  2. COMPANY PROFILE (8 tables)
     ✓ Static pages (CMS)
     ✓ Blog/artikel system
     ✓ Organization structure
     ✓ Team members
     ✓ Media library
     ✓ Menu management
     ✓ Site settings
  
  3. SHARED SYSTEM (1 table)
     ✓ User management (admin/editor)
     ✓ Audit logs
  
  TOTAL: 17 Tables
  - 8 Donation tables
  - 8 Company Profile tables
  - 1 Audit table
  
  TECH STACK:
  - Laravel 11
  - Livewire 3 + Mary UI
  - Tailwind CSS v3
  - MySQL 8.0
  
  TIMELINE: 4-6 weeks
  BUDGET: Rp 2.000.000 (MVP)
  '''
}
