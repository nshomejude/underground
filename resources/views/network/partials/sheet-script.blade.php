<script>
    (function () {
        var panel = document.getElementById('nw-panel');
        var back = document.querySelector('.nw-backdrop');
        if (!panel) return;
        function set(on) {
            panel.classList.toggle('is-open', on);
            if (back) back.hidden = !on;
            document.body.classList.toggle('ac-lock', on);
            document.querySelectorAll('[data-nw-sheet]').forEach(function (b) { b.setAttribute('aria-expanded', on ? 'true' : 'false'); });
        }
        document.querySelectorAll('[data-nw-sheet]').forEach(function (b) { b.addEventListener('click', function () { set(!panel.classList.contains('is-open')); }); });
        document.querySelectorAll('[data-nw-sheet-close]').forEach(function (b) { b.addEventListener('click', function () { set(false); }); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') set(false); });
    })();
</script>
