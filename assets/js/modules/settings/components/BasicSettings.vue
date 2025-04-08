<template>
  <div class="basic-settings-container">
    <div v-if="loading" class="d-flex justify-center ma-5">
      <v-progress-circular indeterminate color="primary" />
    </div>
    
    <template v-else>
      <v-card flat class="settings-card pa-4">
        <h2 class="text-h6 mb-4">Basic Settings</h2>
        <v-list>
          <v-list-item>
            <v-row align="center">
              <v-col cols="8">
                <div class="text-subtitle-1 font-weight-medium">Enable Tag Pilot</div>
                <div class="text-caption text-grey">Turn on/off the Tag Pilot tracking functionality</div>
              </v-col>
              <v-col cols="4" class="text-right">
                <v-switch
                  v-model="localSettings.state"
                  color="primary"
                  hide-details
                  inset
                />
              </v-col>
            </v-row>
          </v-list-item>
          
          <v-divider class="my-3" />
          
          <v-list-item>
            <v-row align="center">
              <v-col cols="8">
                <div class="text-subtitle-1 font-weight-medium">
                  Track User ID
                  <v-chip v-if="!isPro" color="warning" size="small" class="ml-2">PRO</v-chip>
                </div>
                <div class="text-caption text-grey">Allow user identification for better tracking accuracy</div>
                <a 
                  v-if="!isPro" 
                  :href="urls.externalLinks.pricing" 
                  target="_blank"
                  class="text-caption upgrade-link"
                >
                  <v-icon size="small" class="mr-1">mdi-arrow-up-bold-circle</v-icon>
                  Upgrade to PRO
                </a>
              </v-col>
              <v-col cols="4" class="text-right">
                <v-switch
                  v-model="localSettings.trackUserId"
                  :disabled="!isPro"
                  color="primary"
                  hide-details
                  inset
                />
              </v-col>
            </v-row>
          </v-list-item>
          
          <v-divider class="my-3" />
          
          <v-list-item>
            <v-row align="center">
              <v-col cols="8">
                <div class="text-subtitle-1 font-weight-medium">Debug Mode</div>
                <div class="text-caption text-grey">Enable detailed logs for troubleshooting</div>
              </v-col>
              <v-col cols="4" class="text-right">
                <v-switch
                  v-model="localSettings.debug"
                  color="primary"
                  hide-details
                  inset
                />
              </v-col>
            </v-row>
          </v-list-item>
        </v-list>
        
        <div v-if="eventsLoaded">
          <h2 class="text-h6 mt-6 mb-4">Tracking Events</h2>
          <v-divider class="mb-3" />
          
          <div v-if="eventsLoading" class="d-flex justify-center ma-4">
            <v-progress-circular indeterminate color="primary" :size="30" />
          </div>
          
          <v-list v-else>
            <v-list-item v-for="(event, index) in events" :key="index">
              <v-row align="center">
                <v-col cols="8">
                  <div class="text-subtitle-1 font-weight-medium">
                    {{ formatEventName(event.name) }}
                    <v-chip v-if="event.isPro && !isPro" color="warning" size="small" class="ml-2">PRO</v-chip>
                  </div>
                  <div class="text-caption text-grey">
                    Track {{ formatEventName(event.name).toLowerCase() }} events on your site
                  </div>
                  <a 
                    v-if="event.isPro && !isPro" 
                    :href="urls.externalLinks.pricing" 
                    target="_blank"
                    class="text-caption upgrade-link"
                  >
                    <v-icon size="small" class="mr-1">mdi-arrow-up-bold-circle</v-icon>
                    Upgrade to PRO
                  </a>
                </v-col>
                <v-col cols="4" class="text-right">
                  <v-switch
                    v-model="event.enabled"
                    :disabled="event.isPro && !isPro"
                    color="primary"
                    hide-details
                    inset
                  />
                </v-col>
              </v-row>
              <v-divider v-if="index < events.length - 1" class="my-3" />
            </v-list-item>
          </v-list>
        </div>
        
        <v-card-actions class="mt-8 px-0">
          <v-spacer />
          <v-btn
            color="primary"
            :loading="saving"
            :disabled="saving"
            @click="saveSettings"
            prepend-icon="mdi-content-save"
            variant="elevated"
            class="px-6"
          >
            Save Settings
          </v-btn>
        </v-card-actions>
      </v-card>
    </template>
    
    <v-snackbar
      v-model="showSuccess"
      color="success"
      :timeout="3000"
      location="top"
    >
      Settings saved successfully
      <template v-slot:actions>
        <v-btn
          variant="text"
          @click="showSuccess = false"
        >
          Close
        </v-btn>
      </template>
    </v-snackbar>

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
import { ref, reactive, onMounted, watch } from 'vue';
import { inject } from 'vue';
import urls from '../config/urls';

