# Tutorial System dengan DriverJS

Sistem tutorial ini menggunakan DriverJS untuk memberikan panduan interaktif kepada pengguna berdasarkan role mereka.

## Fitur Utama

### 1. Role-Based Access Control (RBAC)
Tutorial yang tersedia disesuaikan dengan role pengguna:
- **Super Admin**: Akses ke semua tutorial
- **Admin**: Tutorial manajemen campaign, donasi, dan pengaturan
- **Editor**: Tutorial campaign dan laporan
- **Viewer**: Hanya tutorial dashboard

### 2. Tutorial yang Tersedia

#### Dashboard Overview (`dashboard-overview`)
- **Role**: Semua role
- **Elemen**: Statistik dashboard, donasi terbaru, menu navigasi
- **Tujuan**: Memperkenalkan fitur utama dashboard

#### Campaign Management (`campaign-management`)
- **Role**: Super Admin, Admin, Editor
- **Elemen**: Tombol buat campaign, filter, tabel campaign
- **Tujuan**: Panduan mengelola campaign penggalangan dana

#### Donation Verification (`donation-verification`)
- **Role**: Super Admin, Admin
- **Elemen**: Filter status, aksi verifikasi
- **Tujuan**: Proses verifikasi donasi yang masuk

#### User Management (`user-management`)
- **Role**: Super Admin only
- **Elemen**: Tombol tambah user, manajemen role
- **Tujuan**: Kelola user dan hak akses

#### Role & Permission (`role-permission`)
- **Role**: Super Admin only
- **Elemen**: Pengaturan role dan permission
- **Tujuan**: Kontrol akses granular

#### Settings Overview (`settings-overview`)
- **Role**: Super Admin, Admin
- **Elemen**: Pengaturan profil, 2FA
- **Tujuan**: Konfigurasi sistem dan keamanan

#### Reports Overview (`reports-overview`)
- **Role**: Super Admin, Admin, Editor
- **Elemen**: Laporan keuangan, analitik
- **Tujuan**: Memahami data dan laporan

## Cara Menggunakan

### 1. Akses Tutorial Menu
- **Desktop**: Klik tombol "Tutorial" di sidebar kiri bawah
- **Mobile**: Klik tombol "Tutorial" di header atas

### 2. Memulai Tutorial
1. Buka menu tutorial
2. Pilih tutorial yang ingin dipelajari
3. Klik tombol "Mulai"
4. **Navigasi Otomatis**: Jika Anda tidak berada di halaman yang tepat, sistem akan menampilkan langkah navigasi terlebih dahulu
5. Klik "Pergi ke Halaman" untuk diarahkan ke halaman yang sesuai
6. Tutorial akan dimulai otomatis setelah navigasi selesai
7. Ikuti panduan step-by-step

### 3. Navigasi Tutorial
- **Selanjutnya**: Lanjut ke step berikutnya
- **Sebelumnya**: Kembali ke step sebelumnya
- **Tutup**: Keluar dari tutorial
- **Progress**: Menampilkan posisi saat ini (misal: 2 dari 5)
- **Pergi ke Halaman**: Navigasi ke halaman yang diperlukan (jika tidak di halaman yang tepat)

## Implementasi Teknis

### 1. Navigasi Otomatis
Sistem tutorial akan memeriksa apakah user berada di halaman yang tepat:
```javascript
// Mapping tutorial ke halaman yang diperlukan
const tourPages = {
    'dashboard-overview': '/admin/dashboard',
    'campaign-management': '/admin/campaigns',
    'donation-verification': '/admin/donations',
    'user-management': '/admin/manage/users',
    'role-permission': '/admin/manage/roles',
    'settings-overview': '/settings/profile',
    'reports-overview': '/admin/reports',
};
```

Jika user tidak di halaman yang tepat, akan ditampilkan langkah navigasi:
- Menampilkan popup dengan tombol "Pergi ke Halaman"
- Menggunakan Livewire navigation untuk berpindah halaman
- Menyimpan tutorial yang akan dimulai di sessionStorage
- Memulai tutorial otomatis setelah navigasi selesai

### 2. JavaScript API
```javascript
// Memulai tutorial tertentu (dengan navigasi otomatis)
startTutorial('dashboard-overview');

// Menampilkan menu tutorial
showTutorialMenu();

// Mengecek tutorial yang sudah selesai
Alpine.store("tutorial").isTourCompleted('dashboard-overview');

// Reset semua tutorial (untuk testing)
Alpine.store("tutorial").resetCompletedTours();

// Memulai tutorial langsung tanpa cek navigasi (internal)
Alpine.store("tutorial").startActualTour('dashboard-overview');
```

