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

// Desktop header nav dropdowns ([data-nav-dropdown]): hover reveals the
// panel via CSS alone (group-hover:, no JS needed). This only adds click/tap
// toggling for touch devices that have no hover state, kept mutually
// exclusive — opening one dropdown closes the others — and closable via an
// outside click or Escape.
document.addEventListener('DOMContentLoaded', () => {
    const dropdowns = document.querySelectorAll('[data-nav-dropdown]');

    if (!dropdowns.length) {
        return;
    }

    const closeAll = (except) => {
        dropdowns.forEach((dropdown) => {
            if (dropdown === except) {
                return;
            }

            delete dropdown.dataset.navOpen;
            dropdown.querySelector('[data-nav-dropdown-toggle]')?.setAttribute('aria-expanded', 'false');
        });
    };

    dropdowns.forEach((dropdown) => {
        const toggle = dropdown.querySelector('[data-nav-dropdown-toggle]');

        if (!toggle) {
            return;
        }

        toggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const isOpen = dropdown.dataset.navOpen !== undefined;

            closeAll(dropdown);

            if (isOpen) {
                delete dropdown.dataset.navOpen;
                toggle.setAttribute('aria-expanded', 'false');
            } else {
                dropdown.dataset.navOpen = '';
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.addEventListener('click', () => closeAll());
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeAll();
        }
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

// Building-blocks animation: builds, holds, dismantles and rebuilds in a loop
// while the section is on screen. Static for visitors who prefer reduced motion.
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-power-blocks]').forEach((root) => {
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches || !('IntersectionObserver' in window)) {
            return;
        }

        const BUILD_MS = 4200; // blocks drop in and the glow starts
        const HOLD_MS = 4200; // finished pyramid on display
        const LEAVE_MS = 2200; // blocks lift away, capstone first
        let timers = [];
        let running = false;

        const later = (fn, ms) => timers.push(setTimeout(fn, ms));
        const clear = () => {
            timers.forEach(clearTimeout);
            timers = [];
        };

        const cycle = () => {
            root.classList.remove('is-leaving', 'is-playing');
            root.classList.add('is-armed');
            void root.offsetWidth; // restart the CSS animations from the hidden state
            root.classList.add('is-playing');

            later(() => {
                root.classList.remove('is-playing');
                root.classList.add('is-leaving');
            }, BUILD_MS + HOLD_MS);

            later(cycle, BUILD_MS + HOLD_MS + LEAVE_MS);
        };

        root.classList.add('is-armed');

        new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting && !running) {
                    running = true;
                    cycle();
                } else if (!entry.isIntersecting && running) {
                    running = false;
                    clear();
                }
            });
        }, { threshold: 0.25 }).observe(root);
    });
});

// Global Reach globe atlas: loaded on demand, only where the section exists.
document.addEventListener('DOMContentLoaded', () => {
    const root = document.querySelector('[data-globe]');

    if (root) {
        import('./globe.js').then((m) => m.initGlobe(root)).catch(() => {});
    }
});

