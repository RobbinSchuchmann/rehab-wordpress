# Rehab Theme — CSS Style Guide

This guide is the law for any CSS in this codebase. Follow it without exception.

## 1. Three CSS layers

```
parent-theme/assets/css/
  tokens.css       Design tokens (CSS custom properties). Single source of truth.
  typography.css   Type system: body, headings, eyebrows, leads, links.
  buttons.css      Shared button system (.rehab-btn + variants).
  layout.css       Containers, section padding, background utilities.
  utilities.css    Atomic helpers (visually-hidden, animations, etc.).

child-theme/{brand}/style.css
  ONLY :root token overrides. No new rules. No layout. No block CSS.

plugins/rehab-blocks/src/{block}/style.scss
  Block-specific layout. Uses ONLY tokens. Never hardcodes brand values.
```

## 2. Token taxonomy

All token names are prefixed `--rehab-`. Defined in `parent-theme/assets/css/tokens.css`.

### Colors
- `--rehab-color-bg` — page background
- `--rehab-color-cream` — section alt background
- `--rehab-color-fg` — base text color (alias of warm-charcoal)
- `--rehab-sage` — primary brand accent
- `--rehab-sage-dark` — darker accent (hover states)
- `--rehab-sage-text` — used for accent-colored text and eyebrows
- `--rehab-ethereal-sage` — alt section background tint
- `--rehab-tan` — secondary accent
- `--rehab-warm-charcoal` — primary text (#2C2C2C-ish)
- `--rehab-soft-charcoal` — secondary text

### Typography
- `--rehab-font-body` — body sans (Inter)
- `--rehab-font-display` — display serif (Ivymode → Georgia fallback)

### Spacing
- `--rehab-space-xs` `--sm` `--md` `--lg` `--xl` — 0.5/1/1.5/3/5 rem

### Layout
- `--rehab-container-max` — primary container max width

### Effects
- `--rehab-ease`, `--rehab-ease-out` — cubic-beziers
- `--rehab-shadow-subtle`, `--rehab-shadow-elevated`, `--rehab-shadow-dramatic`

### Radii
- `--rehab-radius-sm` `--md` `--lg` `--pill`

## 3. Hard rules

1. **No hardcoded hex/rgb in block CSS.** Use `var(--rehab-*)`.
2. **No hardcoded spacing values.** Use `var(--rehab-space-*)`.
3. **No bare class names.** Always prefix with `rehab-`.
4. **BEM naming.** `.rehab-hero__headline`, `.rehab-card--featured`.
5. **Tokens with fallbacks.** `var(--rehab-sage, #BAC3A1)` so block CSS still renders if tokens fail to load.
6. **One purpose per file.** No "miscellaneous" files.
7. **No SCSS nesting beyond 1 level.** Flat selectors are easier to debug.
8. **Block CSS lazy-loads.** Stay inside `src/{block}/style.scss`. Only load when block is on a page (handled by `block.json`).

## 4. Brand variation

Each child theme overrides tokens. Example for a hypothetical "Anker" brand:

```css
/* themes/anker-child/style.css */
:root {
  --rehab-sage: #4A7B6E;        /* swap to Anker's brand color */
  --rehab-sage-dark: #2F5A4F;
  --rehab-sage-text: #3A6155;
  --rehab-warm-charcoal: #1A1A1A;
  --rehab-font-display: "Cormorant Garamond", Georgia, serif;
}
```

That's it. No CSS rewrites. Same blocks render with the new brand.

## 5. When tokens aren't enough

If a block needs brand-specific structural variation (e.g. button radius, image radius), introduce a NEW token at component level:

```css
/* tokens.css */
--rehab-button-radius: 0;        /* Diamond default: square */
--rehab-image-radius: 100px;     /* Diamond default: rounded top-left */
```

Then child theme can override as needed. This way structural variation stays declarative, never imperative.

## 6. Naming conventions

| Pattern | Meaning | Example |
|---|---|---|
| `.rehab-{block}` | Block root | `.rehab-hero` |
| `.rehab-{block}__{element}` | Element within block | `.rehab-hero__headline` |
| `.rehab-{block}--{modifier}` | Variant of block | `.rehab-hero--minimal` |
| `.rehab-{utility}` | Theme-wide utility | `.rehab-container`, `.rehab-btn` |
| `--rehab-{token}` | Design token | `--rehab-sage` |

## 7. Responsive

Mobile-first. Use these breakpoints (consistent with Diamond's CSS):

- 640px (sm) — small tablets
- 768px (md) — tablets
- 1024px (lg) — laptop
- 1366px / 1440px (xl) — desktop

Apply via `@media (min-width: 1024px) { … }`. Don't introduce new breakpoints without coordination.

## 8. JavaScript-coupled CSS

If a block has JS-driven state (e.g. accordion open, slider active), gate state classes with the block prefix:

- `is-active`, `is-open`, `is-loading` — state modifiers (no `rehab-` prefix, scoped under block selector)
- `[data-rehab-X]` — JS hooks (data attributes, not classes)

Example: `.rehab-faq__item.is-open { … }`
