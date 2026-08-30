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
