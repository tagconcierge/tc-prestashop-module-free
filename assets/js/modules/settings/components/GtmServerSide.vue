<template>
  <div class="gtm-server-side-container">
    <div v-if="loading" class="d-flex justify-center ma-5">
      <v-progress-circular indeterminate color="primary" />
    </div>
    
    <template v-else>
      <v-card flat class="settings-card pa-4">
        <div class="text-h6 mb-4">
          GTM Server Container
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
          Specify details of your GTM Server-Side container to enable Server Side Tracking.
        </div>
        
        <div v-if="!isPro" class="mb-4">
          <a 
            :href="urls.externalLinks.pricing" 
            target="_blank"
            class="upgrade-link"
          >
            <v-icon size="small" class="mr-1">mdi-arrow-up-bold-circle</v-icon>
            Upgrade to PRO to access Server-Side GTM features
          </a>
        </div>
        
        <v-form ref="form" v-model="isFormValid">
          <div class="mb-4">
            <div class="text-subtitle-1 font-weight-medium mb-2">
              GTM Server Container URL
            </div>
            <v-text-field
              v-model="localSettings.serverContainerUrl"
              placeholder="https://metrics-demo-prestashop.tagpilot.io/"
              variant="outlined"
              class="mb-3"
              hint="The full url of you GTM Server Container."
              persistent-hint
              :disabled="!isPro"
              :rules="urlRules"
              @update:model-value="validateServerSettings"
            />
          </div>

          <div class="mb-4">
            <div class="text-subtitle-1 font-weight-medium mb-2">
              GA4 Client Activation Path
            </div>
            <v-text-field
              v-model="localSettings.ga4ClientActivationPath"
              placeholder="/mp"
              variant="outlined"
              hint="Path of the GA4 client in the server container. Presets use /mp."
              persistent-hint
              :disabled="!isPro"
            />
          </div>

          <div class="mb-4">
            <div class="text-subtitle-1 font-weight-medium mb-2">
              X-Gtm-Server-Preview header
            </div>
            <v-text-field
              v-model="localSettings.gtmServerPreviewHeader"
              placeholder="header value"
              variant="outlined"
              hint="Paste the preview header from GTM to see server events in Preview. Leave empty in production."
              persistent-hint
              :disabled="!isPro"
            />
          </div>
          
          <div class="mb-4">
            <v-divider class="my-4"></v-divider>
            <div class="text-h6 mb-4">
              Load gtm.js from sGTM
              <v-chip
                v-if="!isPro"
                color="warning"
                size="small"
                class="ml-2"
              >
                PRO
              </v-chip>
            </div>
            <div class="text-body-2 text-grey mb-4">
              Modify the gtm.js installation snippet to be loaded from 1st party domain of your sGTM container.
            </div>
            
            <v-list>
              <v-list-item>
                <v-row align="center">
                  <v-col cols="8">
                    <div class="text-subtitle-1 font-weight-medium">Enable</div>
                    <div class="text-caption text-grey">Will change public GTM domain to sGTM custom domain provided in the settings above.</div>
                  </v-col>
                  <v-col cols="4" class="text-right">
                    <v-switch
                      v-model="localSettings.loadGtmFromServerContainer"
                      color="primary"
                      hide-details
                      inset
                      :disabled="!isPro || !isValidServerUrl"
                    />
                  </v-col>
                </v-row>
              </v-list-item>
            </v-list>
            <div v-if="serverUrlWarning" class="mt-2 text-caption error--text">
              {{ serverUrlWarning }}
            </div>
          </div>

          <div class="mb-4">
            <v-divider class="my-4"></v-divider>
            <div class="text-h6 mb-4">
              Purchase queue
              <v-chip
                v-if="!isPro"
                color="warning"
                size="small"
                class="ml-2"
              >
                PRO
              </v-chip>
            </div>
            <div class="text-body-2 text-grey mb-4">
              Optional. When off, purchase is sent immediately after the order is paid. When on, the shop request does not wait for the server container. A cron job sends queued orders.
            </div>
            <v-list>
              <v-list-item>
                <v-row align="center">
                  <v-col cols="8">
                    <div class="text-subtitle-1 font-weight-medium">Send purchase in the background</div>
                    <div class="text-caption text-grey">The checkout request does not wait for the server container.</div>
                  </v-col>
                  <v-col cols="4" class="text-right">
                    <v-switch
                      v-model="localSettings.serverPurchaseBackground"
                      color="primary"
                      hide-details
                      inset
                      :disabled="!isPro"
                    />
                  </v-col>
                </v-row>
              </v-list-item>
            </v-list>
            <div v-if="localSettings.serverPurchaseBackground && serverPurchaseCronUrl" class="text-caption text-grey mt-2">
              Call this URL every minute, or add the module cron task in PrestaShop Cron jobs:
              <div class="mt-1">{{ serverPurchaseCronUrl }}</div>
            </div>
          </div>
        </v-form>
        
        <v-card-actions class="mt-8 px-0">
          <v-spacer />
          <v-btn
            color="primary"
            :loading="saving"
            :disabled="saving || !isFormValid"
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
import { ref, reactive, onMounted, watch, computed } from 'vue';
import { inject } from 'vue';
import urls from '../config/urls';

