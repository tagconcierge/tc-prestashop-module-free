<template>
  <div class="gtm-presets-container">
    <div v-if="loading" class="d-flex justify-center ma-5">
      <v-progress-circular indeterminate color="primary" />
    </div>
    
    <template v-else>
      <v-card flat class="settings-card pa-4">
        <div class="text-h6 mb-4">Google Tag Manager Presets</div>
        <div class="text-body-2 text-grey mb-6">
          Choose from predefined tag templates to quickly set up common tracking scenarios
        </div>
        
        <v-row>
          <v-col cols="12" sm="6" md="4" v-for="preset in processedPresets" :key="preset.id">
            <preset-card 
              :preset="preset"
              :is-disabled="preset.locked"
              :show-pro-chip="preset.locked"
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

const apiService = inject('apiService');

const loading = ref(true);
const showError = ref(false);
const errorMessage = ref('');
const apiPresets = ref([]);

const processedPresets = computed(() => {
  return apiPresets.value.map(preset => ({
    ...preset,
    isServer: false
  }));
});

const fetchPresets = async () => {
  loading.value = true;
  try {
    apiPresets.value = await apiService.getPresets();
  } catch (error) {
    errorMessage.value = 'Failed to load presets from the server. Please try again later.';
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
</style> 