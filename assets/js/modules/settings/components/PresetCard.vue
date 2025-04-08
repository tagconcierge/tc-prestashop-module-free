<template>
  <div class="preset-card-wrapper">
    <v-card
      class="preset-card h-100"
      variant="outlined"
      :color="isSelected ? 'primary' : undefined"
      :class="{'selected-preset': isSelected}"
      :disabled="isDisabled"
      @click="onClick"
    >
      <v-card-item>
        <template v-slot:prepend>
          <v-icon 
            :icon="icon" 
            size="large" 
            class="mb-2" 
            :color="isSelected ? 'white' : 'primary'"
          ></v-icon>
        </template>
        <v-card-title class="text-subtitle-1 font-weight-medium">
          {{ preset.name }}
          <v-chip
            v-if="showProChip"
            size="x-small"
            color="warning"
            class="ml-2"
          >
            PRO
          </v-chip>
        </v-card-title>
      </v-card-item>
      <v-card-text class="text-body-2">
        {{ preset.description }}
      </v-card-text>
      <v-card-text v-if="preset.events && preset.events.length > 0" class="pt-0">
        <div class="text-caption font-weight-medium mb-1">Events:</div>
        <v-chip-group>
          <v-chip
            v-for="event in preset.events.slice(0, 3)"
            :key="event"
            size="x-small"
            color="primary"
            variant="tonal"
            class="mr-1 mb-1"
          >
            {{ event }}
          </v-chip>
          <v-chip
            v-if="preset.events.length > 3"
            size="x-small"
            color="grey"
            variant="tonal"
            class="mr-1 mb-1"
          >
            +{{ preset.events.length - 3 }} more
          </v-chip>
        </v-chip-group>
      </v-card-text>
      <v-card-text class="d-flex align-center justify-space-between pt-0">
        <div v-if="preset.type" class="text-caption">
          <v-chip
            size="x-small"
            :color="preset.type === 'basic' ? 'green' : 'blue'"
            variant="tonal"
          >
            {{ preset.type }}
          </v-chip>
          <v-chip
            size="x-small"
            color="grey"
            variant="tonal"
            class="ml-1"
          >
            v{{ preset.version }}
          </v-chip>
        </div>
        
        <!-- Przycisk Download dla presetów dostępnych w wersji FREE -->
        <div class="action-buttons" @click.stop>
          <v-btn
            v-if="!isDisabled || (preset.isServer && isPro)"
            size="small"
            variant="text"
            color="primary"
            icon
            @click.stop="downloadPreset"
            :loading="downloading"
            :disabled="downloading"
            class="download-btn"
          >
            <v-icon>mdi-download</v-icon>
            <v-tooltip activator="parent" location="top">Download preset configuration</v-tooltip>
          </v-btn>
        </div>
      </v-card-text>
    </v-card>
    
    <!-- Przycisk Upgrade dla presetów PRO w wersji FREE, pozycjonowany absolutnie -->
    <v-btn
      v-if="shouldShowUpgradeButton"
      size="x-small"
      variant="tonal"
      color="warning"
      class="upgrade-btn-absolute"
      @click.stop="openUpgradeLink"
    >
      <v-icon size="small" class="mr-1">mdi-arrow-up-bold-circle</v-icon>
      UPGRADE TO PRO
    </v-btn>
  </div>
  
  <!-- Snackbar notyfikacje -->
  <v-snackbar
    v-model="showSuccess"
    color="success"
    :timeout="3000"
    location="bottom"
  >
    Preset downloaded successfully
    <template v-slot:actions>
      <v-btn
        variant="text"
        @click="showSuccess = false"
      >
        Close
      </v-btn>
    </template>
  </v-snackbar>

  <v-snackbar
    v-model="showError"
    color="error"
    :timeout="5000"
    location="bottom"
  >
    {{ errorMessage }}
    <template v-slot:actions>
      <v-btn
        variant="text"
        @click="showError = false"
      >
        Close
      </v-btn>
    </template>
  </v-snackbar>
</template>

<script setup>
import { computed, ref, inject } from 'vue';
import urls from '../config/urls';
import { downloadObjectAsJson } from '../utils/fileUtils';

const apiService = inject('apiService');
const moduleVersion = inject('moduleVersion', '1.0.0');
const isPro = inject('isPro', false);

