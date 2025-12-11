<script setup>
const props = defineProps({
  config: {
    type: Object,
    default: () => ({}),
  },
});
</script>

<template>
  <div class="wi_section" style="margin-top: var(--wi_space-md);">
    <h2 class="wi_section__heading">Config preview</h2>
    <div class="wi_card">
      <pre style="white-space: pre-wrap; margin: 0;">{{ JSON.stringify(config, null, 2) }}</pre>
    </div>
  </div>
</template>

