<style>
    :root {
        --auth-ink: #18140f;
        --auth-ink-70: #6b6259;
        --auth-ink-40: #a89f97;
        --auth-ink-15: #eae5df;
        --auth-ink-06: #f5f2ee;
        --auth-parch: #faf8f5;
        --auth-white: #ffffff;
        --auth-clay: #9b6e58;
        --auth-clay-lt: #f4ede8;
        --auth-sage: #617a62;
        --auth-gold: #b08d5c;
        --auth-r-md: 14px;
        --auth-r-lg: 20px;
        --auth-r-xl: 32px;
    }

    body.auth-body { background: #f6f1ea; color: var(--auth-ink); }
    .auth-wrap {
        display: grid;
        grid-template-columns: minmax(320px, 430px) minmax(0, 1fr);
        min-height: 100vh;
        font-family: "DM Sans", system-ui, sans-serif;
    }
    .auth-brand-side {
        display: flex;
        align-items: stretch;
        min-height: 100vh;
        padding: 18px 0 18px 18px;
    }
    .auth-visual {
        position: sticky;
        top: 18px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 100%;
        height: calc(100vh - 36px);
        overflow: hidden;
        border-radius: 28px;
        background:
            radial-gradient(circle at 20% 16%, rgba(176,141,92,.22), transparent 34%),
            linear-gradient(145deg, #251f19, #574232 58%, #8f6a4e);
        padding: clamp(22px, 3vw, 34px);
        box-shadow: 0 24px 70px rgba(24,20,15,.16);
    }
    .auth-logo { position: relative; z-index: 2; color: var(--auth-white); font-family: Georgia, serif; font-size: 23px; letter-spacing: -.3px; text-decoration: none; }
    .auth-logo em { color: var(--auth-gold); font-style: italic; }
    .auth-postcard {
        position: relative;
        flex: 0 1 58%;
        min-height: 320px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 22px;
        background: #1e1813;
        box-shadow: 0 18px 48px rgba(0,0,0,.22);
    }
    .auth-postcard img { width: 100%; height: 100%; object-fit: cover; filter: saturate(.9) contrast(.98) brightness(.74); }
    .auth-postcard__shade { position: absolute; inset: 0; background: linear-gradient(180deg, rgba(24,20,15,.08), rgba(24,20,15,.78)); }
    .auth-postcard__caption { position: absolute; right: 22px; bottom: 22px; left: 22px; color: var(--auth-white); }
    .auth-postcard__caption span { display: block; margin-bottom: 9px; color: rgba(255,255,255,.62); font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
    .auth-postcard__caption strong { display: block; max-width: 300px; font-family: Georgia, serif; font-size: clamp(26px, 3.2vw, 38px); font-weight: 400; line-height: 1.02; }
    .auth-postcard__caption em { color: var(--auth-gold); font-style: italic; }
    .auth-route-mark {
        position: absolute;
        top: 20px;
        right: 20px;
        display: grid;
        gap: 10px;
        border: 1px solid rgba(255,255,255,.14);
        border-radius: 999px;
        background: rgba(255,255,255,.12);
        padding: 10px;
        backdrop-filter: blur(12px);
    }
    .auth-route-mark span { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,.55); }
    .auth-route-mark span:nth-child(2) { background: var(--auth-gold); }
    .auth-route-mark span:nth-child(3) { background: rgba(255,255,255,.32); }
    .auth-visual__footer { position: relative; z-index: 2; }
    .auth-visual__footer p { max-width: 310px; margin-bottom: 16px; color: rgba(255,255,255,.62); font-size: 13px; line-height: 1.65; }
    .auth-trust-list { display: flex; flex-wrap: wrap; gap: 8px; max-width: 360px; }
    .auth-trust-list span {
        border: 1px solid rgba(255,255,255,.16); border-radius: 999px; background: rgba(255,255,255,.08);
        color: rgba(255,255,255,.68); padding: 7px 12px; font-size: 11px; backdrop-filter: blur(8px);
    }
    .auth-form-side {
        display: flex;
        align-items: center;
        justify-content: center;
        overflow-y: auto;
        padding: clamp(28px, 6vw, 76px);
        background:
            linear-gradient(120deg, rgba(255,255,255,.86), rgba(250,248,245,.88)),
            radial-gradient(circle at 88% 14%, rgba(155,110,88,.12), transparent 30%);
    }
    .auth-form-inner { width: 100%; max-width: 430px; }
    .auth-form-top { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 34px; }
    .auth-back { display: inline-flex; align-items: center; gap: 7px; color: var(--auth-ink-40); font-size: 13px; text-decoration: none; transition: color .2s, transform .2s; }
    .auth-back:hover { color: var(--auth-ink); transform: translateX(-2px); }
    .auth-mode-switch {
        display: inline-flex;
        flex: 0 0 auto;
        gap: 4px;
        border: 1px solid var(--auth-ink-15);
        border-radius: 999px;
        background: rgba(255,255,255,.66);
        padding: 4px;
        box-shadow: 0 8px 28px rgba(24,20,15,.06);
    }
    .auth-mode-link {
        min-width: 92px;
        border-radius: 999px;
        color: var(--auth-ink-70);
        padding: 8px 13px;
        font-size: 12px;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        transition: background .2s, color .2s, box-shadow .2s;
        white-space: nowrap;
    }
    .auth-mode-link:hover { color: var(--auth-ink); }
    .auth-mode-link.is-active { background: var(--auth-ink); color: var(--auth-white); box-shadow: 0 8px 18px rgba(24,20,15,.14); }
    .auth-head { margin-bottom: 28px; }
    .auth-title { margin-bottom: 7px; color: var(--auth-ink); font-family: Georgia, serif; font-size: clamp(27px, 4vw, 34px); font-weight: 400; letter-spacing: -.4px; line-height: 1.08; }
    .auth-subtitle { color: var(--auth-ink-70); font-size: 14px; font-weight: 300; line-height: 1.6; }
    .auth-field { margin-bottom: 16px; }
    .auth-field label { display: block; margin-bottom: 6px; color: var(--auth-ink-70); font-size: 12px; font-weight: 600; }
    .auth-field input, .auth-field select, .auth-field textarea {
        width: 100%; border: 1.5px solid var(--auth-ink-15); border-radius: var(--auth-r-md);
        background: rgba(255,255,255,.7); color: var(--auth-ink); outline: none; padding: 13px 16px; font-size: 14px;
        transition: border-color .2s, background .2s, box-shadow .2s;
    }
    .auth-field textarea { min-height: 96px; resize: vertical; line-height: 1.6; }
    .auth-field input:focus, .auth-field select:focus, .auth-field textarea:focus { border-color: var(--auth-clay); background: var(--auth-white); box-shadow: 0 0 0 4px rgba(155,110,88,.12); }
    .auth-field-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .auth-error { margin-top: 6px; color: #b42318; font-size: 12px; }
    .auth-status { margin-bottom: 18px; border-radius: var(--auth-r-md); background: var(--auth-sage); color: var(--auth-white); padding: 10px 14px; font-size: 13px; }
    .auth-forgot { float: right; margin-top: -10px; margin-bottom: 20px; color: var(--auth-clay); font-size: 12px; text-decoration: none; }
    .auth-form-row { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-bottom: 20px; }
    .auth-form-row .auth-forgot { float: none; margin: 0; white-space: nowrap; }
    .auth-form-row .auth-check { margin-bottom: 0; }
    .auth-check { display: flex; align-items: flex-start; gap: 10px; margin-bottom: 20px; }
    .auth-check input { width: 16px; height: 16px; flex-shrink: 0; margin-top: 2px; accent-color: var(--auth-clay); }
    .auth-check label { color: var(--auth-ink-70); cursor: pointer; font-size: 12px; font-weight: 300; line-height: 1.55; }
    .auth-submit { width: 100%; border: 0; border-radius: 999px; background: var(--auth-ink); color: var(--auth-white); cursor: pointer; margin-bottom: 18px; padding: 13px; font-size: 14px; font-weight: 600; transition: background .2s, transform .15s, box-shadow .2s; box-shadow: 0 14px 34px rgba(24,20,15,.14); }
    .auth-submit:hover { background: #2c261f; transform: translateY(-1px); }
    .auth-submit--clay { background: var(--auth-clay); }
    .auth-submit--clay:hover { background: #7a5443; }
    .auth-switch { border-top: 1px solid var(--auth-ink-15); color: var(--auth-ink-70); margin-top: 6px; padding-top: 18px; text-align: center; font-size: 13px; }
    .auth-switch a { color: var(--auth-clay); font-weight: 700; text-decoration: none; }
    .auth-actions { display: flex; align-items: center; justify-content: space-between; gap: 14px; margin-top: 20px; }
    .auth-actions .auth-submit { width: auto; min-width: 150px; margin-bottom: 0; padding-left: 24px; padding-right: 24px; }
    .auth-secondary-link {
        border: 0; background: transparent; color: var(--auth-clay); cursor: pointer;
        font-size: 13px; font-weight: 600; text-decoration: none;
    }
    .auth-panel-note {
        border: 1px solid var(--auth-ink-15); border-radius: var(--auth-r-lg);
        background: rgba(255,255,255,.66); color: var(--auth-ink-70);
        margin-bottom: 22px; padding: 16px 18px; font-size: 13px; line-height: 1.65;
    }
    .auth-agency-page { min-height: 100vh; background: var(--auth-parch); font-family: "DM Sans", system-ui, sans-serif; }
    .agency-top-nav { position: sticky; top: 0; z-index: 10; display: flex; align-items: center; justify-content: space-between; height: 64px; border-bottom: 1px solid var(--auth-ink-15); background: var(--auth-white); padding: 0 60px; }
    .agency-logo { color: var(--auth-ink); font-family: Georgia, serif; font-size: 21px; text-decoration: none; }
    .agency-logo em { color: var(--auth-clay); font-style: italic; }
    .agency-hero { display: grid; grid-template-columns: 1fr 1fr; gap: 60px; align-items: center; background: var(--auth-ink); padding: 52px 60px; }
    .agency-eyebrow { margin-bottom: 14px; color: var(--auth-gold); font-size: 10px; font-weight: 700; letter-spacing: 2.5px; text-transform: uppercase; }
    .agency-hero h1 { margin-bottom: 12px; color: var(--auth-white); font-family: Georgia, serif; font-size: clamp(28px, 3vw, 42px); font-weight: 400; line-height: 1.08; }
    .agency-hero h1 em { color: var(--auth-gold); font-style: italic; }
    .agency-hero p { max-width: 430px; color: rgba(255,255,255,.48); font-size: 15px; font-weight: 300; line-height: 1.7; }
    .agency-perks { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .agency-perk { display: flex; align-items: center; gap: 10px; color: rgba(255,255,255,.62); font-size: 13px; }
    .agency-main { display: grid; grid-template-columns: 1fr 360px; gap: 28px; max-width: 1100px; margin: 0 auto; padding: 40px 60px 80px; }
    .agency-card, .agency-side-card { overflow: hidden; border: 1px solid var(--auth-ink-15); border-radius: var(--auth-r-xl); background: var(--auth-white); }
    .agency-card__head { border-bottom: 1px solid var(--auth-ink-06); padding: 28px 36px 22px; }
    .agency-card__body { padding: 28px 36px 32px; }
    .agency-section-label { margin: 10px 0 12px; border-top: 1px solid var(--auth-ink-06); padding-top: 18px; color: var(--auth-ink-40); font-size: 10px; font-weight: 700; letter-spacing: 2px; text-transform: uppercase; }
    .agency-side-card { background: var(--auth-ink); padding: 28px 26px; color: var(--auth-white); }
    .agency-side-card h3 { margin-bottom: 18px; font-family: Georgia, serif; font-size: 18px; font-weight: 400; }
    .agency-side-card p { color: rgba(255,255,255,.42); font-size: 12px; font-weight: 300; line-height: 1.6; }

    @media (max-width: 1000px) {
        .auth-wrap { grid-template-columns: 1fr; }
        .auth-brand-side { min-height: auto; padding: 14px 14px 0; }
        .auth-visual { position: relative; top: auto; height: auto; min-height: 280px; gap: 24px; }
        .auth-postcard { display: none; }
        .auth-form-side { align-items: flex-start; padding: 36px 28px; }
        .agency-hero, .agency-main { grid-template-columns: 1fr; padding-left: 28px; padding-right: 28px; }
        .agency-perks, .agency-sidebar { display: none; }
        .agency-top-nav { padding: 0 28px; }
    }

    @media (max-width: 640px) {
        .auth-field-row { grid-template-columns: 1fr; }
        .auth-form-top { align-items: stretch; flex-direction: column; margin-bottom: 26px; }
        .auth-mode-switch { width: 100%; }
        .auth-mode-link { flex: 1; min-width: 0; }
        .auth-form-row { align-items: flex-start; flex-direction: column; }
        .auth-visual { border-radius: 22px; padding: 22px; }
        .auth-visual__footer p { max-width: none; }
        .auth-actions { align-items: stretch; flex-direction: column-reverse; }
        .auth-actions .auth-submit { width: 100%; }
        .agency-card__head, .agency-card__body { padding-left: 20px; padding-right: 20px; }
    }
</style>
