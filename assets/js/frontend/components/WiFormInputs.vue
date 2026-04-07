<script setup>
import { computed } from "vue";
import DeleteIcon from '@/assets/img/delete_icon.svg?component';

// Repeater constraints
const MAX_ROWS = 5;
const MAX_CLASSES = 45;
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
  clone.push({ id: `row-${nextRowId.value}`, classes: 1, searchEnabled: false, accelEnabled: false, trademarkType: '' });
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

function updateClassesByDelta(id, delta) {
  const clone = props.rows.map((row) => {
    if (row.id !== id) return row;
    const current = Number(row.classes);
    const safeCurrent = Number.isFinite(current) ? current : MIN_CLASSES;
    const next = Math.min(MAX_CLASSES, Math.max(MIN_CLASSES, safeCurrent + delta));
    return { ...row, classes: next };
  });
  emit("update:rows", clone);
}

function updateRowFlag(id, key, checked) {
  const clone = props.rows.map((row) => {
    if (row.id !== id) return row;
    const next = { ...row, [key]: checked };
    if (!next.searchEnabled && !next.accelEnabled) {
      next.trademarkType = '';
    } else if (!['word', 'fig', 'combined'].includes(next.trademarkType)) {
      next.trademarkType = 'word';
    }
    return next;
  });
  emit("update:rows", clone);
}

function toggleRowFlag(id, key) {
  const row = props.rows.find((item) => item.id === id);
  if (!row) return;
  updateRowFlag(id, key, !Boolean(row[key]));
}

function updateTrademarkType(id, value) {
  const clone = props.rows.map((row) =>
    row.id === id ? { ...row, trademarkType: value } : row
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
          <transition-group name="wi_tm" tag="div" class="wi_inputs__rows-list">
          <div class="wi_row wi_row--trademark" v-for="(row, index) in rows" :key="row.id">
            <div class="wi_row__line wi_row__line--main">
              <div class="wi_row__header-row">
                <span class="wi_row__label wi_row__label--trademark">{{ config.labels?.trademark || 'Trademark' }} #{{ index + 1 }}</span>
                <button
                  v-if="rows.length > 1"
                  type="button"
                  class="wi_row__action wi_row__remove"
                  @click="removeRow(row.id)"
                >
                  <DeleteIcon class="wi_icon--delete" aria-hidden="true" />
                  <span class="visually-hidden">Remove trademark row</span>
                </button>
              </div>

              <div class="wi_row__classes-row">
                <span class="wi_row__label wi_row__label--classes">{{ config.labels?.number_of_classes || 'Number of classes' }}:</span>
                <div class="wi_row__control wi_row__control--classes">
                  <div class="wi_row__stepper">
                    <button
                      type="button"
                      class="wi_stepper-btn"
                      :disabled="Number(row.classes) <= MIN_CLASSES"
                      @click="updateClassesByDelta(row.id, -1)"
                    >
                      −
                    </button>
                    <input
                      class="wi_stepper-input"
                      type="number"
                      inputmode="numeric"
                      :min="MIN_CLASSES"
                      :max="MAX_CLASSES"
                      :value="row.classes"
                      @input="updateClasses(row.id, $event.target.value)"
                      @blur="finalizeClasses(row.id, $event.target.value)"
                    />
                    <button
                      type="button"
                      class="wi_stepper-btn"
                      :disabled="Number(row.classes) >= MAX_CLASSES"
                      @click="updateClassesByDelta(row.id, 1)"
                    >
                      +
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="wi_row__line wi_row__line--options">
              <div class="wi_row__options">
                <div class="wi_options__toggle-group">
                  <button
                    type="button"
                    class="wi_option-toggle"
                    :class="{ 'is-active': Boolean(row.searchEnabled) }"
                    @click="toggleRowFlag(row.id, 'searchEnabled')"
                  >
                    <span class="wi_option-toggle__sign" aria-hidden="true"></span>
                    <span>{{ config.labels?.trademark_clearance_search || 'Trademark Search' }}</span>
                  </button>
                  <button
                    type="button"
                    class="wi_option-toggle"
                    :class="{ 'is-active': Boolean(row.accelEnabled) }"
                    @click="toggleRowFlag(row.id, 'accelEnabled')"
                  >
                    <span class="wi_option-toggle__sign" aria-hidden="true"></span>
                    <span>{{ config.labels?.accelerated_examination || 'Priority Examination' }}</span>
                  </button>
                </div>
                <div
                  class="wi_row__trademark-type-wrap"
                  :class="{ 'is-open': Boolean(row.searchEnabled || row.accelEnabled) }"
                  :aria-hidden="!(row.searchEnabled || row.accelEnabled)"
                >
                  <div class="wi_row__trademark-type-inner">
                    <div class="wi_row__trademark-type">
                      <div class="wi_row__type-row">
                        <span class="wi_row__label wi_row__label--type">
                          {{ config.labels?.trademark_type || 'Choose type:' }}
                        </span>
                        <select
                          class="wi_row__type-select"
                          :value="row.trademarkType || 'word'"
                          :disabled="!(row.searchEnabled || row.accelEnabled)"
                          @change="updateTrademarkType(row.id, $event.target.value)"
                        >
                          <option value="word">{{ config.labels?.word_trademark || 'Word mark' }}</option>
                          <option value="fig">{{ config.labels?.figurative_trademark || 'Logo mark' }}</option>
                          <option value="combined">{{ config.labels?.combined_trademark || 'Combined mark' }}</option>
                        </select>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          </transition-group>
          <div class="wi_row wi_row--add" v-if="canAddRow">
            <div class="wi_row__control wi_row__control--full">
              <button
                type="button"
                class="wi_row__action wi_actions__add wi_actions__add--full"
                @click="addRow()"
              >
                + {{ config.labels?.add_another_trademark || 'Add Trademark' }}
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
