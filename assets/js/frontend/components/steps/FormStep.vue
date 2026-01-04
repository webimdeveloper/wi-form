<script setup>
import { defineProps, defineEmits, ref } from 'vue';
import WiFormInputs from '../WiFormInputs.vue';

const props = defineProps({
  formState: { type: Object, required: true },
  config: { type: Object, required: true },
});

const emit = defineEmits(['update:mode', 'update:rows', 'next']);
const showError = ref(false);

function handleMode(nextMode) {
  emit('update:mode', nextMode);
  // Clear error state as soon as a mode is selected
  showError.value = false;
}

function handleRows(nextRows) {
  emit('update:rows', nextRows);
}

function onNext() {
  // Validation: customer type must be selected
  if (!props.formState.mode || !['company', 'private'].includes(props.formState.mode)) {
    showError.value = true;
    console.warn('WiForm: customer type not selected');
    return; // do not emit next
  }

  // Reset error if customer type is now selected
  showError.value = false;

  // Validation: at least one trademark row with valid classes
  const rows = Array.isArray(props.formState.rows) ? props.formState.rows : [];
  if (rows.length === 0) {
    console.warn('WiForm: no trademark rows');
    return;
  }

  // Ensure all rows have valid class counts (>= 1)
  const allValid = rows.every((row) => {
    const classes = Number(row.classes);
    return Number.isFinite(classes) && classes >= 1;
  });

  if (!allValid) {
    console.warn('WiForm: invalid trademark class count in one or more rows');
    return;
  }

  emit('next');
}
</script>

<template>
  <div class="wi_step wi_step--form">
    <WiFormInputs
      :mode="formState.mode"
      :rows="formState.rows"
      :config="config"
      :show-error="showError"
      @update:mode="handleMode"
      @update:rows="handleRows"
    >
      <template #secondary>
        <!-- Keep summary preview in the form if desired; parent may choose otherwise -->
      </template>
    </WiFormInputs>

    <div class="wi_step__actions">
      <button class="wi_btn wi_btn--primary wi_btn-prm-100" type="button" @click="onNext">Calculate</button>
    </div>
  </div>
</template>