// Language switcher (public site). EN is the authored language; choosing FR
// machine-translates the page with Google's website translator, which is only
// downloaded after a visitor picks FR (nothing third-party loads otherwise).
// The choice is stored in the standard `googtrans` cookie so it persists
// across pages. Admin pages have no [data-lang-switch], so they never translate.
document.addEventListener('DOMContentLoaded', () => {
    const switches = document.querySelectorAll('[data-lang-switch]');

    if (switches.length === 0) {
        return;
    }

    const COOKIE = 'googtrans';
    const host = window.location.hostname;
    const parent = host.replace(/^www\./, '');
    const isFrench = () => document.cookie.split('; ').some((c) => c === `${COOKIE}=/en/fr`);

    const writeCookie = (value) => {
        document.cookie = `${COOKIE}=${value}; path=/; max-age=31536000; samesite=lax`;
        if (host.includes('.')) {
            document.cookie = `${COOKIE}=${value}; path=/; max-age=31536000; samesite=lax; domain=.${parent}`;
        }
    };

    const clearCookie = () => {
        const expired = `${COOKIE}=; path=/; expires=Thu, 01 Jan 1970 00:00:00 GMT`;
        document.cookie = expired;
        document.cookie = `${expired}; domain=${host}`;
        document.cookie = `${expired}; domain=.${parent}`;
    };

    // The machine translator renders "Underground" as "souterrain". Wrap every
    // occurrence of the firm's name in a notranslate span before it runs so the
    // brand stays "Underground" in French.
    const protectBrandName = () => {
        const skip = new Set(['SCRIPT', 'STYLE', 'NOSCRIPT', 'TEXTAREA', 'INPUT', 'CODE', 'PRE']);
        const pattern = /\bUnderground\b/gi;
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
            acceptNode: (node) => {
                const parent = node.parentElement;

                if (!parent || skip.has(parent.tagName) || parent.closest('.notranslate, [translate="no"]')) {
                    return NodeFilter.FILTER_REJECT;
                }

                pattern.lastIndex = 0;

                return pattern.test(node.nodeValue) ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_REJECT;
            },
        });

        const nodes = [];
        while (walker.nextNode()) {
            nodes.push(walker.currentNode);
        }

        nodes.forEach((node) => {
            const fragment = document.createDocumentFragment();
            let last = 0;
            const text = node.nodeValue;

            // Keep one adjacent whitespace character inside the span: the
            // translator trims segment edges, which otherwise glues the brand
            // to the neighbouring word ("UndergroundNous sommes ...").
            pattern.lastIndex = 0;
            let match;

            while ((match = pattern.exec(text)) !== null) {
                let start = match.index;
                let end = match.index + match[0].length;

                if (start > last && /\s/.test(text[start - 1])) {
                    start -= 1;
                }

                if (end < text.length && /\s/.test(text[end])) {
                    end += 1;
                }

                fragment.appendChild(document.createTextNode(text.slice(last, start)));
                const span = document.createElement('span');
                span.className = 'notranslate';
                span.setAttribute('translate', 'no');
                span.textContent = text.slice(start, end);
                fragment.appendChild(span);
                last = end;
                pattern.lastIndex = end;
            }

            fragment.appendChild(document.createTextNode(text.slice(last)));
            node.parentNode.replaceChild(fragment, node);
        });
    };

    // Fixed French for a few short UI labels the machine translator gets wrong
    // ("Home" -> "Maison", "Apply" -> "Appliquer"). Only exact-match text inside
    // header/nav/footer chrome is touched, never body copy.
    const glossary = {
        Home: 'Accueil',
        Apply: 'Postuler',
        'Request an Executive Briefing': 'Demander un briefing exécutif',
    };

    const applyGlossary = () => {
        const walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT, {
            acceptNode: (node) => {
                const parent = node.parentElement;

                if (!parent || !parent.closest('header, nav, footer')) {
                    return NodeFilter.FILTER_REJECT;
                }

                return Object.prototype.hasOwnProperty.call(glossary, node.nodeValue.trim())
                    ? NodeFilter.FILTER_ACCEPT
                    : NodeFilter.FILTER_REJECT;
            },
        });

        const nodes = [];
        while (walker.nextNode()) {
            nodes.push(walker.currentNode);
        }

        nodes.forEach((node) => {
            const span = document.createElement('span');
            span.className = 'notranslate';
            span.setAttribute('translate', 'no');
            span.textContent = glossary[node.nodeValue.trim()];
            node.parentNode.replaceChild(span, node);
        });
    };

    // The <title> can't be wrapped in a span, so after the translator rewrites
    // it ("About — Underground Network" -> "À propos — Réseau souterrain") put
    // the original brand segment back, keeping the translated page name.
    const protectTitleBrand = () => {
        const titleEl = document.querySelector('title');
        const separator = /(\s[—|-]\s)/;
        const original = document.title.split(separator);
        const brandIndex = original.findIndex((part) => /Underground/i.test(part));

        if (!titleEl || brandIndex === -1) {
            return;
        }

        new MutationObserver(() => {
            const current = document.title.split(separator);

            if (current.length !== original.length || current[brandIndex] === original[brandIndex]) {
                return;
            }

            current[brandIndex] = original[brandIndex];
            document.title = current.join('');
        }).observe(titleEl, { childList: true, characterData: true, subtree: true });
    };

    const loadTranslator = () => {
        if (window.googleTranslateElementInit) {
            return;
        }

        protectBrandName();
        protectTitleBrand();
        applyGlossary();

        window.googleTranslateElementInit = () => {
            new window.google.translate.TranslateElement(
                { pageLanguage: 'en', includedLanguages: 'en,fr', autoDisplay: false },
                'google_translate_element',
            );
        };

        const script = document.createElement('script');
        script.src = 'https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit';
        script.async = true;
        document.head.appendChild(script);
    };

    const paint = () => {
        const fr = isFrench();
        document.documentElement.lang = fr ? 'fr' : 'en';

        switches.forEach((group) => {
            group.querySelectorAll('[data-lang]').forEach((link) => {
                const active = (link.dataset.lang === 'fr') === fr;
                link.classList.toggle('text-gold', active);
                link.classList.toggle('text-muted', !active);

                if (active) {
                    link.setAttribute('aria-current', 'true');
                } else {
                    link.removeAttribute('aria-current');
                }
            });
        });
    };

    switches.forEach((group) => {
        group.querySelectorAll('[data-lang]').forEach((link) => {
            link.addEventListener('click', (event) => {
                event.preventDefault();

                const wantsFrench = link.dataset.lang === 'fr';

                if (wantsFrench === isFrench()) {
                    return;
                }

                if (wantsFrench) {
                    writeCookie('/en/fr');
                } else {
                    clearCookie();
                }

                window.location.reload();
            });
        });
    });

    if (isFrench()) {
        loadTranslator();
    }

    paint();
});
