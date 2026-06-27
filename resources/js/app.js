import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

window.showNotification = function (message, type = 'success') {
    const toast = document.getElementById('notification-toast');
    const content = document.getElementById('notification-content');
    const icon = document.getElementById('notification-icon');
    const msgEl = document.getElementById('notification-message');

    if (!toast || !content) return;

    if (type === 'success') {
        content.className = 'w-72 p-4 border-l-4 rounded-lg shadow-lg flex items-start gap-2 bg-green-50 border-green-500 text-green-800';
        icon.textContent = 'check_circle';
        icon.className = 'material-symbols-outlined text-base text-green-600';
    } else {
        content.className = 'w-72 p-4 border-l-4 rounded-lg shadow-lg flex items-start gap-2 bg-red-50 border-red-500 text-red-800';
        icon.textContent = 'error';
        icon.className = 'material-symbols-outlined text-base text-red-600';
    }

    msgEl.textContent = message;
    toast.classList.remove('hidden');

    setTimeout(() => {
        toast.classList.add('hidden');
    }, 5000);

    const closeBtn = document.getElementById('notification-close');
    if (closeBtn) {
        closeBtn.onclick = function () {
            toast.classList.add('hidden');
        };
    }
};

document.addEventListener('DOMContentLoaded', function () {
    // Theme Switcher
    (function initTheme() {
        const stored = localStorage.getItem('theme') || 'system';
        applyTheme(stored);
        highlightActiveTheme(stored);

        document.querySelectorAll('.theme-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                const theme = this.dataset.theme;
                localStorage.setItem('theme', theme);
                applyTheme(theme);
                highlightActiveTheme(theme);
            });
        });

        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', () => {
            if (localStorage.getItem('theme') === 'system') applyTheme('system');
        });

        function applyTheme(theme) {
            const isDark = theme === 'dark' || (theme === 'system' && window.matchMedia('(prefers-color-scheme: dark)').matches);
            document.documentElement.classList.toggle('dark', isDark);
        }

        function highlightActiveTheme(theme) {
            document.querySelectorAll('.theme-btn').forEach(btn => {
                btn.classList.remove('text-yellow-500', 'bg-gray-200', 'dark:bg-gray-600');
                btn.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-200', 'dark:hover:bg-gray-600');
                if (btn.dataset.theme === theme) {
                    btn.classList.remove('text-gray-600', 'dark:text-gray-300');
                    btn.classList.add('text-yellow-500', 'bg-gray-200', 'dark:bg-gray-600');
                }
            });
        }
    })();

    // Sidebar Toggle
    (function initSidebar() {
        const sidebar = document.getElementById('sidebar');
        const toggle = document.getElementById('sidebar-toggle');
        const mainContent = document.getElementById('main-content');
        const headerWrapper = document.getElementById('header-wrapper');

        function setCollapsed(collapsed) {
            if (!sidebar || !mainContent || !headerWrapper) return;

            if (collapsed) {
                sidebar.classList.add('-translate-x-full');
                sidebar.classList.remove('md:translate-x-0');
                sidebar.classList.add('collapsed');
                mainContent.classList.remove('md:ml-[264px]');
                headerWrapper.classList.remove('md:left-[264px]', 'md:w-[calc(100%-264px)]');
            } else {
                sidebar.classList.remove('-translate-x-full');
                sidebar.classList.add('md:translate-x-0');
                sidebar.classList.remove('collapsed');
                mainContent.classList.add('md:ml-[264px]');
                headerWrapper.classList.add('md:left-[264px]', 'md:w-[calc(100%-264px)]');
            }
        }

        if (toggle && sidebar) {
            toggle.addEventListener('click', function () {
                const isDesktop = window.innerWidth >= 768;
                if (!isDesktop) {
                    sidebar.classList.toggle('-translate-x-full');
                    return;
                }
                const isCurrentlyCollapsed = !sidebar.classList.contains('md:translate-x-0');
                setCollapsed(!isCurrentlyCollapsed);
            });

            const mediaQuery = window.matchMedia('(min-width: 768px)');
            mediaQuery.addEventListener('change', function (e) {
                if (e.matches) {
                    sidebar.classList.remove('-translate-x-full');
                    sidebar.classList.add('md:translate-x-0');
                    mainContent.classList.add('md:ml-[264px]');
                    headerWrapper.classList.add('md:left-[264px]', 'md:w-[calc(100%-264px)]');
                } else {
                    sidebar.classList.add('-translate-x-full');
                    mainContent.classList.remove('md:ml-[264px]');
                    headerWrapper.classList.remove('md:left-[264px]', 'md:w-[calc(100%-264px)]');
                }
            });
        }
    })();

    // User Dropdown
    (function initDropdown() {
        const toggle = document.getElementById('dropdown-toggle');
        const menu = document.getElementById('dropdown-menu');

        if (toggle && menu) {
            toggle.addEventListener('click', function (e) {
                e.stopPropagation();
                menu.classList.toggle('hidden');
            });

            document.addEventListener('click', function () {
                menu.classList.add('hidden');
            });

            menu.addEventListener('click', function (e) {
                e.stopPropagation();
            });
        }
    })();

    // Sidebar Accordion & Active Page
    (function initSidebarNav() {
        function getRouteKey() {
            const path = window.location.pathname.replace(/\/+$/, '');
            const segments = path.split('/').filter(Boolean);
            return segments.pop() || 'dashboard';
        }

        const current = getRouteKey();

        document.querySelectorAll('.sidebar-link').forEach(link => {
            if (link.dataset.route === current) {
                link.classList.add('bg-gray-200', 'dark:bg-gray-700', 'dark:text-white', 'font-semibold');
                link.classList.remove('hover:bg-gray-100', 'dark:hover:bg-gray-700');
            }
        });

        document.querySelectorAll('.sidebar-menu-btn').forEach(btn => {
            const menu = btn.dataset.menu;
            const submenu = document.querySelector(`[data-submenu="${menu}"]`);

            if (submenu) {
                const hasActive = submenu.querySelector(`.sidebar-link[data-route="${current}"]`);
                if (hasActive) {
                    submenu.classList.remove('hidden');
                    btn.classList.add('bg-gray-200', 'dark:bg-gray-700', 'font-semibold', 'dark:text-white');
                    btn.querySelector('.material-symbols-outlined:last-child')?.classList.add('rotate-180');
                }

                btn.addEventListener('click', function () {
                    if (sidebar.classList.contains('collapsed')) return;
                    submenu.classList.toggle('hidden');
                    btn.querySelector('.sidebar-chevron')?.classList.toggle('rotate-180');
                });
            }
        });
    })();
});
