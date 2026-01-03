<script setup>
import { computed, reactive } from 'vue';
import WiFormInputs from './WiFormInputs.vue';
import WiFormSummary from './WiFormSummary.vue';
import WiFormStatusBar from './WiFormStatusBar.vue';
import ConfigPreview from './sections/ConfigPreview.vue';
import ProposalCallout from './sections/ProposalCallout.vue';

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

const state = reactive({
  mode: 'company',
  rows: [{ id: 'row-1', classes: 1 }],
  email: '',
  emailVisible: false,
  status: 'idle',
  statusMessage: '',
});

const summary = computed(() => {
  const cfg = props.config?.[state.mode] || {};
  const bucToUzs = Number(props.config?.buc_to_uzs) || 412000;
  const usdToUzs = Number(props.config?.usd_to_uzs) || 12000;
  const bucToUsd = bucToUzs / usdToUzs;

  let totalBUC = 0;
  let classes = 0;

  state.rows.forEach((row) => {
    const value = Number(row.classes);
    const clamped = Number.isFinite(value) && value >= 1 ? value : 1;
    classes += clamped;

    const extra = Math.max(clamped - 1, 0);
    const submitBUC = Number(cfg.submit_first || 0) + Number(cfg.submit_additional || 0) * extra;
    const certBUC = Number(cfg.cert_first || 0) + Number(cfg.cert_additional || 0) * extra;
    totalBUC += submitBUC + certBUC;
  });

  const stateDutyUSD = totalBUC * bucToUsd;
  const serviceUSD = (Number(cfg.service_per_tm_usd) || 0) * state.rows.length;
  const totalUSD = stateDutyUSD + serviceUSD;

  return {
    mode: state.mode,
    trademarks: state.rows.length,
    classes,
    totals: {
      stateDutyUSD: Math.round(stateDutyUSD),
      serviceUSD: Math.round(serviceUSD),
      totalUSD: Math.round(totalUSD),
    },
  };
});

function handleModeChange(nextMode) {
  state.mode = nextMode;
}

function handleRowsChange(nextRows) {
  state.rows = nextRows;
}

function handleEmailChange(nextEmail) {
  state.email = nextEmail;
}

function handleEmailVisibility(nextVisible) {
  state.emailVisible = nextVisible;
}

function handleEmailUpdate(nextEmail) {
  state.email = nextEmail;
}
</script>

<template>
  <div class="wi_root wi_shell" :data-instance-id="instanceId">
    <WiFormStatusBar
      v-if="state.status !== 'idle' || state.statusMessage"
      :status="state.status"
      :message="state.statusMessage"
    />

    <div class="wi_section">
      <h2 class="wi_section__heading">Inputs</h2>
      <WiFormInputs
        :mode="state.mode"
        :rows="state.rows"
        :config="config"
        @update:mode="handleModeChange"
        @update:rows="handleRowsChange"
      >
        <template #secondary>
          <WiFormSummary :summary="summary" />
          <ProposalCallout
            :email="state.email"
            :email-visible="state.emailVisible"
            @toggle-email="handleEmailVisibility"
            @update:email="handleEmailUpdate"
          />
          <ConfigPreview :config="config" />
        </template>
      </WiFormInputs>
    </div>
  </div>
</template>

