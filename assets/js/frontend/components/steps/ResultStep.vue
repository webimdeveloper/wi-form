<script setup>
import { defineProps, defineEmits } from 'vue';
import WiFormSummary from '../WiFormSummary.vue';

const props = defineProps({
  results: { type: Object, required: true },
  config: { type: Object, default: () => ({}) },
  currency: { type: String, default: 'USD' },
  redirectUrl: { type: String, default: '' },
});

const emit = defineEmits(['back', 'update:currency']);

function onUpdateCurrency(val) {
  emit('update:currency', val);
}

function onBack() {
  emit('back');
}
</script>

<template>
  <div class="wi_step wi_step--result">
    <WiFormSummary
      :summary="results"
      :currency="currency"
      :rate="config.usd_to_uzs || 12000"
      :config="config"
      @update:currency="onUpdateCurrency"
    />

    <div class="wi_step__actions">
      <button class="wi_btn wi_btn--secondary wi_btn-to-back" type="button" @click="onBack">{{ config.labels?.back || '← Back' }}</button>
      <a class="wi_btn wi_btn--primary wi_btn-to-contact" :href="redirectUrl || '#'">{{ config.labels?.request_proposal || 'Request proposal' }}</a>
    </div>
  </div>
</template>