const apiService = inject('apiService');
const isPro = inject('isPro', false);
const serverPurchaseCronUrl = inject('serverPurchaseCronUrl', '');

const loading = ref(true);
const saving = ref(false);
const showSuccess = ref(false);
const showError = ref(false);
const errorMessage = ref('');
const form = ref(null);
const isFormValid = ref(true);
const serverUrlWarning = ref('');

const urlRules = [
  v => !v || /^https?:\/\/[-a-zA-Z0-9@:%._+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_+.~#?&//=]*)$/.test(v) || 'Please enter a valid URL starting with http:// or https://'
];

const localSettings = reactive({
  serverContainerUrl: '',
  loadGtmFromServerContainer: false,
  ga4ClientActivationPath: '/mp',
  gtmServerPreviewHeader: '',
  serverPurchaseBackground: false
});

const isValidServerUrl = computed(() => {
  if (!localSettings.serverContainerUrl) {
    return false;
  }
  
  return /^https?:\/\/[-a-zA-Z0-9@:%._+~#=]{2,256}\.[a-z]{2,6}\b([-a-zA-Z0-9@:%_+.~#?&//=]*)$/.test(localSettings.serverContainerUrl);
});

const validateServerSettings = () => {
  if (localSettings.loadGtmFromServerContainer && !isValidServerUrl.value) {
    localSettings.loadGtmFromServerContainer = false;
    serverUrlWarning.value = 'You must provide a valid server container URL to enable loading GTM from server';
  } else {
    serverUrlWarning.value = '';
  }
};

watch(() => localSettings.serverContainerUrl, () => {
  validateServerSettings();
});

watch(isPro, (newValue) => {
  if (!newValue) {
    localSettings.serverContainerUrl = '';
    localSettings.loadGtmFromServerContainer = false;
  }
}, { immediate: true });

const loadSettings = async () => {
  loading.value = true;
  try {
    const data = await apiService.getSettings('server_side');
    if (data && isPro) {
      localSettings.serverContainerUrl = data.serverContainerUrl || '';
      localSettings.loadGtmFromServerContainer = data.loadGtmFromServerContainer || false;
      localSettings.ga4ClientActivationPath = data.ga4ClientActivationPath || '/mp';
      localSettings.gtmServerPreviewHeader = data.gtmServerPreviewHeader || '';
      localSettings.serverPurchaseBackground = data.serverPurchaseBackground || false;

      if (localSettings.loadGtmFromServerContainer && !isValidServerUrl.value) {
        localSettings.loadGtmFromServerContainer = false;
      }
    } else {
      localSettings.serverContainerUrl = '';
      localSettings.loadGtmFromServerContainer = false;
      localSettings.ga4ClientActivationPath = '/mp';
      localSettings.gtmServerPreviewHeader = '';
      localSettings.serverPurchaseBackground = false;
    }
  } catch (error) {
    errorMessage.value = error.message || 'Failed to load settings';
    showError.value = true;
  } finally {
    loading.value = false;
  }
};

const saveSettings = async () => {
  if (!isFormValid.value) {
    return;
  }

  if (localSettings.loadGtmFromServerContainer && !isValidServerUrl.value) {
    localSettings.loadGtmFromServerContainer = false;
    serverUrlWarning.value = 'You must provide a valid server container URL to enable loading GTM from server';
    return;
  }
  
  saving.value = true;
  try {
    const settingsToSave = isPro
      ? localSettings
      : {
        serverContainerUrl: '',
        loadGtmFromServerContainer: false,
        ga4ClientActivationPath: '/mp',
        gtmServerPreviewHeader: '',
        serverPurchaseBackground: false
      };
    
    await apiService.saveSettings('server_side', settingsToSave);
    showSuccess.value = true;
  } catch (error) {
    errorMessage.value = error.message || 'Failed to save settings';
    showError.value = true;
  } finally {
    saving.value = false;
  }
};

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

.error--text {
  color: var(--v-theme-error) !important;
}
</style> 