### 3. Data Tour Attributes
Tambahkan atribut `data-tour` pada elemen HTML:
```html
<div data-tour="dashboard-stats">
    <!-- Konten statistik dashboard -->
</div>
```

### 4. Role Detection
Sistem otomatis mendeteksi role user dari:
- Meta tag: `<meta name="user-role" content="admin" />`
- JavaScript: `Alpine.store("tutorial").userRole`

### 5. Livewire Component
Tutorial menu menggunakan Livewire component:
```blade
<livewire:admin.tutorial-menu />
```

## Menambah Tutorial Baru

### 1. Update JavaScript (resources/js/app.js)
```javascript
// Tambah di tourPermissions
'tutorial-baru': ['super_admin', 'admin'],

// Tambah di tours object
'tutorial-baru': [
    {
        element: '[data-tour="element-target"]',
        popover: {
            title: 'Judul Step',
            description: 'Deskripsi step ini.',
            position: 'bottom'
        }
    }
]
```

### 2. Update Livewire Component
Tambah tutorial baru di array `$allTutorials` dalam `tutorial-menu.blade.php`.

### 3. Tambah Data Tour Attributes
Tambahkan `data-tour="element-target"` pada elemen HTML yang ingin dijelaskan.

## Kustomisasi

### 1. Styling
DriverJS menggunakan CSS dari `driver.js/dist/driver.css`. Untuk kustomisasi:
```css
.driver-popover {
    /* Custom styling */
}
```

### 2. Bahasa
Teks tutorial sudah dalam Bahasa Indonesia. Untuk mengubah:
```javascript
this.driver = driver({
    nextBtnText: "Lanjut",
    prevBtnText: "Kembali",
    doneBtnText: "Selesai",
    // ...
});
```

### 3. Posisi Popover
Tersedia posisi: `top`, `bottom`, `left`, `right`, `top-left`, `top-right`, dll.

## Troubleshooting

### Tutorial Tidak Muncul
1. Pastikan user memiliki role yang sesuai
2. Cek apakah elemen dengan `data-tour` ada di halaman
3. Pastikan JavaScript sudah di-build: `npm run build`

### Tutorial Tidak Dimulai Setelah Navigasi
**Gejala**: Setelah klik "Pergi ke Halaman", tutorial tidak dimulai otomatis
**Penyebab**: 
1. SessionStorage tidak tersimpan dengan benar
2. Livewire navigation gagal
3. Elemen tutorial belum ter-render

**Solusi**:
1. Pastikan browser mendukung sessionStorage
2. Cek console browser untuk error JavaScript
3. Tunggu beberapa detik untuk loading halaman
4. Refresh halaman dan coba lagi

### Navigasi Tutorial Salah Halaman
**Gejala**: Tutorial mengarahkan ke halaman yang salah
**Penyebab**: Mapping halaman di JavaScript tidak sesuai dengan route Laravel

**Solusi**:
1. Cek mapping `tourPages` di `resources/js/app.js`
2. Pastikan route Laravel sesuai dengan path di mapping
3. Update mapping jika ada perubahan route

### Modal Tutorial Tidak Bisa Ditutup
**Gejala**: Modal tutorial terbuka tapi tombol "Tutup" tidak berfungsi
**Penyebab**: Error JavaScript `$wire is not defined`
**Solusi**: 
1. Pastikan menggunakan `x-on:click` bukan `onclick` untuk Livewire calls
2. Gunakan `$wire.closeModal()` bukan `$wire.call('closeModal')`
3. Rebuild assets: `npm run build`

**Cara Menutup Modal**:
- Klik tombol "Tutup" di bagian bawah modal
- Tekan tombol ESC (otomatis dari Flux UI)
- Klik di luar area modal (otomatis dari Flux UI)
- Mulai tutorial (otomatis menutup modal)

### Elemen Tidak Ditemukan
1. Pastikan `data-tour` attribute sudah benar
2. Cek apakah elemen sudah ter-render saat tutorial dimulai
3. Untuk elemen dinamis, gunakan selector CSS alternatif

### Role Tidak Terdeteksi
1. Pastikan meta tag `user-role` ada di head
2. Cek apakah user sudah login dan memiliki role
3. Refresh halaman jika role baru saja diubah

## Best Practices

1. **Konsistensi**: Gunakan naming convention yang konsisten untuk `data-tour`
2. **Deskripsi Jelas**: Tulis deskripsi yang mudah dipahami
3. **Step Logis**: Urutkan step sesuai alur kerja natural
4. **Testing**: Test tutorial di berbagai role dan device
5. **Update**: Perbarui tutorial saat UI berubah