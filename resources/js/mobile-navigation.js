const header = document.querySelector('[data-site-header]');

if (header) {
    const toggle = header.querySelector('[data-menu-toggle]');
    const menu = header.querySelector('#site-navigation');
    const desktop = window.matchMedia('(min-width: 64rem)');
    const openIcon = toggle.querySelector('[data-menu-open-icon]');
    const closeIcon = toggle.querySelector('[data-menu-close-icon]');

    function setOpen(open, restoreFocus = false) {
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? toggle.dataset.closeLabel : toggle.dataset.openLabel);
        menu.classList.toggle('hidden', !open);
        openIcon.classList.toggle('hidden', open);
        closeIcon.classList.toggle('hidden', !open);
        if (restoreFocus) toggle.focus();
    }

    toggle.addEventListener('click', () => setOpen(toggle.getAttribute('aria-expanded') !== 'true'));
    menu.addEventListener('click', (event) => {
        if (event.target.closest('a') && !desktop.matches) setOpen(false);
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && toggle.getAttribute('aria-expanded') === 'true') setOpen(false, true);
    });
    document.addEventListener('click', (event) => {
        if (!header.contains(event.target)) setOpen(false);
    });
    desktop.addEventListener('change', () => setOpen(false));
}
