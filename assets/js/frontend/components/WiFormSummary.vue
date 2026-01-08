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

  rate: { type: Number, default: 12000 },
  config: { type: Object, default: () => ({}) },
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
  // UZS use decimal
  return new Intl.NumberFormat('ru-RU', {
    style: 'decimal',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0,
  });
});

function getValue(usdValue) {
  if (props.currency === 'UZS') {
    return usdValue * props.rate;
  }
  return usdValue;
}

// Computed properties for each total
const formattedStateDutySubmit = computed(() => {
  const val = props.currency === 'UZS'
    ? (props.summary.totals.stateDutySubmitUZS ?? (props.summary.totals.stateDutySubmitUSD * props.rate))
    : props.summary.totals.stateDutySubmitUSD;
  return formatter.value.format(val);
});

const formattedStateDutyCert = computed(() => {
  const val = props.currency === 'UZS'
    ? (props.summary.totals.stateDutyCertUZS ?? (props.summary.totals.stateDutyCertUSD * props.rate))
    : props.summary.totals.stateDutyCertUSD;
  return formatter.value.format(val);
});

const formattedStateDuty = computed(() => {
  const val = props.currency === 'UZS'
    ? (props.summary.totals.stateDutyUZS ?? (props.summary.totals.stateDutyUSD * props.rate))
    : props.summary.totals.stateDutyUSD;
  return formatter.value.format(val);
});

const formattedService = computed(() => {
  const val = props.currency === 'UZS'
    ? (props.summary.totals.serviceUZS ?? (props.summary.totals.serviceUSD * props.rate))
    : props.summary.totals.serviceUSD;
  return formatter.value.format(val);
});

const formattedTotal = computed(() => {
  const val = props.currency === 'UZS'
    ? (props.summary.totals.totalUZS ?? (props.summary.totals.totalUSD * props.rate))
    : props.summary.totals.totalUSD;
  return formatter.value.format(val);
});

const formattedClasses = computed(() => {
  const total = props.summary.classes;
  const counts = props.summary.classCounts || [];
  
  if (counts.length > 1) {
    const breakdown = counts.join(' + ');
    return `${total} <span class="wi_stat__breakdown">(${breakdown})</span>`;
  }
  return total;
});

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
    value="UZS"
    id="currency-uzs"
    :checked="currency === 'UZS'"
    @change="onCurrencyChange"
  />
  <label for="currency-uzs">UZS</label>
</div>





    <div class="wi_card wi_card--totals">
      <!-- <p class="wi_card__title">Totals</p> -->
      <div class="wi_stat">
        <span class="wi_stat__label">{{ config.labels?.applicant_type || 'Applicant type:' }}</span>
        <span class="wi_stat__value">{{
          summary.mode === "company" 
            ? (config.labels?.legal_entity || "Legal entity") 
            : (config.labels?.individual || "Individual")
        }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">{{ config.labels?.trademarks || 'Trademarks:' }}</span>
        <span class="wi_stat__value">{{ summary.trademarks }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">{{ config.labels?.total_classes || 'Total classes:' }}</span>
        <span class="wi_stat__value" v-html="formattedClasses"></span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">{{ config.labels?.state_fee_filing || 'State fee for filing:' }}</span>
        <span class="wi_stat__value">{{ formattedStateDutySubmit }} {{ currency }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">{{ config.labels?.state_fee_cert || 'State fee for TM certificate:' }}</span>
        <span class="wi_stat__value">{{ formattedStateDutyCert }} {{ currency }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">{{ config.labels?.total_state_fee || 'Total state fee:' }}</span>
        <span class="wi_stat__value">{{ formattedStateDuty }} {{ currency }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">{{ config.labels?.service || 'Service:' }}</span>
        <span class="wi_stat__value">{{ formattedService }} {{ currency }}</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">{{ config.labels?.total || 'Total:' }}<sup>*</sup></span>
        <span class="wi_stat__value">{{ formattedTotal }} {{ currency }}</span>
      </div>
    </div>
    <p class="wi_p-note">
      <sup>*</sup> {{ config.labels?.note_text || 'The stated price is for reference only and does not guarantee the final cost.' }}
    </p>
  </div>
</template>
