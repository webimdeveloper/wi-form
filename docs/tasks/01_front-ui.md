# Task ID: 01

# Title: Frontend UI

# Type: parent

# Subtasks:

- docs/tasks/01-02_add-vue.md ✅
- docs/tasks/01-03_css-structure-and-theme.md
- docs/tasks/01-04_behaviour-interactions.md
- docs/tasks/01-05_visual-polish-micro-ux.md
- docs/tasks/01-06_qa-refactor-validation.md

# Actions:

- Use the context below to understand the current state of the frontend.
- Read linked subtasks and propose an action plan and suggested file changes.
- If any subtask should be split further, propose new child task IDs following the same naming pattern.

# Status: planned

# Last Updated: 2025-12-11

---

## 1. Context

WiForm is a WordPress plugin that renders a frontend calculator/form via shortcode.  
The current frontend:

- Has outdated and inconsistent structure
- Lacks a proper Vue component architecture
- Provides minimal or incomplete UX feedback and interaction
- Has limited or placeholder-level visual styling

The goal of this parent task is to drive the **full frontend overhaul**, structured across several child tasks.  
This includes Vue integration, component structure, UI behaviour, styling, and final polish.

---

## 2. Goal

Transform the WiForm frontend into a **modern, reactive, elegant UI/UX** using:

- Vue components
- Clean architecture
- Light CSS tooling (CSS variables, functions, modern units)
- Improved interactions and microbehaviours
- A maintainable styling structure

All without breaking the existing shortcode entry point or backend logic.

---

## 3. Scope & Constraints

### **In scope**

- Vue architecture and mounting process
- Component layout and hierarchy
- Reactive state management
- UI interaction patterns (input updates, states, transitions)
- Visual styling using a lightweight CSS library or structured custom CSS
- Small HTML wrapper adjustments for proper frontend mounting

### **Out of scope**

- Backend PHP logic (unless required for mounting changes)
- Calculation logic changes
- API/data layer changes
- Deployment/CI pipeline

### **Technical constraints**

- Must work inside WordPress and existing shortcode system
- Must maintain backward compatibility of shortcode output
- All frontend code must remain inside `assets/js/frontend` (or approved structure)
- All visual styling must follow the new CSS architecture defined in subtasks

---

## 4. Definition of Done (EPIC-Level)

This parent task (EPIC) is considered **done** when all subtasks reach “done” status and the following conditions are met:

### **Architecture & Structure**

- Vue is properly integrated and mounted
- Component tree is clear, documented, and stable

### **Behaviour & UX**

- Inputs update reactive state in real time
- UI provides proper feedback:
  - loading
  - error
  - disabled
  - success
- No console errors during usage

### **Visual Design**

- UI is clean, consistent, and readable
- Spacing, typography, and layout follow the defined CSS system
- Basic micro-interactions (hover, focus, transitions) are implemented

### **Documentation & Quality**

- All child-task `task.md` files contain Decisions & Notes
- Architecture docs are updated with the final component structure
- Code is cleaned, refactored, and free of unused logic

---

## 5. Final Commit Rule

When all above conditions are met and **every subtask is marked complete**, this EPIC is closed with a single final commit:

Task-01: Frontend UI (01-02, 01-03, 01-04, 01-05, 01-06)
