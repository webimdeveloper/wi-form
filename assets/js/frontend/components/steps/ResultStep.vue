<script setup>
import { defineProps, defineEmits } from 'vue';
import WiFormSummary from '../WiFormSummary.vue';

const props = defineProps({
  results: { type: Object, required: true },
  redirectUrl: { type: String, default: '' },
});

const emit = defineEmits(['back']);

function onBack() {
  emit('back');
}
</script>

<template>
  <div class="wi_step wi_step--result">
    <WiFormSummary :summary="results" />

    <div class="wi_step__actions">
      <button class="wi_btn wi_btn--secondary" type="button" @click="onBack">Back</button>
      <a class="wi_btn wi_btn--primary" :href="redirectUrl || '#'">Continue</a>
    </div>
  </div>
</template>
