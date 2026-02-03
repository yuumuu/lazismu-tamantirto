import { createIcons, icons } from "lucide";
import { driver } from "driver.js";
import "driver.js/dist/driver.css";

document.addEventListener("alpine:init", () => {
    Alpine.store("ui", {
        // STATE
        sidebarOpen: JSON.parse(localStorage.getItem("ui.sidebarOpen")) ?? true,
        bottomBarOpen: JSON.parse(localStorage.getItem("ui.bottomBarOpen")) ?? true,

        // INIT
        init() {
            // Flux handles appearance automatically
        },

        // SIDEBAR ACTIONS
        toggleSidebarVisibility() {
            this.sidebarOpen = !this.sidebarOpen;
            localStorage.setItem("ui.sidebarOpen", JSON.stringify(this.sidebarOpen));
        },

        openSidebar() {
            this.sidebarOpen = true;
            localStorage.setItem("ui.sidebarOpen", "true");
        },

        closeSidebar() {
            this.sidebarOpen = false;
            localStorage.setItem("ui.sidebarOpen", "false");
        },

        // BOTTOM BAR ACTIONS
        toggleBottomBarVisibility() {
            this.bottomBarOpen = !this.bottomBarOpen;
            localStorage.setItem("ui.bottomBarOpen", JSON.stringify(this.bottomBarOpen));
        },

        openBottomBar() {
            this.bottomBarOpen = true;
            localStorage.setItem("ui.bottomBarOpen", "true");
        },

        closeBottomBar() {
            this.bottomBarOpen = false;
            localStorage.setItem("ui.bottomBarOpen", "false");
        },
    });

    // DriverJS Tutorial System with RBAC
    Alpine.store("tutorial", {
        driver: null,
        currentTour: null,
        userRole: null,
        completedTours: JSON.parse(localStorage.getItem("tutorial.completedTours")) ?? [],

        init() {
            this.driver = driver({
                showProgress: true,
                showButtons: ["next", "previous", "close"],
                nextBtnText: "Selanjutnya",
                prevBtnText: "Sebelumnya",
                doneBtnText: "Selesai",
                closeBtnText: "Tutup",
                progressText: "{{current}} dari {{total}}",
                onDestroyed: () => {
                    if (this.currentTour) {
                        this.markTourCompleted(this.currentTour);
                    }
                },
            });

            // Get user role from meta tag or global variable
            this.userRole = document.querySelector('meta[name="user-role"]')?.content || 'viewer';

            // Check if we need to start a tutorial after navigation
            this.checkForPendingTutorial();
        },

        // Check if there's a pending tutorial to start after navigation
        checkForPendingTutorial() {
            const pendingTutorial = sessionStorage.getItem('startTutorialAfterNavigation');
            if (pendingTutorial) {
                sessionStorage.removeItem('startTutorialAfterNavigation');
                
                // Wait a bit for the page to fully load
                setTimeout(() => {
                    this.startActualTour(pendingTutorial);
                }, 1000);
            }
        },

        // Start a tutorial tour based on user role
        startTour(tourName) {
            if (!this.canAccessTour(tourName)) {
                console.warn(`User role '${this.userRole}' cannot access tour '${tourName}'`);
                return;
            }

            if (this.isTourCompleted(tourName)) {
                if (!confirm("Anda sudah menyelesaikan tutorial ini. Ingin mengulanginya?")) {
                    return;
                }
            }

            // Check if user is on the correct page for the tutorial
            const requiredPage = this.getRequiredPageForTour(tourName);
            const currentPath = window.location.pathname;

            if (requiredPage && !this.isOnCorrectPage(currentPath, requiredPage)) {
                // Show navigation step first
                this.showNavigationStep(tourName, requiredPage);
                return;
            }

            // Start the actual tutorial
            this.startActualTour(tourName);
        },

        // Get the required page/route for a specific tour
        getRequiredPageForTour(tourName) {
            const tourPages = {
                'dashboard-overview': '/admin/dashboard',
                'campaign-management': '/admin/campaigns',
                'donation-verification': '/admin/donations',
                'user-management': '/admin/manage/users',
                'role-permission': '/admin/manage/roles',
                'settings-overview': '/settings/profile',
                'reports-overview': '/admin/reports',
            };

            return tourPages[tourName] || null;
        },

        // Check if user is on the correct page
        isOnCorrectPage(currentPath, requiredPage) {
            return currentPath === requiredPage;
        },

        // Show navigation step to guide user to correct page
        showNavigationStep(tourName, requiredPage) {
            const tourTitles = {
                'dashboard-overview': 'Dashboard',
                'campaign-management': 'Manajemen Campaign',
                'donation-verification': 'Verifikasi Donasi',
                'user-management': 'Manajemen User',
                'role-permission': 'Role & Permission',
                'settings-overview': 'Pengaturan Sistem',
                'reports-overview': 'Laporan & Analitik',
            };

            const tourTitle = tourTitles[tourName] || 'Tutorial';

            this.currentTour = tourName;
            this.driver.setSteps([
                {
                    popover: {
                        title: `Tutorial ${tourTitle}`,
                        description: `Untuk memulai tutorial ${tourTitle}, Anda perlu berada di halaman yang tepat. Klik tombol "Pergi ke Halaman" untuk diarahkan ke halaman yang sesuai.`,
                        showButtons: ['next', 'close'],
                        nextBtnText: 'Pergi ke Halaman',
                        onNextClick: () => {
                            this.driver.destroy();
                            // Navigate to the required page
                            if (window.Livewire) {
                                // Use Livewire navigation if available
                                window.Livewire.visit(requiredPage);
                            } else {
                                // Fallback to regular navigation
                                window.location.href = requiredPage;
                            }
                            
                            // Set a flag to start tutorial after navigation
                            sessionStorage.setItem('startTutorialAfterNavigation', tourName);
                        }
                    }
                }
            ]);
            this.driver.drive();
        },

        // Start the actual tutorial (without navigation step)
        startActualTour(tourName) {
            this.currentTour = tourName;
            const tour = this.getTourSteps(tourName);
            
            if (tour && tour.length > 0) {
                this.driver.setSteps(tour);
                this.driver.drive();
            }
        },

        // Check if user can access a specific tour based on role
        canAccessTour(tourName) {
            const tourPermissions = {
                'dashboard-overview': ['super_admin', 'admin', 'editor', 'viewer'],
                'campaign-management': ['super_admin', 'admin', 'editor'],
                'donation-verification': ['super_admin', 'admin'],
                'user-management': ['super_admin'],
                'role-permission': ['super_admin'],
                'settings-overview': ['super_admin', 'admin'],
                'reports-overview': ['super_admin', 'admin', 'editor'],
            };

            return tourPermissions[tourName]?.includes(this.userRole) ?? false;
        },

        // Get tour steps based on tour name
        getTourSteps(tourName) {
            const tours = {
                'dashboard-overview': [
                    {
                        element: '[data-tour="dashboard-stats"]',
                        popover: {
                            title: 'Statistik Dashboard',
                            description: 'Di sini Anda dapat melihat ringkasan statistik penting seperti total donasi, campaign aktif, dan lainnya.',
                            position: 'bottom'
                        }
                    },
                    {
                        element: '[data-tour="recent-donations"]',
                        popover: {
                            title: 'Donasi Terbaru',
                            description: 'Bagian ini menampilkan donasi-donasi terbaru yang masuk ke sistem.',
                            position: 'top'
                        }
                    },
                    {
                        element: '[data-tour="navigation-menu"]',
                        popover: {
                            title: 'Menu Navigasi',
                            description: 'Gunakan menu ini untuk mengakses berbagai fitur sistem sesuai dengan hak akses Anda.',
                            position: 'right'
                        }
                    }
                ],
                'campaign-management': [
                    {
                        element: '[data-tour="campaign-create"]',
                        popover: {
                            title: 'Buat Campaign Baru',
                            description: 'Klik tombol ini untuk membuat campaign penggalangan dana baru.',
                            position: 'bottom'
                        }
                    },
                    {
                        element: '[data-tour="campaign-filters"]',
                        popover: {
                            title: 'Filter Campaign',
                            description: 'Gunakan filter ini untuk mencari campaign berdasarkan kategori, status, atau kata kunci.',
                            position: 'bottom'
                        }
                    },
                    {
                        element: '[data-tour="campaign-table"]',
                        popover: {
                            title: 'Daftar Campaign',
                            description: 'Tabel ini menampilkan semua campaign dengan informasi target, terkumpul, dan progress.',
                            position: 'top'
                        }
                    }
                ],
                'donation-verification': [
                    {
                        element: '[data-tour="donation-status-filter"]',
                        popover: {
                            title: 'Filter Status Donasi',
                            description: 'Filter donasi berdasarkan status: Pending, Verified, atau Rejected.',
                            position: 'bottom'
                        }
                    },
                    {
                        element: '[data-tour="donation-verification-actions"]',
                        popover: {
                            title: 'Aksi Verifikasi',
                            description: 'Gunakan tombol ini untuk memverifikasi atau menolak donasi setelah memeriksa bukti transfer.',
                            position: 'left'
                        }
                    }
                ],
                'user-management': [
                    {
                        element: '[data-tour="user-create"]',
                        popover: {
                            title: 'Tambah User Baru',
                            description: 'Buat akun user baru dan tentukan role yang sesuai.',
                            position: 'bottom'
                        }
                    },
                    {
                        element: '[data-tour="user-roles"]',
                        popover: {
                            title: 'Manajemen Role',
                            description: 'Kelola role dan permission untuk mengatur hak akses user.',
                            position: 'bottom'
                        }
                    }
                ],
                'settings-overview': [
                    {
                        element: '[data-tour="profile-settings"]',
                        popover: {
                            title: 'Pengaturan Profil',
                            description: 'Update informasi profil dan password Anda di sini.',
                            position: 'bottom'
                        }
                    },
                    {
                        element: '[data-tour="two-factor-auth"]',
                        popover: {
                            title: 'Autentikasi Dua Faktor',
                            description: 'Aktifkan 2FA untuk keamanan akun yang lebih baik.',
                            position: 'bottom'
                        }
                    }
                ]
            };

            return tours[tourName] || [];
        },

        // Mark tour as completed
        markTourCompleted(tourName) {
            if (!this.completedTours.includes(tourName)) {
                this.completedTours.push(tourName);
                localStorage.setItem("tutorial.completedTours", JSON.stringify(this.completedTours));
            }
        },

        // Check if tour is completed
        isTourCompleted(tourName) {
            return this.completedTours.includes(tourName);
        },

        // Reset all completed tours (for testing)
        resetCompletedTours() {
            this.completedTours = [];
            localStorage.removeItem("tutorial.completedTours");
        },

        // Get available tours for current user role
        getAvailableTours() {
            const allTours = [
                { name: 'dashboard-overview', title: 'Pengenalan Dashboard', description: 'Pelajari fitur-fitur utama dashboard' },
                { name: 'campaign-management', title: 'Manajemen Campaign', description: 'Cara mengelola campaign penggalangan dana' },
                { name: 'donation-verification', title: 'Verifikasi Donasi', description: 'Proses verifikasi donasi yang masuk' },
                { name: 'user-management', title: 'Manajemen User', description: 'Kelola user dan hak akses' },
                { name: 'role-permission', title: 'Role & Permission', description: 'Atur role dan permission sistem' },
                { name: 'settings-overview', title: 'Pengaturan Sistem', description: 'Konfigurasi pengaturan aplikasi' },
                { name: 'reports-overview', title: 'Laporan & Analitik', description: 'Memahami laporan dan data analitik' }
            ];

            return allTours.filter(tour => this.canAccessTour(tour.name));
        }
    });
});

// Re-initialize icons after Livewire navigation
document.addEventListener("livewire:navigated", () => {
    createIcons({ icons });
    
    // Check for pending tutorials after Livewire navigation
    const tutorialStore = Alpine.store("tutorial");
    if (tutorialStore) {
        tutorialStore.checkForPendingTutorial();
    }
});

// Initialize icons on page load
createIcons({ icons });

// Global tutorial functions for easy access
window.startTutorial = (tourName) => {
    Alpine.store("tutorial").startTour(tourName);
};

window.showTutorialMenu = () => {
    const tutorials = Alpine.store("tutorial").getAvailableTours();
    
    if (tutorials.length === 0) {
        alert("Tidak ada tutorial yang tersedia untuk role Anda.");
        return;
    }

    let menuHtml = '<div class="tutorial-menu"><h3>Tutorial yang Tersedia:</h3><ul>';
    tutorials.forEach(tutorial => {
        const completed = Alpine.store("tutorial").isTourCompleted(tutorial.name) ? ' ✓' : '';
        menuHtml += `<li><button onclick="startTutorial('${tutorial.name}')">${tutorial.title}${completed}</button><p>${tutorial.description}</p></li>`;
    });
    menuHtml += '</ul></div>';

    // You can replace this with a proper modal implementation
    const modal = document.createElement('div');
    modal.innerHTML = `
        <div style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 10000; display: flex; align-items: center; justify-content: center;">
            <div style="background: white; padding: 2rem; border-radius: 8px; max-width: 500px; max-height: 80vh; overflow-y: auto;">
                ${menuHtml}
                <button onclick="this.closest('div').parentElement.remove()" style="margin-top: 1rem; padding: 0.5rem 1rem; background: #ef4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Tutup</button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
};