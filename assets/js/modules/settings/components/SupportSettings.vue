<template>
  <div class="support-settings-container">
    <v-card flat class="settings-card pa-4">
      <div class="text-h6 mb-4">Get Help & Support</div>
      <div class="text-body-2 text-grey mb-6">
        Find the resources you need to get the most out of {{ moduleName }}
      </div>
      
      <v-row>
        <v-col cols="12" sm="6" md="4">
          <v-card
            :href="urls.externalLinks.documentation"
            target="_blank"
            rel="noopener"
            class="support-card h-100"
            variant="elevated"
            elevation="2"
          >
            <v-card-item>
              <template v-slot:prepend>
                <v-icon color="primary" size="large" class="mb-2">mdi-book-open-page-variant</v-icon>
              </template>
              <v-card-title class="text-subtitle-1 font-weight-medium">Documentation</v-card-title>
            </v-card-item>
            <v-card-text class="text-body-2">
              Access our comprehensive documentation to learn more about {{ moduleName }} features and configuration.
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="4">
          <v-card
            :href="urls.externalLinks.services"
            target="_blank"
            rel="noopener"
            class="support-card h-100"
            variant="elevated"
            elevation="2"
          >
            <v-card-item>
              <template v-slot:prepend>
                <v-icon color="primary" size="large" class="mb-2">mdi-lifebuoy</v-icon>
              </template>
              <v-card-title class="text-subtitle-1 font-weight-medium">Custom Services</v-card-title>
            </v-card-item>
            <v-card-text class="text-body-2">
              Explore our custom server-side GTM migration, training and other professional services.
            </v-card-text>
          </v-card>
        </v-col>

        <v-col cols="12" sm="6" md="4">
          <v-card
            :href="urls.externalLinks.contact"
            class="support-card h-100"
            variant="elevated"
            elevation="2"
          >
            <v-card-item>
              <template v-slot:prepend>
                <v-icon color="primary" size="large" class="mb-2">mdi-email</v-icon>
              </template>
              <v-card-title class="text-subtitle-1 font-weight-medium">Contact Support</v-card-title>
            </v-card-item>
            <v-card-text class="text-body-2">
              Need help? Contact our support team directly for assistance with your {{ moduleName }} setup.
            </v-card-text>
          </v-card>
        </v-col>
      </v-row>
      
      <div class="mt-8 pt-4 border-top">
        <div class="text-subtitle-1 font-weight-medium mb-2">Version Information</div>
        <v-table density="compact" class="version-table">
          <tbody>
            <tr>
              <td class="text-left font-weight-medium">{{ moduleName }} Version:</td>
              <td class="text-right">{{ moduleVersion }}</td>
            </tr>
            <tr>
              <td class="text-left font-weight-medium">Edition:</td>
              <td class="text-right">{{ isPro ? 'PRO' : 'FREE' }}</td>
            </tr>
          </tbody>
        </v-table>
      </div>
    </v-card>
  </div>
</template>

<script setup>
import { inject } from 'vue';
import urls from '../config/urls';

const moduleName = inject('moduleName', 'Tag Pilot');
const moduleVersion = inject('moduleVersion', '1.0.0');
const isPro = inject('isPro', false);
</script>

<style scoped>
.settings-card {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05) !important;
  background-color: #FFFFFF;
}

.support-card {
  height: 100%;
  text-decoration: none;
  transition: transform 0.2s, box-shadow 0.2s;
  border-radius: 8px;
}

.support-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1) !important;
}

.text-grey {
  color: #757575;
}

.font-weight-medium {
  font-weight: 500;
}

.h-100 {
  height: 100%;
}

.border-top {
  border-top: 1px solid rgba(0, 0, 0, 0.1);
}

.version-table {
  background-color: transparent !important;
  box-shadow: none !important;
}
</style> 