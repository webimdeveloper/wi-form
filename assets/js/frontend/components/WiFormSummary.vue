<script setup>
import { computed } from "vue";
const props = defineProps({
  summary: {
    type: Object,
    default: () => ({
      mode: "company",
      trademarks: 1,
      classes: 1,
      totals: {
        stateDutyUSD: 0,
        serviceUSD: 0,
        totalUSD: 0,
      },
    }),
  },
  currency: { type: String, default: 'USD' },
  rate: { type: Number, default: 480 },
});

const emit = defineEmits(['update:currency']);

const formatter = computed(() => {
  if (props.currency === 'USD') {
    return new Intl.NumberFormat('en-US', {
      style: 'currency',
      currency: 'USD',
      minimumFractionDigits: 0,
      maximumFractionDigits: 0,
    });
  }
  // KZT use decimal with comma
  return new Intl.NumberFormat('en-US', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  });
});

function getValue(usdValue) {
  if (props.currency === 'KZT') {
    return usdValue * props.rate;
  }
  return usdValue;
}

// Computed properties for each total
const formattedStateDuty = computed(() =>
  formatter.value.format(getValue(props.summary.totals.stateDutyUSD))
);
const formattedService = computed(() =>
  formatter.value.format(getValue(props.summary.totals.serviceUSD))
);
const formattedTotal = computed(() =>
  formatter.value.format(getValue(props.summary.totals.totalUSD))
);

function onCurrencyChange(e) {
  emit('update:currency', e.target.value);
}
</script>

<template>
  <div class="wi_summary">

<div class="wi_currency-toggle">
  <input
    type="radio"
    name="currency"
    value="USD"
    id="currency-usd"
    :checked="currency === 'USD'"
    @change="onCurrencyChange"
  />
  <label for="currency-usd">USD</label>

  <input
    type="radio"
    name="currency"
    value="KZT"
    id="currency-kzt"
    :checked="currency === 'KZT'"
    @change="onCurrencyChange"
  />
  <label for="currency-kzt">KZT</label>
</div>





    <div class="wi_card wi_card--totals">
      <!-- <p class="wi_card__title">Totals</p> -->
      <div class="wi_stat">
        <span class="wi_stat__label">Applicant type:</span>
        <span class="wi_stat__value">{{
          summary.mode === "company" ? "Company" : "Private person"
        }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Total trademarks:</span>
        <span class="wi_stat__value">{{ summary.trademarks }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Total classes:</span>
        <span class="wi_stat__value">{{ summary.classes }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">State duty:</span>
        <span class="wi_stat__value">{{ formattedStateDuty }} {{ currency }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Service:</span>
        <span class="wi_stat__value">{{ formattedService }} {{ currency }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Total:<sup>*</sup></span>
        <span class="wi_stat__value">{{ formattedTotal }} {{ currency }}</span>
      </div>
    </div>
    <p class="wi_p-note">
      <sup>*</sup> The stated price is for reference only and does not guarantee
      the final cost. The final price will be agreed upon and approved
      separately.
    </p>
  </div>
</template>
