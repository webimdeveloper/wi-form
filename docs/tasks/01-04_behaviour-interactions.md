# Task ID: 01-04

# Title: Behaviour & Interactions

# Type: child

# Parent Task: docs/tasks/01_front-ui.md

# Status: planned

# Last Updated: 2025-12-11

---

## 1. Context

The WiForm frontend is being refactored:

- **docs/tasks/01-02_add-vue.md** adds the Vue architecture and component structure.
- **docs/tasks/01-03_css-structure-and-theme.md** introduces the CSS structure and theme system.

Right now, the UI may render correctly but has limited or rough behaviour:

- Inputs may not fully sync with state.
- UI states (loading, error, success) are minimal or missing.
- Feedback to the user is basic or inconsistent.

This task focuses on **how the UI behaves**, not how it looks.  
Styling and visual polish will be handled separately in `01-05`.

---

## 2. Goal

Implement **clear, predictable, and user-friendly behaviour** for the WiForm frontend, including:

- Reactive updates from inputs to state and UI
- Well-defined UI states (idle, loading, error, success)
- Basic validation feedback (at least at a minimal UX level)
- Smooth, non-jumpy transitions between states

The result should make the form **feel alive and responsive**, even before final visual styling.

---

## 3. Requirements

### 3.1 Functional Behaviour

- [ ] All form inputs are bound to reactive state (Vue).
- [ ] Changing any input:
  - [ ] Updates the corresponding state immediately.
  - [ ] Triggers re-calculation or derived state where needed.
- [ ] Form has clear high-level states:
  - [ ] `idle` – before user interaction or while editing.
  - [ ] `loading` – when any async operation is in progress (future-proof, even if mocked now).
  - [ ] `error` – when validation fails or something goes wrong.
  - [ ] `success` – when submission/processing completes (even if only simulated).
- [ ] Visible feedback is displayed for:
  - [ ] Global form-level errors (e.g., “Please fix the errors below”).
  - [ ] Field-level issues (at least basic message or visual flag).
- [ ] Disable/enable:
  - [ ] Submit button disabled when form is invalid or in `loading` state.
  - [ ] Inputs may be disabled during loading if appropriate.

### 3.2 Technical Behaviour

- [ ] Core behaviour logic lives in a single source of truth (e.g., root component or dedicated composable/store).
- [ ] State machine or at least a clear state enum is defined (e.g., `"idle" | "loading" | "error" | "success"`).
- [ ] No duplicated logic across components – children should receive state and callbacks via props/emits.
- [ ] Any async operations (if present or stubbed) are handled with proper error catching.
- [ ] All behaviour is testable and not tightly coupled to specific CSS classes.

### 3.3 Out of Scope for This Task

- Final visual design of error/success states (colors, typography, etc.)
- Micro-animations (hover/focus transitions) – handled in `01-05`.
- Backend integration or real API logic (use stubs/mocks if needed).

---

## 4. To-Do (for Cursor)

> Cursor should review the current Vue architecture (01-02) and CSS structure (01-03) before making changes.

- [ ] Analyse the existing Vue components and identify:
  - [ ] Where state currently lives.
  - [ ] How inputs and summary currently update.
- [ ] Define a simple state model for the form (`idle`, `loading`, `error`, `success`) and document it in the Decisions section.
- [ ] Implement reactive bindings:
  - [ ] Inputs → state.
  - [ ] State → summary/result UI.
- [ ] Add minimal validation logic (simple required fields / basic format checks as needed).
- [ ] Implement global and field-level feedback hooks (logic only, basic plain-text or placeholder markup).
- [ ] Implement button disabled/active rules based on form validity and state.
- [ ] Add comments (`// TODO`) where more advanced behaviour/validation can be added later.
- [ ] Ensure no console errors are thrown during normal use.

---

## 5. Notes (Working Log)

Use to track findings and implementation notes.

- 2025-12-11: _[empty – to be filled during implementation]_

---

## 6. Decisions

### 6.1 State Model

_TBD – to be filled after implementation._

Expected format:

- Possible states:
  - `idle`
  - `loading`
  - `error`
  - `success`

Reasoning:

- Simple enough for now.
- Can be extended later if multi-step logic or async flows are added.

### 6.2 Validation Approach

_TBD – to be filled after implementation._

Possible options:

- Minimal inline validation in the root component.
- Simple validation helpers (pure functions).
- Later extension to a more robust validation library if needed.

---

## 7. Testing Checklist

- [ ] Changing any input updates related state and summary in real time.
- [ ] Submitting an incomplete/invalid form shows a clear error or warning.
- [ ] Form “loading” state is visible (even if simulated with a short timeout).
- [ ] Form cannot be submitted multiple times rapidly during loading.
- [ ] Errors are cleared appropriately when the user fixes input values.
- [ ] No uncaught errors in the console during typical user flow.
- [ ] Basic keyboard navigation (Tab/Shift+Tab) works without breaking behaviour.

---

## 8. Completion

- Status: `planned` → `in-progress` → `done`
- Date completed: _[YYYY-MM-DD]_

Example final commit message for this subtask:

Task-01: Subtask 01-04_behaviour-interactions
