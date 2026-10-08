/* SchoolHub UI — small vanilla enhancements */
(function () {
    'use strict';

    /* Sidebar (mobile off-canvas) ---------------------------------------- */
    function closeSidebar() {
        document.body.classList.remove('sidebar-open');
    }
    document.addEventListener('DOMContentLoaded', function () {
        var openBtn = document.querySelector('[data-ui="sidebar-open"]');
        var closeBtn = document.querySelector('[data-ui="sidebar-close"]');
        var backdrop = document.querySelector('.sidebar-backdrop');
        if (openBtn) openBtn.addEventListener('click', function () { document.body.classList.add('sidebar-open'); });
        if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
        if (backdrop) backdrop.addEventListener('click', closeSidebar);
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeSidebar(); });
    });

    /* Dark mode ----------------------------------------------------------- */
    document.addEventListener('DOMContentLoaded', function () {
        var toggle = document.getElementById('themeToggle');
        var icon = toggle ? toggle.querySelector('i') : null;

        var saved = null;
        try { saved = localStorage.getItem('ui_theme'); } catch (e) {}
        var dark = saved ? saved === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
        document.documentElement.classList.toggle('dark', dark);
        if (icon) icon.className = dark ? 'bi bi-sun-fill' : 'bi bi-moon-stars';

        if (toggle) toggle.addEventListener('click', function () {
            var isDark = !document.documentElement.classList.contains('dark');
            document.documentElement.classList.toggle('dark', isDark);
            try { localStorage.setItem('ui_theme', isDark ? 'dark' : 'light'); } catch (e) {}
            if (icon) icon.className = isDark ? 'bi bi-sun-fill' : 'bi bi-moon-stars';
        });
    });

    /* Auto-dismiss flash alerts ------------------------------------------- */
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.alert-dismissible').forEach(function (alert) {
            var btn = alert.querySelector('.btn-close');
            if (btn) btn.addEventListener('click', function () { alert.remove(); });
            setTimeout(function () {
                alert.style.transition = 'opacity .4s ease, transform .4s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-6px)';
                setTimeout(function () { alert.remove(); }, 420);
            }, 5000);
        });
    });

    /* Confirm destructive actions ------------------------------------------ */
    document.addEventListener('submit', function (e) {
        var form = e.target;
        if (form.matches && form.matches('form[data-confirm]')) {
            if (!window.confirm(form.getAttribute('data-confirm'))) e.preventDefault();
        }
    });
})();
