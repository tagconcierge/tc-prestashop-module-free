<template>
  <div class="gtm-server-presets-container">
    <div v-if="loading" class="d-flex justify-center ma-5">
      <v-progress-circular indeterminate color="primary" />
    </div>
    
    <template v-else>
      <v-card flat class="settings-card pa-4">
        <div class="text-h6 mb-4">
          Server-Side GTM Templates
          <v-chip
            v-if="!isPro"
            color="warning"
            size="small"
            class="ml-2"
          >
            PRO
          </v-chip>
        </div>
        <div class="text-body-2 text-grey mb-6">
          Choose from predefined server-side GTM configurations for your specific needs
        </div>
        
        <div v-if="!isPro" class="mb-4">
          <a 
            :href="urls.externalLinks.pricing" 
            target="_blank"
            class="upgrade-link"
          >
            <v-icon size="small" class="mr-1">mdi-arrow-up-bold-circle</v-icon>
            Upgrade to PRO to access Server-Side GTM templates
          </a>
        </div>
        
        <v-row>
          <v-col cols="12" sm="6" md="4" v-for="preset in processedPresets" :key="preset.id">
            <preset-card 
              :preset="preset"
              :is-disabled="!isPro || preset.locked"
              :show-pro-chip="preset.locked || !isPro"
            />
          </v-col>
        </v-row>
      </v-card>
    </template>
    
    <v-dialog
      v-model="showError"
      max-width="500"
    >
      <v-card>
        <v-card-title class="text-h5">
          Error
        </v-card-title>
        <v-card-text>
          {{ errorMessage }}
        </v-card-text>
        <v-card-actions>
          <v-spacer />
          <v-btn
            color="primary"
            variant="text"
            @click="showError = false"
          >
            Close
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { inject } from 'vue';
import PresetCard from './PresetCard.vue';
import urls from '../config/urls';

const apiService = inject('apiService');
const isPro = inject('isPro', false);

const loading = ref(true);
const showError = ref(false);
const errorMessage = ref('');
const apiPresets = ref([]);

const processedPresets = computed(() => {
  return apiPresets.value.map(preset => ({
    ...preset,
    isServer: true
  }));
});

const fetchPresets = async () => {
  loading.value = true;
  try {
    apiPresets.value = await apiService.getPresets('server');
  } catch (error) {
    errorMessage.value = 'Failed to load server presets from the server. Please try again later.';
    showError.value = true;
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchPresets();
});
</script>

<style scoped>
.settings-card {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
  background-color: #FFFFFF;
}

.text-grey {
  color: #757575;
}

.font-weight-medium {
  font-weight: 500 !important;
}

.upgrade-link {
  display: inline-flex;
  align-items: center;
  color: var(--v-theme-warning);
  text-decoration: none;
  font-weight: 500;
  font-size: 0.875rem;
  padding: 6px 12px;
  border-radius: 4px;
  background-color: rgba(var(--v-theme-warning), 0.08);
  transition: background-color 0.2s;
}

.upgrade-link:hover {
  background-color: rgba(var(--v-theme-warning), 0.12);
}
</style> 