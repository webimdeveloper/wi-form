<script setup>
const props = defineProps({
  status: {
    type: String,
    default: 'idle',
  },
  message: {
    type: String,
    default: '',
  },
});
</script>

<template>
  <div class="wi_status" :class="{
    'wi_status--success': status === 'success',
    'wi_status--warn': status === 'warn',
    'wi_status--error': status === 'error',
  }">
    <span class="wi_stat__label">Status:</span>
    <span class="wi_stat__value">{{ status }}</span>
    <span v-if="message" class="wi_stat__label">— {{ message }}</span>
  </div>
</template>

