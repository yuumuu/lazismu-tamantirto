import { createIcons, icons } from "lucide";

document.addEventListener("alpine:init", () => {
    Alpine.store("ui", {
        // STATE
        bottomBarOpen:
            JSON.parse(localStorage.getItem("ui.bottomBarOpen")) ?? true,

        // INIT
        init() {
            // Flux handles appearance automatically
        },

        // BOTTOM BAR ACTIONS
        toggleBottomBarVisibility() {
            this.bottomBarOpen = !this.bottomBarOpen;
            localStorage.setItem(
                "ui.bottomBarOpen",
                JSON.stringify(this.bottomBarOpen)
            );
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
});

// Re-initialize icons after Livewire navigation
document.addEventListener("livewire:navigated", () => {
    createIcons({ icons });
});

createIcons({ icons });