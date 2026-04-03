# Cursor Task: Trademark Calculator UI Upgrade

## Context

The existing Trademark Registration Cost Calculator widget has working logic for applicant type selection, per-trademark class counts, add/remove trademark rows, and a Calculate button. The goal is to **upgrade only the visual structure and add two new per-row toggles** — without rewriting any existing JS logic, calculation functions, or CSS variables.

---

## What to preserve — do not touch

- All existing JS functions: `addTrademark()`, `removeTrademark()`, `updateClasses()`, the Calculate button's click handler and pricing logic
- All existing CSS custom properties / variables (colors, fonts, spacing)
- The existing applicant type radio buttons and their change handlers
- The existing `Number of classes` input and its validation (min 1, max 20)
- The result/output screen — only the input screen (screen 1) is being changed

---

## What changes

### 1. Wrap each trademark row in a card

Each trademark row (currently rendered however it is) should be wrapped in a container div with class `tm-card`. Structure per card:

```
.tm-card
  └── .tm-card-header
        ├── .tm-label-row
        │     ├── .tm-tag          ← "Trademark #N" label (pill style)
        │     └── .tm-classes-row  ← existing classes input/stepper
        └── .delete-btn            ← existing delete/trash icon (only if 2+ rows)
  └── .tm-addons
        ├── .addons-row
        │     ├── .addon-chip[data-addon="search"]    ← new toggle
        │     └── .addon-chip[data-addon="priority"]  ← new toggle
        └── .type-reveal           ← shown only when ≥1 addon is active
              ├── label "Trademark type"
              ├── <select> with options: Text / Figurative (image) / Combined
              └── helper note: "Type applies to all selected services for this trademark"
```

### 2. Replace the classes plain input with a stepper

Swap the existing `<input type="number">` for a stepper component:

```html
<div class="stepper">
  <button class="stepper-btn" onclick="updateClasses(id, -1)">−</button>
  <span class="stepper-val">1</span>
  <button class="stepper-btn" onclick="updateClasses(id, 1)">+</button>
</div>
```

- The `updateClasses(id, delta)` function already exists — wire the buttons to it
- Keep the existing min=1, max=20 guard that's already in that function
- The displayed value reads from the existing state object

### 3. Add the two addon toggles per row

Each chip is a `<button class="addon-chip">`. State is tracked per trademark in the existing state array — add two boolean fields to each trademark object:

```js
// In the existing trademark object, add:
search: false,
priority: false,
trademarkType: 'text'   // persists when toggles change, not reset
```

Toggle behavior:
- Clicking a chip flips its boolean and re-renders only that card (or full re-render — match whatever pattern is already used)
- Active state: add class `active` to `.addon-chip`
- Chip icon shows `+` when inactive, `✓` when active

### 4. Trademark type dropdown — conditional display

