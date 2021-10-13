import { createApp } from 'vue';
import App from '@/views/layouts/app';
import BalmUI from 'balm-ui';
import BalmUIPlus from 'balm-ui/dist/balm-ui-plus';

const app = createApp(App);
app.use(BalmUI);
app.use(BalmUIPlus);
app.mount('#app');