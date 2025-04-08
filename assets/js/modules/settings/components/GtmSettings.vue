<template>
  <div class="gtm-settings-container">
    <div v-if="loading" class="d-flex justify-center ma-5">
      <v-progress-circular indeterminate color="primary" />
    </div>
    
    <template v-else>
      <v-card flat class="settings-card pa-4">
        <div class="text-h6 mb-4">Google Tag Manager Configuration</div>
        <div class="text-body-2 text-grey mb-6">
          Configure your GTM container snippets to enable tracking on your website
        </div>
        
        <v-form>
          <div class="mb-4">
            <div class="text-subtitle-1 font-weight-medium mb-2">GTM Head Snippet</div>
            <div class="text-caption text-grey mb-2">Paste the code that should be placed in the &lt;head&gt; of your website</div>
            <v-textarea
              v-model="localSettings.gtmContainerSnippetHead"
              placeholder="<!-- Google Tag Manager -->"
              rows="4"
              class="font-family-monospace code-area"
              variant="outlined"
              bg-color="#f5f5f5"
              auto-grow
              hide-details
            />
          </div>
          
          <div class="mb-6">
            <div class="text-subtitle-1 font-weight-medium mb-2">GTM Body Snippet</div>
            <div class="text-caption text-grey mb-2">Paste the code that should be placed after the opening &lt;body&gt; tag</div>
            <v-textarea
              v-model="localSettings.gtmContainerSnippetBody"
              placeholder="<!-- Google Tag Manager (noscript) -->"
              rows="4"
              class="font-family-monospace code-area"
              variant="outlined"
              bg-color="#f5f5f5"
              auto-grow
              hide-details
            />
          </div>
        </v-form>
        
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
    
    <!-- Always render notifications outside of conditionals -->
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
import { ref, reactive, onMounted } from 'vue';
import { inject } from 'vue';

const apiService = inject('apiService');

const loading = ref(true);
const saving = ref(false);
const showSuccess = ref(false);
const showError = ref(false);
const errorMessage = ref('');

const localSettings = reactive({
  gtmContainerSnippetHead: '',
  gtmContainerSnippetBody: ''
});

const loadSettings = async () => {
  loading.value = true;
  try {
    const data = await apiService.getSettings('gtm_installation');
    Object.assign(localSettings, data);
  } catch (error) {
    errorMessage.value = error.message || 'Failed to load settings';
    showError.value = true;
  } finally {
    loading.value = false;
  }
};

const saveSettings = async () => {
  saving.value = true;
  try {
    await apiService.saveSettings('gtm_installation', localSettings);
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

.font-family-monospace {
  font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Consolas', 'source-code-pro', monospace;
}

.text-grey {
  color: #757575;
}

.font-weight-medium {
  font-weight: 500;
}

.code-area {
  border-radius: 4px;
  font-size: 14px;
}
</style> 