const apiService = inject('apiService');

const isPro = inject('isPro');

const loading = ref(true);
const saving = ref(false);
const showSuccess = ref(false);
const showError = ref(false);
const errorMessage = ref('');


const events = ref([]);
const eventsLoading = ref(false);
const eventsLoaded = ref(false);

const localSettings = reactive({
  state: false,
  trackUserId: false,
  debug: false
});


const formatEventName = (name) => {
  return name
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

const loadSettings = async () => {
  loading.value = true;
  try {
    const data = await apiService.getSettings('basic');
    Object.assign(localSettings, data);
    
    if (!isPro) {
      localSettings.trackUserId = false;
    }
  } catch (error) {
    errorMessage.value = error.message || 'Failed to load settings';
    showError.value = true;
  } finally {
    loading.value = false;
    loadEvents();
  }
};

const loadEvents = async () => {
  eventsLoading.value = true;
  try {
    const eventsList = await apiService.getEvents();
    events.value = eventsList.map(event => ({
      ...event,
      enabled: typeof event.enabled === 'string'
        ? event.enabled === '1' || event.enabled === 'true'
        : Boolean(event.enabled),
      isPro: typeof event.isPro === 'string'
        ? event.isPro === '1' || event.isPro === 'true'
        : Boolean(event.isPro)
    }));

    if (!isPro) {
      events.value.forEach(event => {
        if (event.isPro) {
          event.enabled = false;
        }
      });
    }
    
    eventsLoaded.value = true;
  } catch (error) {
    console.error('Error loading events:', error);
    errorMessage.value = error.message || 'Failed to load events';
    showError.value = true;
  } finally {
    eventsLoading.value = false;
  }
};

const saveSettings = async () => {
  saving.value = true;
  try {
    if (!isPro) {
      localSettings.trackUserId = false;
    }

    const settingsToSave = {
      ...localSettings
    };

    events.value.forEach(event => {
      const enabled = (event.isPro && !isPro) ? false : event.enabled;
      settingsToSave[`event_${event.name}`] = enabled;
    });

    await apiService.saveSettings('basic', settingsToSave);
    showSuccess.value = true;
  } catch (error) {
    errorMessage.value = error.message || 'Failed to save settings';
    showError.value = true;
  } finally {
    saving.value = false;
  }
};

watch(() => localSettings.trackUserId, (newValue) => {
  if (!isPro && newValue === true) {
    localSettings.trackUserId = false;
  }
});

watch(events, (newEvents) => {
  if (!isPro) {
    newEvents.forEach(event => {
      if (event.isPro && event.enabled) {
        event.enabled = false;
      }
    });
  }
}, { deep: true });

onMounted(() => {
  loadSettings();
});
</script>

<style scoped>
.settings-card {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
  background-color: #FFFFFF;
}

.v-list-item {
  padding: 12px 0;
}

.text-grey {
  color: #757575;
}

.font-weight-medium {
  font-weight: 500;
}

.upgrade-link {
  color: #FF9800;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  margin-top: 4px;
  font-weight: 500;
}

.upgrade-link:hover {
  text-decoration: underline;
}
</style> 