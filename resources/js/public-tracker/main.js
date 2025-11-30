import { createApp } from 'vue';
import TrackerApp from './TrackerApp.vue';

const mountEl = document.getElementById('order-tracker-app');

if (mountEl) {
    const app = createApp(TrackerApp, {
        endpoint: mountEl.dataset.endpoint || '/api/orders/track',
    });

    app.mount(mountEl);
}
