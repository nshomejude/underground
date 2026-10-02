<script>
    (function () {
        var nodes = document.querySelectorAll('[data-vt-countdown]');
        if (!nodes.length) { return; }
        var live = document.getElementById('vt-live-region');
        var pad = function (n) { return (n < 10 ? '0' : '') + n; };
        var plural = function (n, w) { return n + ' ' + w + (n === 1 ? '' : 's'); };
        var announced = false;

        function text(ms) {
            var s = Math.floor(ms / 1000);
            var d = Math.floor(s / 86400), h = Math.floor((s % 86400) / 3600), m = Math.floor((s % 3600) / 60);
            if (d > 0) { return plural(d, 'day') + ' ' + plural(h, 'hour') + ' left'; }
            if (h > 0) { return plural(h, 'hour') + ' ' + plural(m, 'minute') + ' left'; }
            return pad(m) + ':' + pad(s % 60) + ' left';
        }

        function tick() {
            var anyOpen = false;
            nodes.forEach(function (el) {
                var ms = new Date(el.getAttribute('data-vt-countdown')).getTime() - Date.now();
                if (ms > 0) { el.textContent = text(ms); anyOpen = true; return; }
                el.textContent = 'Voting has closed';
                if (el.hasAttribute('data-vt-reload') && !announced) {
                    announced = true;
                    if (live) { live.textContent = 'Voting has closed. Loading the result.'; }
                    window.setTimeout(function () { window.location.reload(); }, 2500);
                }
            });
            if (anyOpen) { window.setTimeout(tick, 1000); }
        }
        tick();
    })();
</script>
