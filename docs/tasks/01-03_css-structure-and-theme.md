# Task ID: 01-03

# Title: CSS Structure and Theme

# Type: child

# Parent Task: docs/tasks/01_front-ui.md

# Status: done

# Last Updated: 2025-12-11

---

## 1. Context

The WiForm frontend is being rebuilt with Vue (see subtask docs/tasks/01-02_add-vue.md).  
The UI currently lacks:

- A consistent visual design system
- A structured CSS architecture
- Design tokens (colors, spacing, typography)
- Predictable component-level styling
- Any reusable styling logic

Before implementing full UX behaviour (01-04) and visual polish (01-05), we need a **clean, scalable CSS foundation**.

This task defines the **styling infrastructure only**, not detailed final design.

---

## 2. Goal

Create a lightweight, maintainable CSS architecture that:

- Uses **modern CSS** (variables, functions, custom properties)
- Is framework-agnostic and works inside the WordPress plugin environment
- Supports theming and future customization
- Provides consistent spacing, typography, and layout primitives
- Is easy for Cursor and humans to extend across future tasks

No detailed styling should be added here—only the **foundation**.

---

## 3. Requirements

### 3.1 Functional

- [ ] Define a **global CSS variable system**:
  - [ ] Colors (semantic naming, e.g., `--color-primary`)
  - [ ] Spacing scale (`--space-xs`, `--space-sm`, etc.)
  - [ ] Typography scale (font sizes, weights)
  - [ ] Radii, borders, transitions
- [ ] Establish a **base stylesheet**:
  - [ ] Reset or normalize
  - [ ] Basic typography rules
  - [ ] Box sizing, layout defaults
- [ ] Create a **component-oriented folder structure**, e.g.:
  - `assets/css/base/`
  - `assets/css/theme/`
  - `assets/css/components/`
- [ ] Provide **class naming conventions** (BEM or simple component-scoped classes).
- [ ] Ensure the CSS is loaded correctly in WordPress via the plugin enqueue logic.

### 3.2 Technical

- [ ] CSS should support light/dark compatibility in the future (variables only; no theme switcher yet).
- [ ] CSS organization should support:
  - incremental component styling
  - overrides for specific environments (WP themes, page builders, etc.)
- [ ] All CSS must be included in the build pipeline if bundling is present.
- [ ] No inline styles unless temporary for scaffolding.

### 3.3 Out of Scope

- Detailed UI styling (handled in `01-05`)
- Behaviour-based styles (loading, error, interactions — handled in `01-04`)
- Final layout of frontend components
- Theme switcher or advanced theming logic

---

## 4. To-Do (for Cursor)

> Completed in this pass.

- [x] Inspect current CSS assets and determine what needs to be replaced/removed (none; built from scratch).
- [x] Propose folder structure under `assets/css` for:
  - base styles
  - tokens
  - utilities
  - component styles
- [x] Create `variables.css` (design tokens) with initial values.
- [x] Create `base.css` (normalize/reset scoped under `.wi_root`, typography, layout defaults).
- [x] Create component-level CSS modules for:
  - Root layout
  - Form inputs
  - Summary area
  - Status message area
- [x] Integrate CSS imports into the frontend build (via Vite entry import).
- [x] Ensure WordPress enqueues the final CSS file correctly.
- [x] Add TODO comments/placeholders for later styling in 01-05.

---

## 5. Notes (Working Log)

Record observations and insights during implementation.

- 2025-12-11: _[empty – to be filled during implementation]_

---

## 6. Decisions

### 6.1 CSS Architecture Model

Structure:
- `assets/css/theme/variables.css` – tokens (colors, spacing, radii, shadows, transitions, fonts), `:root` defaults plus `.wi_theme-dark` hook for future overrides.
- `assets/css/base/base.css` – scoped reset/normalize under `.wi_root` (box-sizing, typography base, links, form controls, lists).
- `assets/css/components/` – per-area styles: `root.css`, `inputs.css`, `summary.css`, `status.css`.
- Imported via `assets/js/frontend/style.css` → Vite bundle → enqueued as `wiform-frontend.css`.

Naming/namespace:
- All selectors prefixed with `.wi_...` (BEM-style blocks/elements), ids with `wi_` if used.
- Modern units (`rem`, `em`, `%`, `clamp()`, `minmax()`) and CSS variables for all spacings/colors/fonts.

Reasoning:
- Scoped to plugin mount to avoid WP theme bleed.
- Token-first for easy theming and future dark mode.
- Component isolation supports future builder extensibility.

### 6.2 Design Token System

Categories implemented:
- Colors: surface, muted, border, primary/accent, states (success/warn/error), focus ring.
- Spacing scale: `--wi_space-2xs` ... `--wi_space-3xl`.
- Typography: font family, weights, size scale via clamp (base/sm/lg, headings).
- Radii/shadows: `--wi_radius-*`, `--wi_shadow-*`.
- Transitions: `--wi_transition-fast`, `--wi_transition-base`.

### 6.3 Loading Method

Bundled via Vite; single CSS output `assets/dist/wiform-frontend.css` enqueued by `wiform-frontend` handle in PHP.

---

## 7. Testing Checklist

- [ ] CSS files compile (if using bundler).
- [ ] Styles load correctly when shortcode is rendered.
- [ ] No console errors relating to missing assets.
- [ ] Root CSS variables are accessible in DevTools.
- [ ] Components use variables (no hard-coded values).
- [ ] No global CSS collisions or style leaks.
- [ ] Responsive rules behave correctly with placeholder markup.

---

## 8. Completion

- Status: `planned` → `in-progress` → `done`
- Date Completed: _[YYYY-MM-DD]_

Example final commit message for this subtask:

Task-01: Subtask 01-03_css-structure-and-theme
