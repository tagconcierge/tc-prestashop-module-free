import { createRouter, createWebHashHistory } from 'vue-router';
import BasicSettings from './components/BasicSettings.vue';
import GtmSettings from './components/GtmSettings.vue';
import SupportSettings from './components/SupportSettings.vue';
import GtmPresets from './components/GtmPresets.vue';
import GtmServerSide from './components/GtmServerSide.vue';
import GtmServerPresets from './components/GtmServerPresets.vue';

const routes = [
  {
    path: '/settings/basic',
    component: BasicSettings
  },
  {
    path: '/settings/gtm',
    component: GtmSettings
  },
  {
    path: '/settings/presets',
    component: GtmPresets
  },
  {
    path: '/settings/server',
    component: GtmServerSide
  },
  {
    path: '/settings/server-presets',
    component: GtmServerPresets
  },
  {
    path: '/settings/support',
    component: SupportSettings
  },
  {
    path: '/settings',
    redirect: '/settings/basic'
  },
  {
    path: '/',
    redirect: '/settings/basic'
  }
];

const router = createRouter({
  history: createWebHashHistory(),
  routes
});

export default router; 