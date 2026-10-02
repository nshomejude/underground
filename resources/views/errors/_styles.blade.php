{{-- Inline brand styles for every error page. Deliberately not Vite/Tailwind: these must work
     even when the asset build, the database or the session is what failed. --}}
<style>
    .er-page { margin: 0; background: #0b0b0c; color: #b9b4ac; font: 400 17px/1.65 'Instrument Sans', system-ui, -apple-system, 'Segoe UI', sans-serif; -webkit-font-smoothing: antialiased; }
    .er-page *, .er *, .er *::before, .er *::after { box-sizing: border-box; }
    .er { position: relative; display: flex; align-items: center; justify-content: center; overflow: hidden; min-height: 70vh; padding: 56px 20px; background: #0b0b0c radial-gradient(900px 480px at 50% -8%, rgba(201, 162, 90, 0.12), transparent 62%); color: #b9b4ac; font-family: 'Instrument Sans', system-ui, -apple-system, 'Segoe UI', sans-serif; }
    .er-page .er { min-height: 100vh; min-height: 100dvh; }
    .er-code { position: absolute; left: 50%; top: 50%; transform: translate(-50%, -50%); margin: 0; font: 600 clamp(180px, 42vw, 420px)/1 'Playfair Display', Georgia, 'Times New Roman', serif; color: #c9a25a; opacity: 0.06; letter-spacing: -0.02em; pointer-events: none; user-select: none; white-space: nowrap; }
    .er-panel { position: relative; z-index: 1; display: flex; flex-direction: column; align-items: center; gap: 18px; width: 100%; max-width: 580px; text-align: center; }
    .er-seal { display: block; width: 88px; height: 88px; }
    .er-eyebrow { margin: 0; font-size: 13px; font-weight: 600; letter-spacing: 0.18em; text-transform: uppercase; color: #e0be7e; }
    .er-title { margin: 0; font: 600 clamp(30px, 7vw, 46px)/1.15 'Playfair Display', Georgia, 'Times New Roman', serif; color: #f3efe6; overflow-wrap: anywhere; }
    .er-text { margin: 0; max-width: 46ch; font-size: 17px; line-height: 1.65; color: #b9b4ac; }
    .er-hint { margin: 0; max-width: 46ch; font-size: 15px; line-height: 1.6; color: #b9b4ac; }
    .er-actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; margin-top: 8px; width: 100%; }
    .er-btn { display: inline-flex; align-items: center; justify-content: center; min-height: 48px; min-width: 48px; padding: 12px 22px; border: 1px solid #c9a25a; background: transparent; color: #e0be7e; font: 600 15px/1.25 'Instrument Sans', system-ui, sans-serif; letter-spacing: 0.04em; text-decoration: none; cursor: pointer; transition: background 0.2s, color 0.2s; }
    .er-btn:hover { background: #c9a25a; color: #0b0b0c; }
    .er-btn-solid { background: #c9a25a; color: #0b0b0c; }
    .er-btn-solid:hover { background: #e0be7e; }
    .er-btn:focus-visible { outline: 2px solid #e0be7e; outline-offset: 3px; }
    .er-ref { margin: 4px 0 0; padding: 10px 16px; border: 1px solid #2a2825; background: #17161a; font-size: 14px; color: #b9b4ac; }
    .er-ref code { margin-left: 6px; font: 600 15px/1 ui-monospace, 'SFMono-Regular', Consolas, monospace; letter-spacing: 0.12em; color: #f3efe6; user-select: all; }
    @media (max-width: 420px) { .er-actions .er-btn { width: 100%; } }
    @media (prefers-reduced-motion: reduce) { .er-btn { transition: none; } }
</style>
