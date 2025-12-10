# WiForm – Frontend Calculator / Form Component
### Stage: UX + Architecture Specification (Frontend Only)

---

## 1. Goal

Build a standalone frontend calculator component for WiForm that:

- Mounts via shortcode.
- Is fully reactive with instant updates.
- Provides premium UX (smooth micro-interactions).
- Can evolve into a schema-driven calculator system.
- Implements the Trademark Calculator MVP for now.

---

## 2. Scope

### In Scope
- Vue-based frontend calculator.
- Real-time logic + UI updates.
- CTA → reveal email input with animation.
- Basic inline validation.
- Scoped, self-contained CSS.
- Hardcoded schema for this MVP (later editable via admin).

### Out of Scope
- Admin pages.
- Email sending or backend storage.
- CRM integration.
- Full builder UI.
- Translations.

---

## 3. Functional Behavior

### 3.1 Shortcode Rendering

Shortcode: `wi_form_trademark`

PHP output structure (conceptually):

- A wrapper `<div>` with:
  - `class="wiform-root"`.
  - Unique `id` for each instance.
  - `data-wiform-config='{...json schema...}'`.

Vue mounts into this container on page load and reads the schema from `data-wiform-config`.

---

## 4. Trademark Calculator Logic (MVP)

### 4.1 Initial State

- Customer type: **Company**.
- One trademark row with `classes = 1`.
- Summary panel visible immediately (shows valid price).
- CTA button visible.
- Email field hidden.

### 4.2 Reactive Behavior

- Changing Company ↔ Private recalculates instantly.
- Editing number of classes recalculates instantly.
- Adding or removing rows recalculates instantly.
- No “Calculate” button: the quote is always live.

---

## 5. CTA & Email Reveal Flow

- Primary CTA label: **“Get commercial proposal by email”**.
- When user clicks CTA:
  - Email form is revealed under the CTA with smooth height/opacity transition.
  - Email input gains focus.
- Email validation:
  - No error until the field is touched.
  - Inline error message appears if the email is invalid after interaction.
- A secondary “Send” button (future) can be disabled while the email is invalid.
- This stage: front-end validation only; no actual email sending.

---

## 6. Configuration Schema (MVP Shape)

Schema is passed from PHP to JS (via localized script or `data-wiform-config`) and includes:

### 6.1 Exchange Rates

- `buc_to_uzs`: 1 BUC → UZS.
- `usd_to_uzs`: 1 USD → UZS.
- Frontend derives `buc_to_usd = buc_to_uzs / usd_to_uzs`.

### 6.2 Pricing Modes

Two modes: `company` and `private`.

Each mode has:

- `submit_first`: BUC for first class (application).
- `submit_additional`: BUC per additional class.
- `cert_first`: BUC for first class (certificate).
- `cert_additional`: BUC per additional class.
- `service_per_tm_usd`: USD service fee per trademark.

### 6.3 UI Settings

- `ctaLabel`: text for main CTA button.
- `showEmailCta`: boolean flag; if false, email part is hidden/disabled.

This schema is hardcoded for now but designed to be reusable for other calculators later.

---

## 7. Vue Component Architecture

### 7.1 Root Component

Responsibilities:

- Parse schema from container.
- Initialize calculator state.
- Handle:
  - Mode toggle (Company / Private).
  - Trademark row add/remove.
  - Classes input changes.
  - Email reveal and validation.
- Compute all totals.
- Render summary panel and CTA.

### 7.2 Planned Subcomponents (can be extracted later)

- `TrademarkCalculatorRoot` – main wrapper.
- `TrademarkRow` – one row with classes input + remove button.
- `SummaryPanel` – shows totals.
- `EmailReveal` – email form and validation messages.
- `CtaButton` – primary CTA.

For speed, MVP may start as a single component but should be structured so these can be split easily.

---

## 8. Reactive State Model

Core state (Composition API):

- `mode`: `"company"` or `"private"`.
- `rows`: array of trademark rows.
  - Each row: `{ id: string, classes: number }`.
- `email`: string.
- `emailVisible`: boolean.
- `touched.email`: boolean (for validation display).

Derived/computed values:

- `trademarkCount = rows.length`.
- `totalClasses = sum(rows[i].classes)`.
- `totalBUC` for submit + certificate (per schema logic).
- `stateDutyUSD = totalBUC * buc_to_usd`.
- `serviceUSD = trademarkCount * service_per_tm_usd`.
- `totalUSD = stateDutyUSD + serviceUSD`.
- `isEmailValid` (simple regex).
- `hasEmailError = !isEmailValid && touched.email`.

Totals should update instantly and animate between values.

---

## 9. UX Rules

### 9.1 Instant Feedback

- Every change in mode, classes, or rows updates totals immediately.
- No full-form submit needed to see price.

### 9.2 Transitions & Micro-interactions

- Smooth transitions (100–200 ms) for:
  - Summary values (e.g., fade/slide between numbers).
  - Add/remove row (height/opacity transform).
  - Email section reveal.
- Inputs:
  - Subtle focus ring and hover state.
- Buttons:
  - Hover: light scale or background change.
  - Active: slight “press” effect.

### 9.3 Validation Behavior

- Classes:
  - Minimum 1, maximum 100.
  - Values are clamped: if <1 → 1; if >100 → 100.
- Email:
  - Empty is allowed until user interacts with email form.
  - Once touched:
    - If invalid, show inline error text.
    - When valid, hide error text.
- CTA / Send:
  - Primary CTA for reveal is always clickable.
  - If/when a “Send” button exists, it remains disabled while email is invalid.

---

## 10. Styling & Isolation

Styling rules:

- All selectors are namespaced: `.wiform-*`.
- No global resets or typography overrides.
- Use CSS variables on the root container, for example:
  - `--wiform-color-primary`
  - `--wiform-color-border`
  - `--wiform-radius`
  - `--wiform-transition`
- Use `rem` for spacing and font sizes.
- Layout built with flexbox or CSS grid.
- The calculator should look clean on any WordPress theme without collisions.

---

## 11. WordPress Integration

- PHP:
  - Registers shortcode `wi_form_trademark`.
  - Outputs root container + inlined JSON schema.
  - Enqueues built JS/CSS only on pages where shortcode is used (conditional enqueue).
- JS:
  - On DOM ready, selects all `.wiform-root` elements and mounts a Vue app into each.
  - Reads configuration from `data-wiform-config` or a localized JS object.

- Build:
  - Use Vite for local dev (HMR).
  - Production build outputs:
    - one JS bundle for the calculator,
    - one CSS file for calculator styles.

---

## 12. Future Extensions (After This Stage)

Not implemented now, but the architecture should support:

- Admin UI for editing schema (rates, labels, modes).
- Multiple calculator types (different schemas).
- Sending quotes by email from backend.
- Saving leads to database or CRM.
- Multi-step calculator UI (steps/sections).
- Full form builder with:
  - elements,
  - conditional logic,
  - formulas,
  - repeaters,
  - hidden fields.
- Global theming and branding options.
- Accessibility improvements (keyboard navigation, ARIA).

---

## 13. MVP Acceptance Criteria

The frontend calculator is considered complete for this stage when:

- Shortcode renders a working Vue calculator.
- Default Company / 1 trademark / 1 class shows valid price on load.
- Switching Company ↔ Private updates totals instantly.
- Adding/removing rows updates totals smoothly.
- Summary panel updates with small animations.
- CTA reveals email field with transition.
- Email field validates inline and shows/hides error.
- No fatal JS errors in the console.
- Styles do not break or get broken by the active WordPress theme.

---