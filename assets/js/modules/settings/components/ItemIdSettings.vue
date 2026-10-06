<template>
  <div class="item-id-settings-container">
    <div v-if="loading" class="d-flex justify-center ma-5">
      <v-progress-circular indeterminate color="primary" />
    </div>

    <template v-else>
      <v-card flat class="settings-card pa-4">
        <div class="text-h6 mb-4">
          Item ID
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
          Choose which value is sent as item_id in ecommerce events. The default is the PrestaShop product ID.
        </div>

        <div v-if="!isPro" class="mb-4">
          <a
            :href="urls.externalLinks.pricing"
            target="_blank"
            class="upgrade-link"
          >
            <v-icon size="small" class="mr-1">mdi-arrow-up-bold-circle</v-icon>
            Upgrade to PRO to choose the item ID source
          </a>
        </div>

        <v-form ref="form" v-model="isFormValid">
          <div class="mb-4">
            <div class="text-subtitle-1 font-weight-medium mb-2">
              Item ID source
            </div>
            <v-select
              v-model="localSettings.itemIdSource"
              :items="itemIdSourceOptions"
              item-title="title"
              item-value="value"
              variant="outlined"
              :disabled="!isPro"
              hint="Used for every ecommerce event."
              persistent-hint
            />
          </div>

          <div v-if="localSettings.itemIdSource === 'pattern'" class="mb-4">
            <div class="text-subtitle-1 font-weight-medium mb-2">
              Pattern
            </div>
            <v-text-field
              v-model="localSettings.itemIdPattern"
              placeholder="abc_{id}_{sku}"
              variant="outlined"
              :disabled="!isPro"
              hint="Placeholders: {id}, {variant_id}, {sku}, {variant_sku}. An empty pattern behaves like Main SKU."
              persistent-hint
            />
          </div>

          <v-alert
            type="info"
            variant="tonal"
            class="mt-2"
          >
            <div class="text-body-2">
              Empty variant SKU falls back to the main SKU. Empty main SKU falls back to the PrestaShop ID.
            </div>
            <div class="text-body-2 mt-2">
              Example for product 12, variant 34, SKU MAIN, variant SKU VAR:
              <strong>{{ preview }}</strong>
            </div>
          </v-alert>
        </v-form>

        <v-card-actions class="mt-8 px-0">
          <v-spacer />
          <v-btn
            color="primary"
            :loading="saving"
            :disabled="saving || !isFormValid || !isPro"
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
import { ref, reactive, computed, onMounted, inject } from 'vue';
import urls from '../config/urls';

const apiService = inject('apiService');
const isPro = inject('isPro', false);

const loading = ref(true);
const saving = ref(false);
const showSuccess = ref(false);
const showError = ref(false);
const errorMessage = ref('');
const isFormValid = ref(true);

const itemIdSourceOptions = [
  { title: 'PrestaShop ID', value: 'id' },
  { title: 'Main SKU', value: 'sku' },
  { title: 'Variant SKU', value: 'variant_sku' },
  { title: 'Custom pattern', value: 'pattern' },
];

const localSettings = reactive({
  itemIdSource: 'id',
  itemIdPattern: '',
});

const preview = computed(() => {
  const id = '12';
  const variantId = '34';
  const sku = 'MAIN';
  const variantSku = 'VAR';
  const resolvedSku = sku || id;
  const resolvedVariantSku = variantSku || resolvedSku;
  const source = localSettings.itemIdSource || 'id';
  const pattern = (localSettings.itemIdPattern || '').trim();

  if (source === 'sku' || (source === 'pattern' && pattern === '')) {
    return resolvedSku;
  }

  if (source === 'variant_sku') {
    return resolvedVariantSku;
  }

  if (source === 'pattern') {
    return pattern
      .replace(/\{variant_id\}/g, variantId)
      .replace(/\{variant_sku\}/g, resolvedVariantSku)
      .replace(/\{sku\}/g, resolvedSku)
      .replace(/\{id\}/g, id);
  }

  return id;
});

const loadSettings = async () => {
  loading.value = true;
  try {
    const data = await apiService.getSettings('item_id');
    if (data && isPro) {
      localSettings.itemIdSource = data.itemIdSource || 'id';
      localSettings.itemIdPattern = data.itemIdPattern || '';
    } else {
      localSettings.itemIdSource = 'id';
      localSettings.itemIdPattern = '';
    }
  } catch (error) {
    errorMessage.value = error.message || 'Failed to load settings';
    showError.value = true;
  } finally {
    loading.value = false;
  }
};

const saveSettings = async () => {
  if (!isPro || !isFormValid.value) {
    return;
  }

  saving.value = true;
  try {
    await apiService.saveSettings('item_id', {
      itemIdSource: localSettings.itemIdSource,
      itemIdPattern: localSettings.itemIdPattern || '',
    });
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
</style>
