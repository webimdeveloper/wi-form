<script setup>
const props = defineProps({
  summary: {
    type: Object,
    default: () => ({
      mode: 'company',
      trademarks: 1,
      classes: 1,
      totals: {
        stateDutyUSD: 0,
        serviceUSD: 0,
        totalUSD: 0,
      },
    }),
  },
});
</script>

<template>
  <div class="wi_summary">
    <div class="wi_card wi_card--totals">
      <p class="wi_card__title">Totals</p>
      <div class="wi_stat">
        <span class="wi_stat__label">Mode</span>
        <span class="wi_stat__value">{{ summary.mode }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Trademarks</span>
        <span class="wi_stat__value">{{ summary.trademarks }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Total classes</span>
        <span class="wi_stat__value">{{ summary.classes }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">State duty (USD)</span>
        <span class="wi_stat__value">{{ summary.totals.stateDutyUSD }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Service (USD)</span>
        <span class="wi_stat__value">{{ summary.totals.serviceUSD }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Total (USD)</span>
        <span class="wi_stat__value">{{ summary.totals.totalUSD }}</span>
      </div>
    </div>
  </div>
</template>

