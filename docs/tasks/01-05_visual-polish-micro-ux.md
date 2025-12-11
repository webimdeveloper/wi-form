# Task ID: 01-05

# Title: Visual Polish & Micro-UX

# Type: child

# Parent Task: docs/tasks/01_front-ui.md

# Status: planned

# Last Updated: 2025-12-11

---

## 1. Context

The WiForm frontend is being refactored in stages:

- **01-02**: Vue architecture and component structure
- **01-03**: CSS structure and theme system
- **01-04**: Behaviour and interactions (reactive state, validation, states)

At this point, the UI should:

- Render with a clean structure
- Behave correctly and reactively
- Use a tokenised CSS system

However, it may still look:

- Basic / unrefined
- Inconsistent in spacing, hierarchy, and visual emphasis
- Lacking micro-interactions that make it feel “smooth” and modern

This task focuses on **how the UI looks and feels**, not on adding new logic.

---

## 2. Goal

Apply **visual polish and micro-UX improvements** to the WiForm frontend to make it:

- Clean and clear in hierarchy
- Pleasant to use and easy to scan
- Consistent with the CSS system defined in `01-03`
- Visually aligned with a modern SaaS/form experience

Without introducing new functional complexity.

---

## 3. Requirements

### 3.1 Visual Design

- [ ] Use the design tokens (colors, spacing, typography, radii) defined in `01-03`.
- [ ] Improve layout and grouping:
  - [ ] Inputs grouped logically with labels and helper text.
  - [ ] Summary/result area visually distinct but integrated.
  - [ ] Status / message area clearly separated but easy to notice.
- [ ] Ensure consistent spacing:
  - [ ] Vertical rhythm between sections.
  - [ ] Consistent padding inside cards/containers.
- [ ] Typography:
  - [ ] Clear title/subtitle hierarchy.
  - [ ] Readable body text.
  - [ ] Matching font sizes and line heights between components.
- [ ] Buttons:
  - [ ] Primary CTA styled clearly.
  - [ ] Secondary/neutral actions if present (e.g., reset).
  - [ ] Clear hover/focus/active states.

### 3.2 Micro-UX & Feedback

- [ ] Add subtle transitions for:
  - [ ] Hover/focus states on inputs and buttons.
  - [ ] State changes for the form (idle → loading → success/error).
- [ ] Ensure error and success messages:
  - [ ] Are visually distinct but not aggressive.
  - [ ] Align with the chosen color system.
- [ ] Loading states:
  - [ ] Use subtle indicators (spinner, text, or skeletons where appropriate).
- [ ] Add small but meaningful details, for example:
  - [ ] Focus outline that matches the theme.
  - [ ] Slight elevation/shadow for cards or key containers (if aligned with brand).
  - [ ] Smooth animation for appearing/disappearing messages.

### 3.3 Accessibility & Usability

- [ ] Contrast ratios respect basic accessibility guidelines (especially for text and key UI elements).
- [ ] Hover states have a non-hover equivalent (e.g., focus styles) for keyboard users.
- [ ] Clickable areas (buttons, key elements) are large enough for comfortable interaction.
- [ ] No essential information is conveyed by color alone (e.g., error states also have icons/text).

### 3.4 Out of Scope

- New behaviour or form logic.
- New fields or structural changes to the layout (except minor tweaks to support visual grouping).
- Full design system documentation (can be future work).

---

## 4. To-Do (for Cursor)

> Cursor should apply styling on top of the existing architecture and behaviour without changing core logic.

- [ ] Review current state of:
  - [ ] Vue components from `01-02`
  - [ ] CSS tokens/structure from `01-03`
  - [ ] Behaviour from `01-04`
- [ ] Propose a simple visual style direction using existing tokens (e.g., “clean, light, card-based UI”).
- [ ] Implement:
  - [ ] Layout improvements (spacing, alignment, grouping).
  - [ ] Typography hierarchy for headings, labels, helper texts.
  - [ ] Button styles and interactive states.
  - [ ] Message styles (errors, warnings, success).
- [ ] Add micro-interactions:
  - [ ] Transitions on hover/focus for buttons and inputs.
  - [ ] Smooth state changes for loading/success/error blocks.
- [ ] Ensure changes are scoped properly (no global WP/theme conflicts).
- [ ] Add minimal comments where design decisions are important for future reference.

---

## 5. Notes (Working Log)

Use to record key styling decisions or challenges.

- 2025-12-11: _[empty – to be filled during implementation]_

---

## 6. Decisions

### 6.1 Visual Style Direction

_TBD – to be filled after implementation._

Suggested format:

- Style keywords: e.g., “light, minimal, subtle shadows, rounded corners”
- Primary colors used:
  - `--color-primary`
  - `--color-accent`
  - etc.
- Layout approach:
  - Card-based / panel-based / full-width sections, etc.

### 6.2 Interaction Patterns

_TBD – to be filled after implementation._

Document:

- How hover/focus is handled for:
  - Inputs
  - Buttons
  - Interactive UI blocks
- How error/success states are visually differentiated.

---

## 7. Testing Checklist

- [ ] Form looks visually consistent on desktop and mobile.
- [ ] Titles, labels, inputs, and summary are easy to scan and understand.
- [ ] Hover/focus states work as expected and improve clarity.
- [ ] Error/success messages are readable and clearly associated with relevant fields/sections.
- [ ] No visual regressions in other parts of the plugin (if any UI is reused).
- [ ] No console errors introduced by styling-related changes.
- [ ] Basic accessibility checks:
  - [ ] Contrast for text and key UI elements.
  - [ ] Focus states visible for keyboard navigation.

---

## 8. Completion

- Status: `planned` → `in-progress` → `done`
- Date completed: _[YYYY-MM-DD]_

Example final commit message for this subtask:

Task-01: Subtask 01-05_visual-polish-micro-ux