const downloading = ref(false);
const showSuccess = ref(false);
const showError = ref(false);
const errorMessage = ref('');

const props = defineProps({
  preset: {
    type: Object,
    required: true
  },
  isSelected: {
    type: Boolean,
    default: false
  },
  isDisabled: {
    type: Boolean,
    default: false
  },
  showProChip: {
    type: Boolean,
    default: false
  },
});

const emit = defineEmits(['click']);

const shouldShowUpgradeButton = computed(() => {
  if (isPro) return false;

  return props.isDisabled && !props.preset.isServer;
});

const internalIconMapping = {
  'ga4': 'mdi-google-analytics',
  'gtm': 'mdi-tag-multiple',
  'fbp': 'mdi-facebook',
  'tiktok': 'mdi-music-note',
  'pinterest': 'mdi-pinterest',
  'twitter': 'mdi-twitter',
  'snapchat': 'mdi-snapchat',
  'linkedin': 'mdi-linkedin',
  'gads': 'mdi-google',
  'microsoft': 'mdi-microsoft',
  'adwords': 'mdi-google',
  'bing': 'mdi-microsoft',
  'remarketing': 'mdi-target',
  'server': 'mdi-server-network',
  'cloud': 'mdi-cloud',
  'ecommerce': 'mdi-cart',
  'shopping': 'mdi-shopping',
  'checkout': 'mdi-cash-register',
  'stripe': 'mdi-currency-usd',
  'paypal': 'mdi-paypal',
  'consent': 'mdi-shield-check',
  'tag': 'mdi-tag',
  'default-server': 'mdi-server',
  'default': 'mdi-tag'
};

const icon = computed(() => {
  if (!props.preset.id) {
    return props.preset.isServer ? internalIconMapping['default-server'] : internalIconMapping.default;
  }

  for (const [key, icon] of Object.entries(internalIconMapping)) {
    if (props.preset.id.includes(key)) {
      return icon;
    }
  }

  return props.preset.isServer ? internalIconMapping['default-server'] : internalIconMapping.default;
});

const onClick = () => {
  if (!props.isDisabled) {
    emit('click', props.preset.id);
  }
};

const downloadPreset = async () => {
  if (!props.preset.id) return;
  
  downloading.value = true;
  try {
    const presetData = await apiService.downloadPreset(props.preset.id, moduleVersion);

    const fileName = props.preset.id.replace("presets/", "");
    downloadObjectAsJson(presetData, fileName);
    
    showSuccess.value = true;
  } catch (error) {
    console.error('Error downloading preset:', error);
    errorMessage.value = 'Failed to download preset. Please try again.';
    showError.value = true;
  } finally {
    downloading.value = false;
  }
};

const openUpgradeLink = (event) => {
  event.stopPropagation();

  window.open(urls.externalLinks.pricing, '_blank');
};
</script>

<style scoped>
.preset-card-wrapper {
  position: relative;
  height: 100%;
}

.h-100 {
  height: 100%;
}

.preset-card {
  cursor: pointer;
  transition: transform 0.2s, box-shadow 0.2s;
  border: 1px solid rgba(0, 0, 0, 0.12);
  display: flex;
  flex-direction: column;
  position: relative;
  height: 100%;
}

.preset-card:hover:not(.v-card--disabled):not(.selected-preset) {
  transform: translateY(-4px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
  border-color: rgba(25, 118, 210, 0.5);
}

.selected-preset {
  color: white;
}

.selected-preset .text-body-2 {
  color: rgba(255, 255, 255, 0.8) !important;
}

.v-card--disabled {
  opacity: 0.7;
  cursor: not-allowed;
}

.action-buttons {
  display: flex;
  justify-content: flex-end;
  align-items: center;
  margin-top: 4px;
}

.download-btn {
  min-width: unset;
}

.download-btn .v-icon {
  margin-right: 0;
}

.upgrade-btn-absolute {
  position: absolute;
  bottom: 16px;
  right: 16px;
  z-index: 100;
  font-size: 0.7rem;
  height: 24px;
  font-weight: 400;
  opacity: 0.85;
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
}

.upgrade-btn-absolute:hover {
  opacity: 1;
}

.upgrade-btn-absolute .v-icon {
  font-size: 0.9rem;
}
</style> 