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
});

const formatter = new Intl.NumberFormat("en-US", {
  style: "currency",
  currency: "USD",
  minimumFractionDigits: 0, // Set to 2 if you want .00 always
  maximumFractionDigits: 0,
});

// Computed properties for each total
const formattedStateDuty = computed(() =>
  formatter.format(props.summary.totals.stateDutyUSD)
);
const formattedService = computed(() =>
  formatter.format(props.summary.totals.serviceUSD)
);
const formattedTotal = computed(() =>
  formatter.format(props.summary.totals.totalUSD)
);
</script>

<template>
  <div class="wi_summary">

<div class="wi_currency-toggle">
  <input type="radio" name="currency" value="USD" id="currency-usd" checked />
  <label for="currency-usd">USD</label>

  <input type="radio" name="currency" value="KZT" id="currency-kzt" />
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
        <span class="wi_stat__value">{{ formattedStateDuty }} USD</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Service:</span>
        <span class="wi_stat__value">{{ formattedService }} USD</span>
      </div>
      <div class="wi_stat">
        <span class="wi_stat__label">Total:<sup>*</sup></span>
        <span class="wi_stat__value">{{ formattedTotal }} USD</span>
      </div>
    </div>
    <p class="wi_p-note">
      <sup>*</sup> The stated price is for reference only and does not guarantee
      the final cost. The final price will be agreed upon and approved
      separately.
    </p>
  </div>
</template>
