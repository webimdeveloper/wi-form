<script setup>
const props = defineProps({
  email: {
    type: String,
    default: '',
  },
  emailVisible: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits(['toggle-email', 'update:email']);

const isValidEmail = (value) => {
  if (!value) return false;
  const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return re.test(String(value).toLowerCase());
};
</script>

<template>
  <div class="wi_section" style="margin-top: var(--wi_space-md);">
    <h2 class="wi_section__heading">Get a Commercial Proposal</h2>
    <div class="wi_card">
      <p class="wi_stat__label">
        Enter your email to receive a commercial proposal. The email must be valid to proceed.
      </p>

      <div v-if="!emailVisible" class="wi_actions" style="margin-top: var(--wi_space-sm);">
        <button type="button" class="wi_actions__add" @click="emit('toggle-email', true)">
          Get a Commercial Proposal
        </button>
      </div>

      <div v-else class="wi_section" style="margin-top: var(--wi_space-sm); gap: var(--wi_space-xs);">
        <div class="wi_row">
          <div class="wi_row__control">
            <input
              type="email"
              :value="email"
              placeholder="you@example.com"
              :class="{ wi_error: email && !isValidEmail(email) }"
              @input="emit('update:email', $event.target.value)"
            />
          </div>
        </div>
        <div class="wi_actions">
          <button
            class="wi_actions__add"
            type="button"
            :disabled="!isValidEmail(email)"
          >
            Send proposal
          </button>
          <button type="button" @click="emit('toggle-email', false)">
            Hide
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