- `.type-reveal` has `display: none` by default
- Add class `show` (which sets `display: block`) when `search === true || priority === true` for that row
- The `<select>` value is bound to `trademarkType` on the trademark object
- On change, update only `trademarkType` — do not reset `search` or `priority`
- When both addons are deactivated, hide `.type-reveal` again but **preserve** the selected type value in state (don't reset it)

### 5. Pass `trademarkType` into existing calculation logic

Find where the existing Calculate button reads from each trademark row. Add `trademarkType` alongside the existing fields (`classes`, etc.) so the calculation function receives it. If the current calculation doesn't use trademark type yet, just pass it through — don't add pricing logic, that's a separate task.

---

## New CSS to add — append after existing styles, do not modify existing rules

Paste these classes as a new block at the end of the existing `<style>` tag:

```css
.tm-card {
  background: #ffffff;
  border: 0.5px solid rgba(0,0,0,0.12);
  border-radius: 12px;
  margin-bottom: 12px;
  overflow: hidden;
}
.tm-card-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 14px 16px 12px;
  border-bottom: 0.5px solid rgba(0,0,0,0.12);
}
.tm-label-row { display: flex; align-items: center; gap: 12px; flex-wrap: wrap; }
.tm-tag {
  font-size: 12px;
  font-weight: 500;
  color: #C9891A;
  background: #FDF8EF;
  border: 0.5px solid #E8C97A;
  border-radius: 20px;
  padding: 3px 10px;
  letter-spacing: 0.02em;
}
.tm-classes-row { display: flex; align-items: center; gap: 8px; }
.classes-label { font-size: 13px; color: #6b6b6b; }
.stepper {
  display: flex;
  align-items: center;
  border: 0.5px solid rgba(0,0,0,0.2);
  border-radius: 8px;
  overflow: hidden;
  height: 32px;
}
.stepper-btn {
  background: #f7f5f1;
  border: none;
  cursor: pointer;
  width: 30px;
  height: 32px;
  font-size: 16px;
  font-weight: 500;
  color: #C9891A;
  display: flex;
  align-items: center;
  justify-content: center;
}
.stepper-btn:hover { background: #F5E6C8; }
.stepper-val {
  width: 32px;
  height: 32px;
  text-align: center;
  line-height: 32px;
  font-size: 14px;
  font-weight: 500;
  border-left: 0.5px solid rgba(0,0,0,0.12);
  border-right: 0.5px solid rgba(0,0,0,0.12);
  background: #ffffff;
}
.tm-addons { padding: 12px 16px; }
.addons-row { display: flex; gap: 8px; flex-wrap: wrap; }
.addon-chip {
  display: flex;
  align-items: center;
  gap: 6px;
  border: 1px solid #E8A530;
  border-radius: 20px;
  padding: 5px 12px 5px 10px;
  cursor: pointer;
  background: #ffffff;
  font-size: 13px;
  color: #C9891A;
  font-weight: 500;
  transition: background 0.18s, color 0.18s, border-color 0.18s;
  user-select: none;
}
.addon-chip:hover { background: #FDF8EF; }
.addon-chip.active {
  background: #E8A530;
  color: #fff;
  border-color: #E8A530;
}
.addon-chip .chip-icon {
  width: 16px;
  height: 16px;
  border-radius: 50%;
  border: 1.5px solid currentColor;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 10px;
  font-weight: 700;
  flex-shrink: 0;
}
.type-reveal {
  display: none;
  margin-top: 10px;
  padding-top: 10px;
  border-top: 0.5px solid rgba(0,0,0,0.12);
}
.type-reveal.show { display: block; }
.type-label {
  font-size: 12px;
  color: #6b6b6b;
  margin-bottom: 6px;
  font-weight: 500;
  text-transform: uppercase;
  letter-spacing: 0.06em;
}
.type-select {
  max-width: 220px;
  width: 100%;
  padding: 7px 28px 7px 10px;
  font-size: 14px;
  border: 1px solid #E8A530;
  border-radius: 8px;
  background-color: #ffffff;
  appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8' viewBox='0 0 12 8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23C9891A' stroke-width='1.5' fill='none' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat;
  background-position: right 10px center;
  cursor: pointer;
}
.type-select:focus { outline: none; border-color: #E8A530; }
.type-note {
  font-size: 12px;
  color: #6b6b6b;
  margin-top: 6px;
  display: flex;
  align-items: center;
  gap: 4px;
}
.info-dot {
  width: 14px;
  height: 14px;
  border-radius: 50%;
  border: 0.5px solid rgba(0,0,0,0.15);
  font-size: 9px;
  font-weight: 700;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
  color: #6b6b6b;
}
```

---

## Checklist for Cursor before finishing

- [ ] Existing Calculate button still fires correctly
- [ ] Existing applicant type radio selection still works
- [ ] Adding a new trademark row increments numbering correctly
- [ ] Deleting a row re-numbers correctly (Trademark #1, #2... no gaps)
- [ ] Delete button only appears when 2+ rows exist
- [ ] Stepper cannot go below 1 or above 20
- [ ] `.type-reveal` only shows when at least one addon chip is active for that row
- [ ] Deactivating both chips hides `.type-reveal` but preserves the selected type in state
- [ ] No existing CSS rules were modified — only new classes appended
- [ ] No existing JS functions were renamed or removed
