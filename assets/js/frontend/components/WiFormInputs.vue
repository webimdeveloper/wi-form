<script setup>
import { computed } from 'vue';

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

const emit = defineEmits([
  'update:mode',
  'update:rows',
]);

const nextRowId = computed(() => props.rows.length + 1);

function setMode(nextMode) {
  emit('update:mode', nextMode);
}

function addRow() {
  const clone = props.rows.map((row) => ({ ...row }));
  clone.push({ id: `row-${nextRowId.value}`, classes: 1 });
  emit('update:rows', clone);
}

function removeRow(id) {
  if (props.rows.length <= 1) {
    return;
  }
  const filtered = props.rows.filter((row) => row.id !== id);
  emit('update:rows', filtered);
}

function updateClasses(id, value) {
  const clone = props.rows.map((row) =>
    row.id === id ? { ...row, classes: value } : row
  );
  emit('update:rows', clone);
}

</script>

<template>
  <div class="wi_inputs">
    <div class="wi_inputs__panel wi_inputs__panel--left">
      <div class="wi_inputs__group">
        <fieldset class="wi_inputs__fieldset" :class="{ 'wi_error': showError }">
          <legend class="wi_row__label">Customer type <span class="wi_required" aria-label="required">*</span></legend>
          <div class="wi_inputs__radio-group">
            <label class="wi_inputs__radio-label">
              <input
                type="radio"
                name="customer_type"
                value="company"
                :checked="mode === 'company'"
                @change="setMode('company')"
              />
              <span>Company</span>
            </label>
            <label class="wi_inputs__radio-label">
              <input
                type="radio"
                name="customer_type"
                value="private"
                :checked="mode === 'private'"
                @change="setMode('private')"
              />
              <span>Private</span>
            </label>
          </div>
        </fieldset>
      </div>

      <div class="wi_inputs__group">
        <span class="wi_row__label">Trademark</span>
        <div class="wi_inputs__rows">
          <div
            class="wi_row"
            v-for="(row, index) in rows"
            :key="row.id"
          >
            <span class="wi_row__label">Number of classes for one Trademark:</span>
            <div class="wi_row__control">
              <input
                type="number"
                min="1"
                max="100"
                :value="row.classes"
                @input="updateClasses(row.id, Number($event.target.value))"
              />
            </div>
            <button
              v-if="rows.length > 1"
              type="button"
              class="wi_row__action wi_row__remove"
              @click="removeRow(row.id)"
            >
              <span aria-hidden="true">−</span>
              <span class="visually-hidden">Remove trademark row</span>
            </button>
          </div>
          <div class="wi_row wi_row--add">
            <span class="wi_row__label wi_row__label--spacer" aria-hidden="true">Add Trademark</span>
            <div class="wi_row__control wi_row__control--full">
              <button
                type="button"
                class="wi_row__action wi_actions__add wi_actions__add--full"
                @click="addRow()"
              >
                Add Trademark
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>

    <div class="wi_inputs__panel wi_inputs__panel--right">
      <slot name="secondary" />
    </div>
  </div>
</template>

