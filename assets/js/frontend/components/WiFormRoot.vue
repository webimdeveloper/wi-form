<script setup>
import { reactive, ref } from 'vue';
import { calculateTotals } from '../lib/calculations/trademark';
import FormStep from './steps/FormStep.vue';
import ResultStep from './steps/ResultStep.vue';

const props = defineProps({
  config: {
    type: Object,
    default: () => ({}),
  },
  instanceId: {
    type: String,
    default: '',
  },
});

const formState = reactive({
  mode: null, // no default; user must select company or private
  rows: [{ id: 'row-1', classes: 1 }],
});

const currentStep = ref(0); // 0 = form, 1 = result
const results = ref(null);

function handleMode(nextMode) {
  formState.mode = nextMode;
}

function handleRows(nextRows) {
  formState.rows = nextRows;
}

function handleNext() {
  // perform calculation and move to result step
  results.value = calculateTotals(props.config || {}, formState.rows, formState.mode);
  currentStep.value = 1;
}

function handleBack() {
  currentStep.value = 0;
}

</script>

<template>
  <div class="wi_root wi_shell" :data-instance-id="instanceId">
    <div v-if="currentStep === 0" class="wi_section">
      <!--<h2 class="wi_section__heading">Select applicant type below:</h2>-->
      <FormStep
        :formState="formState"
        :config="config"
        @update:mode="handleMode"
        @update:rows="handleRows"
        @next="handleNext"
      />
    </div>

    <div v-else class="wi_section">
      <!-- <h2 class="wi_section__heading">Results</h2> -->
      <ResultStep
        :results="results"
        :redirectUrl="config?.redirectUrl"
        @back="handleBack"
      />
    </div>
  </div>
</template>

