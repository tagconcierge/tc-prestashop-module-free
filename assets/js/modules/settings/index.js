import { createApp } from 'vue';
import { createVuetify } from 'vuetify';
import App from './App.vue';
import router from './router';
import ApiService from './services/ApiService';
import * as components from 'vuetify/components';
import * as directives from 'vuetify/directives';
import { aliases, mdi } from 'vuetify/iconsets/mdi';
import 'vuetify/styles';


const app = createApp(App);

const vuetify = createVuetify({
  components,
  directives,
  icons: {
    defaultSet: 'mdi',
    aliases,
    sets: {
      mdi,
    },
  },
  theme: {
    defaultTheme: 'light',
    themes: {
      light: {
        colors: {
          primary: '#1976D2',
          secondary: '#424242',
          accent: '#82B1FF',
          error: '#FF5252',
          info: '#2196F3',
          success: '#4CAF50',
          warning: '#FFC107',
        },
      },
    },
  },
});

const appElement = document.getElementById('tag-concierge-settings-app');
const adminLink = appElement.getAttribute('data-admin-link');
const isPro = appElement.getAttribute('data-pro') === 'true';
const moduleName = appElement.getAttribute('data-module-name') || 'Tag Pilot';
const moduleVersion = appElement.getAttribute('data-module-version') || '0.0.0';
const instanceUuid = appElement.getAttribute('data-instance-uuid') || '00000000-0000-0000-0000-000000000000';

ApiService.init(adminLink, moduleVersion, instanceUuid, isPro);

app.provide('apiService', ApiService);
app.provide('isPro', isPro);
app.provide('moduleName', moduleName);
app.provide('moduleVersion', moduleVersion);
app.provide('instanceUuid', instanceUuid);

app.use(router);
app.use(vuetify);

app.mount('#tag-concierge-settings-app');