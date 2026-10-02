// Interactive HD globe atlas for the Global Reach page.
// Canvas 2D + d3-geo (orthographic). Loaded on demand only on pages that
// contain [data-globe]; map data is bundled (no third-party requests).
import { geoDistance, geoGraticule10, geoInterpolate, geoOrthographic, geoPath } from 'd3-geo';
import { feature, mesh } from 'topojson-client';
import atlas110 from 'world-atlas/countries-110m.json?url';
import atlas50 from 'world-atlas/countries-50m.json?url';

const GOLD = '#C9A25A';
const GOLD2 = '#E0BE7E';

export function initGlobe(root) {
    const canvas = root.querySelector('[data-globe-canvas]');
    const stage = root.querySelector('[data-globe-stage]');
    const card = root.querySelector('[data-globe-card]');
    const list = root.querySelector('[data-globe-list]');
    const pauseBtn = root.querySelector('[data-globe-pause]');
    const resetBtn = root.querySelector('[data-globe-reset]');

    if (!canvas || !stage) {
        return;
    }

    let OFFICES = [];
    try {
        OFFICES = JSON.parse(root.dataset.offices || '[]');
    } catch (e) {
        OFFICES = [];
    }

    const LINKS = JSON.parse(root.dataset.links || '[]');
    const byId = Object.fromEntries(OFFICES.map((o) => [o.id, o]));
    const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    const ctx = canvas.getContext('2d');
    let W = 0;
    let H = 0;
    let dpr = 1;
    let R = 0;
    const HOME = { rot: [20, -18], zoom: 1 };
    let rot = HOME.rot.slice();
    let zoom = HOME.zoom;
    let paused = reduce;
    let tween = null;
    let dragging = false;
    let selected = null;
    let lastT = 0;
    let interacting = 0;
    let running = false;
    let onScreen = true;
    let land = null;
    let landLo = null;
    let borders = null;
    let bordersLo = null;
    const sphere = { type: 'Sphere' };
    const grat = geoGraticule10();
    const proj = geoOrthographic().clipAngle(90).precision(0.4);
    const path = geoPath(proj, ctx);

    const stars = Array.from({ length: 160 }, () => ({
        x: Math.random(), y: Math.random(), r: Math.random() * 1.1 + 0.2, a: Math.random() * 0.5 + 0.2, p: Math.random() * 6,
    }));

    let pat = null;
    function makePattern() {
        const s = 6 * dpr;
        const p = document.createElement('canvas');
        p.width = s;
        p.height = s;
        const c = p.getContext('2d');
        c.fillStyle = 'rgba(201,162,90,.38)';
        c.beginPath();
        c.arc(s / 2, s / 2, 0.9 * dpr, 0, 7);
        c.fill();
        c.strokeStyle = 'rgba(201,162,90,.12)';
        c.lineWidth = dpr * 0.6;
        c.beginPath();
        c.moveTo(0, s);
        c.lineTo(s, 0);
        c.stroke();
        pat = ctx.createPattern(p, 'repeat');
    }

    function resize() {
        dpr = Math.min(window.devicePixelRatio || 1, 3);
        const r = stage.getBoundingClientRect();
        W = r.width;
        H = r.height;
        canvas.width = Math.round(W * dpr);
        canvas.height = Math.round(H * dpr);
        ctx.setTransform(dpr, 0, 0, dpr, 0, 0);
        R = Math.min(W, H) * 0.4;
        makePattern();
    }

    function setProj() {
        proj.translate([W / 2, H / 2]).scale(R * zoom).rotate([rot[0], rot[1], 0]);
    }

    const center = () => [-rot[0], -rot[1]];
    const visible = (ll) => geoDistance(ll, center()) < Math.PI / 2 - 0.001;

    function drawBackground(t) {
        ctx.fillStyle = '#0B0B0C';
        ctx.fillRect(0, 0, W, H);
        for (const s of stars) {
            const a = s.a * (reduce ? 1 : 0.7 + 0.3 * Math.sin(t / 1200 + s.p));
            ctx.fillStyle = 'rgba(243,239,230,' + a + ')';
            ctx.beginPath();
            ctx.arc(s.x * W, s.y * H, s.r, 0, 7);
            ctx.fill();
        }
    }

    function drawGlobe() {
        const cx = W / 2;
        const cy = H / 2;
        const r = R * zoom;
        let g = ctx.createRadialGradient(cx, cy, r * 0.98, cx, cy, r * 1.28);
        g.addColorStop(0, 'rgba(224,190,126,.34)');
        g.addColorStop(0.35, 'rgba(201,162,90,.1)');
        g.addColorStop(1, 'rgba(201,162,90,0)');
        ctx.fillStyle = g;
        ctx.beginPath();
        ctx.arc(cx, cy, r * 1.28, 0, 7);
        ctx.fill();

        g = ctx.createRadialGradient(cx - r * 0.3, cy - r * 0.35, r * 0.1, cx, cy, r);
        g.addColorStop(0, '#1b1a21');
        g.addColorStop(1, '#0d0d10');
        ctx.fillStyle = g;
        ctx.beginPath();
        path(sphere);
        ctx.fill();

        ctx.strokeStyle = 'rgba(201,162,90,.10)';
        ctx.lineWidth = 0.6;
        ctx.beginPath();
        path(grat);
        ctx.stroke();

        const lo = interacting > 0 && landLo;
        const L = lo ? landLo : land;
        const B = lo ? bordersLo : borders;
        if (L) {
            ctx.beginPath();
            path(L);
            ctx.fillStyle = pat || 'rgba(201,162,90,.2)';
            ctx.fill();
            ctx.fillStyle = 'rgba(201,162,90,.07)';
            ctx.fill();
            if (B) {
                ctx.beginPath();
                path(B);
                ctx.strokeStyle = 'rgba(201,162,90,.28)';
                ctx.lineWidth = 0.5;
                ctx.stroke();
            }
            ctx.beginPath();
            path(L);
            ctx.strokeStyle = GOLD;
            ctx.lineWidth = 0.9;
            ctx.stroke();
        }

        g = ctx.createRadialGradient(cx - r * 0.45, cy - r * 0.4, r * 0.2, cx, cy, r * 1.02);
        g.addColorStop(0, 'rgba(0,0,0,0)');
        g.addColorStop(0.6, 'rgba(0,0,0,.15)');
        g.addColorStop(1, 'rgba(0,0,0,.72)');
        ctx.fillStyle = g;
        ctx.beginPath();
        path(sphere);
        ctx.fill();

        ctx.beginPath();
        path(sphere);
        ctx.strokeStyle = 'rgba(224,190,126,.75)';
        ctx.lineWidth = 1.4;
        ctx.stroke();
    }

    const arcs = LINKS.map(([a, b], i) => ({
        a: byId[a],
        b: byId[b],
        it: geoInterpolate(byId[a].c, byId[b].c),
        d: geoDistance(byId[a].c, byId[b].c),
        ph: i * 0.17,
        dir: i % 2 ? 1 : -1,
    }));

    function lift(ll, h) {
        const p = proj(ll);
        if (!p) return null;
        const cx = W / 2;
        const cy = H / 2;
        const k = 1 + h;
        return [cx + (p[0] - cx) * k, cy + (p[1] - cy) * k];
    }

    function arcPoint(A, u) {
        const ll = A.it(u);
        const h = 0.22 * A.d * Math.sin(Math.PI * u);
        return { ll, p: lift(ll, h), vis: visible(ll) };
    }

    function drawArcs(t) {
        const N = 48;
        for (const A of arcs) {
            let seg = [];
            const flush = () => {
                if (seg.length > 1) {
                    ctx.beginPath();
                    ctx.moveTo(seg[0][0], seg[0][1]);
                    for (let i = 1; i < seg.length; i++) ctx.lineTo(seg[i][0], seg[i][1]);
                    ctx.strokeStyle = 'rgba(224,190,126,.9)';
                    ctx.lineWidth = 1.4;
                    ctx.shadowColor = GOLD2;
                    ctx.shadowBlur = 8;
                    ctx.stroke();
                    ctx.shadowBlur = 0;
                }
                seg = [];
            };
            for (let i = 0; i <= N; i++) {
                const q = arcPoint(A, i / N);
                if (q.vis && q.p) seg.push(q.p);
                else flush();
            }
            flush();

            if (!reduce) {
                const base = (((t / 1000) * (0.11 / Math.max(A.d, 0.2)) * 0.5) + A.ph) % 1;
                for (let k = 0; k < 2; k++) {
                    const u0 = (base + k * 0.5) % 1;
                    const u = A.dir > 0 ? u0 : 1 - u0;
                    for (let tr = 0; tr < 10; tr++) {
                        const uu = u - A.dir * tr * 0.008;
                        if (uu < 0 || uu > 1) continue;
                        const q = arcPoint(A, uu);
                        if (!q.vis || !q.p) continue;
                        const f = 1 - tr / 10;
                        ctx.fillStyle = tr ? 'rgba(224,190,126,' + 0.5 * f + ')' : '#fff3d2';
                        ctx.shadowColor = GOLD2;
                        ctx.shadowBlur = tr ? 0 : 14;
                        ctx.beginPath();
                        ctx.arc(q.p[0], q.p[1], tr ? 2.2 * f : 3.4, 0, 7);
                        ctx.fill();
                        ctx.shadowBlur = 0;
                    }
                }
            }
        }
    }

    const hits = [];
    function roundRect(x, y, w, h, r) {
        if (ctx.roundRect) {
            ctx.roundRect(x, y, w, h, r);
        } else {
            ctx.rect(x, y, w, h);
        }
    }

    function drawOffices(t) {
        hits.length = 0;
        const small = W < 520;
        ctx.font = '600 ' + (small ? 11 : 13) + 'px "Instrument Sans", system-ui, sans-serif';
        ctx.textBaseline = 'middle';
        for (const o of OFFICES) {
            if (!visible(o.c)) continue;
            const p = proj(o.c);
            if (!p) continue;
            hits.push({ o, x: p[0], y: p[1] });
            const sel = selected === o.id;
            const base = o.hq ? 6 : 4.5;
            if (!reduce) {
                for (let k = 0; k < 2; k++) {
                    const u = ((t / 1800) + k * 0.5 + OFFICES.indexOf(o) * 0.13) % 1;
                    ctx.strokeStyle = 'rgba(224,190,126,' + 0.7 * (1 - u) + ')';
                    ctx.lineWidth = 1.5;
                    ctx.beginPath();
                    ctx.arc(p[0], p[1], base + u * 22, 0, 7);
                    ctx.stroke();
                }
            } else {
                ctx.strokeStyle = 'rgba(224,190,126,.5)';
                ctx.beginPath();
                ctx.arc(p[0], p[1], base + 8, 0, 7);
                ctx.stroke();
            }
            ctx.fillStyle = o.hq ? GOLD2 : GOLD;
            ctx.shadowColor = GOLD2;
            ctx.shadowBlur = 12;
            ctx.beginPath();
            ctx.arc(p[0], p[1], base + (sel ? 2 : 0), 0, 7);
            ctx.fill();
            ctx.shadowBlur = 0;
            ctx.strokeStyle = '#0B0B0C';
            ctx.lineWidth = 1.5;
            ctx.stroke();

            const lbl = o.name + (o.hq ? '  (HQ)' : '');
            const tw = ctx.measureText(lbl).width;
            let lx = p[0] + base + 10;
            let ly = p[1] - base - 6;
            if (o.lp === 'bl') {
                lx = p[0] - base - 10 - tw;
                ly = p[1] + base + 14;
            } else if (o.lp === 'br') {
                lx = p[0] + base + 10;
                ly = p[1] + base + 16;
            } else if (o.lp === 'tc') {
                lx = p[0] - tw / 2;
                ly = p[1] - base - 22;
            }
            ctx.fillStyle = 'rgba(11,11,12,.88)';
            ctx.strokeStyle = sel ? GOLD2 : 'rgba(201,162,90,.6)';
            ctx.lineWidth = 1;
            ctx.beginPath();
            roundRect(lx - 6, ly - 11, tw + 12, 22, 6);
            ctx.fill();
            ctx.stroke();
            ctx.fillStyle = '#F3EFE6';
            ctx.fillText(lbl, lx, ly);
        }
    }

    function frame(t) {
        if (!running) return;
        const dt = Math.min(50, t - (lastT || t));
        lastT = t;
        if (tween) {
            const k = Math.min(1, (t - tween.t0) / tween.dur);
            const e = k < 0.5 ? 4 * k * k * k : 1 - Math.pow(-2 * k + 2, 3) / 2;
            rot = [tween.f[0] + (tween.to[0] - tween.f[0]) * e, tween.f[1] + (tween.to[1] - tween.f[1]) * e];
            zoom = tween.fz + (tween.tz - tween.fz) * e;
            if (k >= 1) tween = null;
        } else if (!paused && !dragging && !selected) {
            rot[0] += dt * 0.006;
        }
        if (interacting > 0) interacting -= dt;
        setProj();
        drawBackground(t);
        drawGlobe();
        drawArcs(t);
        drawOffices(t);
        requestAnimationFrame(frame);
    }

    function start() {
        if (running || !onScreen || document.hidden) return;
        running = true;
        lastT = 0;
        requestAnimationFrame(frame);
    }

    function stop() {
        running = false;
    }

    function flyTo(ll, z) {
        const tx = -ll[0];
        const ty = -ll[1];
        const dx = ((tx - rot[0] + 540) % 360) - 180;
        tween = { f: rot.slice(), to: [rot[0] + dx, ty], fz: zoom, tz: z || 1.35, t0: performance.now(), dur: reduce ? 1 : 1400 };
    }

    function fmt(n, pos, neg) {
        return Math.abs(n).toFixed(2) + '° ' + (n >= 0 ? pos : neg);
    }

    function select(id) {
        selected = id;
        const o = byId[id];
        flyTo(o.c, 1.45);
        if (card) {
            card.hidden = false;
            card.innerHTML = '';
            const close = document.createElement('button');
            close.type = 'button';
            close.className = 'gl-x';
            close.setAttribute('aria-label', 'Close office card');
            close.textContent = '×';
            close.addEventListener('click', clearSel);
            const tag = document.createElement('div');
            tag.className = 'gl-tag';
            tag.textContent = o.tag;
            const h = document.createElement('h3');
            h.textContent = o.name;
            const p = document.createElement('p');
            p.textContent = o.txt;
            const co = document.createElement('p');
            co.className = 'gl-tag';
            co.textContent = fmt(o.c[1], 'N', 'S') + ', ' + fmt(o.c[0], 'E', 'W');
            card.append(close, tag, h, p, co);
        }
        root.querySelectorAll('[data-globe-list] button').forEach((b) => b.setAttribute('aria-pressed', b.dataset.id === id ? 'true' : 'false'));
    }

    function clearSel() {
        selected = null;
        if (card) card.hidden = true;
        root.querySelectorAll('[data-globe-list] button').forEach((b) => b.setAttribute('aria-pressed', 'false'));
    }

    if (list) {
        list.querySelectorAll('button[data-id]').forEach((b) => b.addEventListener('click', () => select(b.dataset.id)));
    }

    if (pauseBtn) {
        if (reduce) {
            pauseBtn.textContent = 'Rotation off (reduced motion)';
            pauseBtn.disabled = true;
            pauseBtn.setAttribute('aria-pressed', 'true');
        }
        pauseBtn.addEventListener('click', () => {
            paused = !paused;
            pauseBtn.textContent = paused ? 'Resume rotation' : 'Pause rotation';
            pauseBtn.setAttribute('aria-pressed', paused ? 'true' : 'false');
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            clearSel();
            const dx = ((HOME.rot[0] - rot[0] + 540) % 360) - 180;
            tween = { f: rot.slice(), to: [rot[0] + dx, HOME.rot[1]], fz: zoom, tz: 1, t0: performance.now(), dur: reduce ? 1 : 1200 };
        });
    }

    // pointer interaction
    const ptrs = new Map();
    let down = null;
    let pinch0 = 0;
    let z0 = 1;

    canvas.addEventListener('pointerdown', (e) => {
        canvas.setPointerCapture(e.pointerId);
        ptrs.set(e.pointerId, [e.clientX, e.clientY]);
        tween = null;
        dragging = true;
        down = { x: e.clientX, y: e.clientY, moved: 0 };
        if (ptrs.size === 2) {
            const [a, b] = [...ptrs.values()];
            pinch0 = Math.hypot(a[0] - b[0], a[1] - b[1]);
            z0 = zoom;
        }
    });

    canvas.addEventListener('pointermove', (e) => {
        if (!ptrs.has(e.pointerId)) return;
        const p = ptrs.get(e.pointerId);
        if (ptrs.size === 2) {
            ptrs.set(e.pointerId, [e.clientX, e.clientY]);
            const [a, b] = [...ptrs.values()];
            zoom = Math.max(0.7, Math.min(6, z0 * Math.hypot(a[0] - b[0], a[1] - b[1]) / pinch0));
            interacting = 200;
            return;
        }
        const dx = e.clientX - p[0];
        const dy = e.clientY - p[1];
        ptrs.set(e.pointerId, [e.clientX, e.clientY]);
        if (down) down.moved += Math.abs(dx) + Math.abs(dy);
        const k = 0.28 / zoom;
        rot[0] += dx * k;
        rot[1] = Math.max(-85, Math.min(85, rot[1] - dy * k));
        interacting = 200;
    });

    function up(e) {
        const wasClick = down && down.moved < 6 && ptrs.size === 1;
        ptrs.delete(e.pointerId);
        if (!ptrs.size) dragging = false;
        if (wasClick) {
            const r = canvas.getBoundingClientRect();
            const x = e.clientX - r.left;
            const y = e.clientY - r.top;
            let best = null;
            let bd = 22;
            for (const h of hits) {
                const d = Math.hypot(h.x - x, h.y - y);
                if (d < bd) {
                    bd = d;
                    best = h;
                }
            }
            if (best) select(best.o.id);
        }
    }

    canvas.addEventListener('pointerup', up);
    canvas.addEventListener('pointercancel', up);

    // wheel zoom only while the pointer is over the globe; never trap page scroll on touch
    canvas.addEventListener('wheel', (e) => {
        e.preventDefault();
        tween = null;
        zoom = Math.max(0.7, Math.min(6, zoom * Math.exp(-e.deltaY * 0.0015)));
        interacting = 200;
    }, { passive: false });

    canvas.addEventListener('keydown', (e) => {
        const s = 8 / zoom;
        let handled = true;
        tween = null;
        if (e.key === 'ArrowLeft') rot[0] += s;
        else if (e.key === 'ArrowRight') rot[0] -= s;
        else if (e.key === 'ArrowUp') rot[1] = Math.min(85, rot[1] + s);
        else if (e.key === 'ArrowDown') rot[1] = Math.max(-85, rot[1] - s);
        else if (e.key === '+' || e.key === '=') zoom = Math.min(6, zoom * 1.15);
        else if (e.key === '-' || e.key === '_') zoom = Math.max(0.7, zoom / 1.15);
        else if (e.key === 'Escape') clearSel();
        else handled = false;
        if (handled) {
            e.preventDefault();
            interacting = 200;
        }
    });

    window.addEventListener('resize', resize);
    document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
    if ('IntersectionObserver' in window) {
        new IntersectionObserver((entries) => {
            onScreen = entries.some((en) => en.isIntersecting);
            if (onScreen) start();
            else stop();
        }, { threshold: 0.05 }).observe(stage);
    }

    // data: low-res first, then the high-definition atlas
    const build = (w) => ({ land: feature(w, w.objects.land), borders: mesh(w, w.objects.countries, (a, b) => a !== b) });
    const load = (url) => fetch(url).then((r) => {
        if (!r.ok) throw new Error(String(r.status));
        return r.json();
    });

    resize();
    start();
    root.classList.add('is-live');

    load(atlas110).then((w) => {
        const d = build(w);
        landLo = d.land;
        bordersLo = d.borders;
        if (!land) {
            land = d.land;
            borders = d.borders;
        }
    }).catch(() => {});

    load(atlas50).then((w) => {
        const d = build(w);
        land = d.land;
        borders = d.borders;
    }).catch(() => {});
}
