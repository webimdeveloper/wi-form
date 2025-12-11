import { createApp } from 'vue';
import WiFormRoot from './components/WiFormRoot.vue';
import './style.css';

function parseConfig(el) {
  const raw = el.getAttribute('data-wiform-config');
  if (!raw) {
    return null;
  }

  try {
    return JSON.parse(raw);
  } catch (err) {
    console.error('WiForm: failed to parse data-wiform-config', err);
    return null;
  }
}

function mountInstances() {
  const roots = document.querySelectorAll('.wiform-root');

  roots.forEach((el, index) => {
    const config = parseConfig(el) || window.wiformTrademarkSettings || {};
    const instanceId = el.getAttribute('data-wiform-id') || `wiform-${index + 1}`;

    const app = createApp(WiFormRoot, {
      config,
      instanceId,
    });

    app.mount(el);
  });
}

if (document.readyState === 'loading') {
  document.addEventListener('DOMContentLoaded', mountInstances);
} else {
  mountInstances();
}

