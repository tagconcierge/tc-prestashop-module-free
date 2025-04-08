<template>
  <v-app>
    <v-main class="fill-height pa-0">
      <v-container fluid class="fill-height pa-0">
        <v-row class="fill-height ma-0">
          <v-col cols="3" md="2" lg="2" xl="2" class="pa-0 navigation-col">
            <v-card flat class="fill-height rounded-0 navigation-card">
              <div class="py-5 px-4 app-title">
                <img :src="urls.externalLinks.logo" alt="Tag Pilot" class="logo-img mb-2" />
              </div>
              
              <v-divider></v-divider>
              
              <v-list class="navigation-list">
                <v-list-item
                  v-for="tab in tabs"
                  :key="tab.value"
                  :value="tab.value"
                  :active="currentTab === tab.value"
                  @click="navigateToTab(tab.value)"
                  color="primary"
                  class="navigation-item"
                  rounded="lg"
                >
                  <template v-slot:prepend>
                    <v-icon :icon="tab.icon" class="nav-icon"></v-icon>
                  </template>
                  
                  <v-list-item-title>{{ tab.title }}</v-list-item-title>
                </v-list-item>
              </v-list>
              
              <div class="pa-4 mt-auto version-info">
                <div class="text-caption text-grey">
                  {{ moduleName }} v{{ moduleVersion }}
                </div>
              </div>
            </v-card>
          </v-col>

          <v-col cols="9" md="10" lg="10" xl="10" class="pa-6">
            <router-view />
          </v-col>
        </v-row>
      </v-container>
    </v-main>
  </v-app>
</template>

<script setup>
import { computed, onMounted, inject, ref, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import urls from './config/urls';

const router = useRouter();
const route = useRoute();

const moduleName = inject('moduleName', 'Tag Pilot');
const moduleVersion = inject('moduleVersion', '1.0.0');

const tabs = [
  { value: 'basic', title: 'Basic Settings', icon: 'mdi-cog-outline', path: '/settings/basic' },
  { value: 'gtm', title: 'GTM Configuration', icon: 'mdi-code-tags', path: '/settings/gtm' },
  { value: 'presets', title: 'GTM Presets', icon: 'mdi-tag-multiple', path: '/settings/presets' },
  { value: 'server', title: 'Server-Side GTM', icon: 'mdi-server-network', path: '/settings/server' },
  { value: 'server-presets', title: 'GTM Server Presets', icon: 'mdi-server-plus', path: '/settings/server-presets' },
  { value: 'support', title: 'Support', icon: 'mdi-help-circle-outline', path: '/settings/support' },
];

const activeTab = ref('basic');

const updateActiveTab = () => {
  const hash = window.location.hash.replace('#', '');

  for (const tab of tabs) {
    if (hash === tab.path) {
      activeTab.value = tab.value;
      return;
    }
  }

  for (const tab of tabs) {
    if (hash.startsWith(tab.path)) {
      activeTab.value = tab.value;
      return;
    }
  }

  activeTab.value = 'basic';
};

const currentTab = computed(() => activeTab.value);

const navigateToTab = (tabValue) => {
  const tab = tabs.find(tab => tab.value === tabValue);
  if (tab) {
    router.push(tab.path);
  }
};

watch(() => route.fullPath, () => {
  updateActiveTab();
}, { immediate: true });

watch(() => window.location.hash, () => {
  updateActiveTab();
});

onMounted(() => {
  updateActiveTab();

  const hash = window.location.hash.replace('#', '');
  
  if (hash === '' || hash === '/' || hash === '/settings') {
    router.push(tabs[0].path);
  } else {
    let validTab = false;
    for (const tab of tabs) {
      if (hash === tab.path || hash.startsWith(tab.path + '/')) {
        validTab = true;
        break;
      }
    }
    
    if (!validTab) {
      router.push(tabs[0].path);
    }
  }
});
</script>

<style>
.fill-height {
  height: 100vh !important;
}

.navigation-col {
  border-right: 1px solid rgba(0, 0, 0, 0.08);
}

.rounded-0 {
  border-radius: 0 !important;
}

.navigation-card {
  display: flex;
  flex-direction: column;
  background-color: #f8f9fa !important;
}

.navigation-list {
  padding: 12px;
}

.navigation-item {
  margin-bottom: 4px;
  height: 48px;
  transition: background-color 0.2s;
}

.navigation-item.v-list-item--active {
  background-color: #e3f2fd !important;
}

.navigation-item:not(.v-list-item--active):hover {
  background-color: rgba(0, 0, 0, 0.04) !important;
}

.app-title {
  text-align: center;
}

.logo-img {
  max-width: 80%;
  height: auto;
  margin: 0 auto;
}

.text-grey {
  color: #757575;
}

.version-info {
  border-top: 1px solid rgba(0, 0, 0, 0.08);
}

.nav-icon {
  margin-right: 8px;
}
</style>