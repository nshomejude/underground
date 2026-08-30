// Mobile navigation drawer: toggled by [data-drawer-toggle="<id>"] buttons,
// closed by any [data-drawer-close] element inside the matching drawer, or Escape.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-drawer-toggle]').forEach((toggle) => {
        const targetId = toggle.getAttribute('data-drawer-toggle');
        const drawer = document.getElementById(targetId);

        if (!drawer) {
            return;
        }

        const open = () => {
            drawer.classList.remove('hidden');
            drawer.setAttribute('aria-hidden', 'false');
            toggle.setAttribute('aria-expanded', 'true');
            document.body.classList.add('overflow-hidden');
        };

        const close = () => {
            drawer.classList.add('hidden');
            drawer.setAttribute('aria-hidden', 'true');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.classList.remove('overflow-hidden');
        };

        toggle.addEventListener('click', open);

        drawer.querySelectorAll('[data-drawer-close]').forEach((closer) => {
            closer.addEventListener('click', close);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape') {
                close();
            }
        });
    });
});
