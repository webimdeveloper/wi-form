<script setup>
import { computed } from "vue";
import DeleteIcon from '@/assets/img/delete_icon.svg?component';

// Repeater constraints
const MAX_ROWS = 5;
const MAX_CLASSES = 40;
const MIN_CLASSES = 1;

const props = defineProps({
  mode: {
    type: [String, null],
    default: null, // no default; must be explicitly selected
  },
  rows: {
    type: Array,
    default: () => [],
  },
  config: {
    type: Object,
    default: () => ({}),
  },
  showError: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(["update:mode", "update:rows"]);

const nextRowId = computed(() => props.rows.length + 1);
const canAddRow = computed(() => props.rows.length < MAX_ROWS);

function setMode(nextMode) {
  emit("update:mode", nextMode);
}

function addRow() {
  if (!canAddRow.value) return;

  const clone = props.rows.map((row) => ({ ...row }));
  clone.push({ id: `row-${nextRowId.value}`, classes: 1 });
  emit("update:rows", clone);
}

function removeRow(id) {
  if (props.rows.length <= 1) {
    return;
  }
  const filtered = props.rows.filter((row) => row.id !== id);
  emit("update:rows", filtered);
}

function updateClasses(id, value) {
  // Allow temporary empty or out-of-bounds values while typing
  // We just update the state directly to what the user typed (if it's a number-ish)
  // But we won't clamp strictly yet, unless it's obviously bad logic.
  // Actually, for a controlled input, we should probably pass the raw value.
  // BUT the prop expectation might be Number.
  // Let's rely on the fact that if they clear it, value is 0 or empty.
  
  // If we just emit, the parent updates the row. 
  // We shouldn't clamp here if we want to allow "deletion" (which results in empty/0).
  const clone = props.rows.map((row) =>
    row.id === id ? { ...row, classes: value } : row
  );
  emit("update:rows", clone);
}

function finalizeClasses(id, value) {
  // Enforce constraints on blur
  let clamped = Number(value);
  if (!Number.isFinite(clamped) || clamped < MIN_CLASSES) {
    clamped = MIN_CLASSES;
  } else if (clamped > MAX_CLASSES) {
    clamped = MAX_CLASSES;
  }
  
  // Only emit if different
  updateClasses(id, clamped);
}
</script>

<template>
  <div class="wi_inputs">
    <div class="wi_inputs__panel">
      <div class="wi_inputs__group">
        <fieldset class="wi_inputs__fieldset" :class="{ wi_error: showError }">
          <legend class="wi_row__label wi_row_title">
            {{ config.labels ? config.labels.choose_applicant_type : 'Choose applicant type:' }}
            <span class="wi_required" aria-label="required">*</span>
          </legend>
          <div class="wi_inputs__radio-group">
            <label class="wi_inputs__radio-label">
              <input
                type="radio"
                name="customer_type"
                value="company"
                :checked="mode === 'company'"
                @change="setMode('company')"
              />
              <span>{{ config.labels ? config.labels.legal_entity : 'Legal entity' }}</span>
            </label>
            <label class="wi_inputs__radio-label">
              <input
                type="radio"
                name="customer_type"
                value="private"
                :checked="mode === 'private'"
                @change="setMode('private')"
              />
              <span>{{ config.labels ? config.labels.individual : 'Individual' }}</span>
            </label>
          </div>
        </fieldset>
      </div>

      <div class="wi_inputs__group">
        <span class="wi_row__label  wi_row_title">{{ config.labels ? config.labels.specify_details : 'Specify details:' }}</span>
        <div class="wi_inputs__rows">
          <div class="wi_row" v-for="(row, index) in rows" :key="row.id">
            <div class="wi_row__group-label">
              <span class="wi_row__label wi_row__label--trademark"
                >Trademark #{{ index + 1 }}</span
              >
              <span class="wi_row__label wi_row__label--classes"
                >Number of classes:</span
              >
            </div>
            <div class="wi_row__control wi_row__control--classes">
              <input
                type="number"
                :min="MIN_CLASSES"
                :max="MAX_CLASSES"
                :value="row.classes"
                @input="updateClasses(row.id, $event.target.value)"
                @blur="finalizeClasses(row.id, $event.target.value)"
              />
            </div>
            <button
              v-if="rows.length > 1"
              type="button"
              class="wi_row__action wi_row__remove"
              @click="removeRow(row.id)"
            >
              <!-- <span aria-hidden="true">DELETE1</span> -->
              <DeleteIcon class="wi_icon--delete" aria-hidden="true" />
              <span class="visually-hidden">Remove trademark row</span>
            </button>
          </div>
          <div class="wi_row wi_row--add">
            <!-- <span class="wi_row__label wi_row__label--spacer" aria-hidden="true"
              >Add Trademark</span
            > -->
            <div class="wi_row__control wi_row__control--full">
              <button
                v-if="canAddRow"
                type="button"
                class="wi_row__action wi_actions__add wi_actions__add--full"
                @click="addRow()"
              >
                + Add Trademark
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!--<div class="wi_inputs__panel wi_inputs__panel--right">
      <slot name="secondary" />
    </div>-->
  </div>
</template>
