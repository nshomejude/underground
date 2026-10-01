// Registers every file under resources/images with Vite's build graph so
// Vite::asset() can resolve them from the manifest (see the Vite docs'
// "Other assets" guidance) without importing each one individually.
import.meta.glob(['../images/**']);

// Live character counter for long-text form fields. A field opts in with
// [data-char-counter] and [data-char-counter-min="<n>"]; the count renders
// into the element carrying [data-char-counter-output="<field-name>"].
// Progressive enhancement only — server-side validation is the source of
// truth, this is purely a typing aid.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-char-counter]').forEach((field) => {
        const min = Number.parseInt(field.getAttribute('data-char-counter-min') ?? '0', 10);
        const output = document.querySelector(`[data-char-counter-output="${field.name}"]`);

        if (!output) {
            return;
        }

        const update = () => {
            const length = field.value.length;
            output.textContent = length >= min ? `${length} characters` : `${length} / ${min} min`;
            output.classList.toggle('text-gold', length >= min);
            output.classList.toggle('text-muted', length < min);
        };

        field.addEventListener('input', update);
        update();
    });
});

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

// Admin light/dark theme toggle. The blocking script in x-admin.layout's
// <head> already applies a saved 'light' preference before paint; this
// only needs to handle the click and keep localStorage + the icon swap
// in sync. Absent entirely on public pages (no [data-theme-toggle] there).
document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.querySelector('[data-theme-toggle]');

    if (!toggle) {
        return;
    }

    const root = document.documentElement;

    const syncIcon = () => {
        const isLight = root.getAttribute('data-theme') === 'light';
        toggle.querySelector('[data-theme-icon="light"]')?.classList.toggle('hidden', isLight);
        toggle.querySelector('[data-theme-icon="dark"]')?.classList.toggle('hidden', !isLight);
    };

    syncIcon();

    toggle.addEventListener('click', () => {
        const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';

        if (next === 'light') {
            root.setAttribute('data-theme', 'light');
        } else {
            root.removeAttribute('data-theme');
        }

        try {
            localStorage.setItem('admin-theme', next);
        } catch (e) {}

        syncIcon();
    });
});

// Hero maxim slides: cross-fades through [data-hero-slide] every 7s, with
// [data-hero-dot] controls. Pauses on hover/focus and when the tab is hidden;
// stays on the first slide for visitors who prefer reduced motion.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-hero-slides]').forEach((root) => {
        const slides = [...root.querySelectorAll('[data-hero-slide]')];
        const dots = [...root.querySelectorAll('[data-hero-dot]')];

        if (slides.length < 2) {
            return;
        }

        let current = 0;
        let timer = null;

        const show = (index) => {
            current = (index + slides.length) % slides.length;

            slides.forEach((slide, i) => {
                const active = i === current;
                slide.classList.toggle('opacity-100', active);
                slide.classList.toggle('opacity-0', !active);
                slide.classList.toggle('pointer-events-none', !active);
                slide.setAttribute('aria-hidden', active ? 'false' : 'true');
            });

            dots.forEach((dot, i) => dot.setAttribute('aria-selected', i === current ? 'true' : 'false'));
        };

        const stop = () => {
            if (timer) {
                clearInterval(timer);
                timer = null;
            }
        };

        const start = () => {
            stop();

            if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                timer = setInterval(() => show(current + 1), 7000);
            }
        };

        dots.forEach((dot, i) => dot.addEventListener('click', () => {
            show(i);
            start();
        }));

        root.addEventListener('mouseenter', stop);
        root.addEventListener('mouseleave', start);
        root.addEventListener('focusin', stop);
        root.addEventListener('focusout', start);
        document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));

        start();
    });
});
