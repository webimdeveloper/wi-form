# Task ID: 01-02
# Title: Add Vue Architecture
# Type: child
# Parent Task: 01_frontend-ui
# Status: done
# Last Updated: 2025-12-11

---

## 1. Context

WiForm is a WordPress plugin that renders a frontend calculator/form via shortcode.

The current frontend:

- Uses basic JS/markup without a clear component structure
- Does not fully leverage Vue for state and UI composition
- Is harder to extend, test, and reason about

This task focuses **only** on setting up a clean Vue-based architecture and component structure.  
No visual polish or detailed behaviour should be implemented here (that will be handled in other subtasks).

This subtask is part of the parent EPIC:

- `Task 01 – Frontend UI`

---

## 2. Goal

Establish a **clean, maintainable Vue architecture** for the WiForm frontend, providing:

- A single, stable Vue mount point for the shortcode output
- A clear, documented component tree
- Centralised reactive state for the form
- Basic data flow (props/emits) between components

The result should be a solid “skeleton” that later subtasks (behaviour, styling, micro-UX) can build upon.

---

## 3. Requirements

### 3.1 Functional

- [ ] Vue is used to render the main frontend UI for WiForm.
- [ ] There is a top-level root component (e.g. `WiFormRoot`) that:
  - Owns the main reactive state
  - Controls which child components are visible/rendered
- [ ] Child components exist for logical areas, for example (names can be refined):
  - [ ] `WiFormInputs` – input fields / steps
  - [ ] `WiFormSummary` – summary / result area
  - [ ] `WiFormStatusBar` or similar – messages / statuses
- [ ] Placeholder markup is created where real UI/behaviour will be implemented later.

### 3.2 Technical

- [ ] Vue is mounted to a predictable DOM node that is rendered by the shortcode.
- [ ] All Vue-related code lives under a clear structure, e.g.:
  - `assets/js/frontend/main.js` (entry)
  - `assets/js/frontend/components/*.vue` or similar
- [ ] No breaking changes to the public shortcode API.
- [ ] No direct DOM manipulation for UI that should be handled by Vue.
- [ ] The build/bundling setup (if present) still works and produces a usable frontend asset.

### 3.3 Out of Scope (for this task)

- No final visual styling or design system.
- No full validation/error/submit logic.
- No complex animations or micro-interactions.
- No PHP/backend changes except minimal adjustments for mount point if absolutely necessary.

---

## 4. To-Do (for Cursor)

> Note: Cursor should update/expand this list after analysing the repo.

- [ ] Analyse current frontend entry point and assets structure under `assets/js` (or existing path).
- [ ] Identify existing shortcode output and decide on a stable mount point for Vue.
- [ ] Propose a component tree (root + main children) and add it here in the Decisions section.
- [ ] Implement:
  - [ ] Vue setup and mount in the entry file.
  - [ ] Root component (`WiFormRoot`) with minimal reactive state.
  - [ ] Child components (structure only, minimal placeholders).
- [ ] Replace/adjust existing JS so that Vue becomes the primary UI layer.
- [ ] Add TODO comments where behaviour/styling will be implemented in other tasks.
- [ ] Ensure the build process (if present) compiles and enqueues the correct bundle.

---

## 5. Notes (Working Log)

Use this section to capture important observations while working.  
Can be filled by you or appended by Cursor when something relevant is discovered.

- 2025-12-11: _[empty – to be filled during implementation]_
- 2025-12-11: Vue scaffold, Vite build config, shortcode mount, and initial placeholders added. Built `assets/dist/wiform-frontend.{js,css}` with `npm run build`.
- 2025-12-11: Added initial calculation in `WiFormRoot` so default Company/1 trademark/1 class shows totals; idle status hidden unless messaging is set; rebuilt bundle.

---

## 6. Decisions

This section records final decisions that matter for future tasks.

### 6.1 Component Structure

Root: `WiFormRoot`  
Children:
- `WiFormInputs` – input + repeater area emitting updates for mode/rows/email visibility
- `WiFormSummary` – summary placeholder fed by root state
- `WiFormStatusBar` – top-level status/message display

Reasoning:
- Separates data entry, summary, and status for later behaviour/styling tasks
- Centralises state in root to simplify later logic additions

### 6.2 Mount Point

Mount element: `<div class="wiform-root" data-wiform-id="wiform-*" data-wiform-config="..."></div>` rendered by the shortcode.  
Reasoning:
- Stable, predictable selector for multi-instance mounting
- Data attribute carries JSON config directly from PHP
- Keeps shortcode API intact while letting Vue own the UI

---

## 7. Testing Checklist

- [ ] Shortcode page renders without PHP errors.
- [ ] The Vue app mounts successfully (no errors in console).
- [ ] Vue devtools (if enabled) shows the expected root component and children.
- [ ] When basic test data/state is changed, UI updates reflect the change (even if visually simple).
- [ ] No hard-coded environment-specific paths or assumptions.
- [ ] Legacy JS interacting with the same DOM is either removed or clearly separated to avoid conflicts.

---

## 8. Completion

- Status: `planned` → `in-progress` → `done`
- Date completed: _[YYYY-MM-DD]_
- Related commit(s) should follow this pattern:

Example final commit message for this subtask:

