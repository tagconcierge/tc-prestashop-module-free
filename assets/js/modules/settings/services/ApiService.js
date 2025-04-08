import axios from 'axios';
import urls from '../config/urls';

class ApiService {
  constructor() {
    this.adminLink = null;
    this.externalApiUrl = urls.api.base;
    this.moduleVersion = null;
    this.instanceUuid = null;
    this.isPro = false;
  }

  init(adminLink, moduleVersion = null, instanceUuid = null, isPro = false) {
    this.adminLink = adminLink;
    this.moduleVersion = moduleVersion;
    this.instanceUuid = instanceUuid;
    this.isPro = isPro;
  }

  async getSettings(section) {
    try {
      const response = await axios.get(`${this.adminLink}&action=GetSettings&ajax=1&section=${section}`);
      if (!response.data.success) {
        throw new Error(response.data.message || `Failed to load ${section} settings`);
      }
      return response.data.data;
    } catch (error) {
      console.error(`Error fetching ${section} settings:`, error);
      throw error;
    }
  }

  async getEvents() {
    try {
      const response = await axios.get(`${this.adminLink}&action=GetEvents&ajax=1`);
      if (!response.data.success) {
        throw new Error(response.data.message || 'Failed to load events');
      }
      return response.data.data;
    } catch (error) {
      console.error('Error fetching events:', error);
      throw error;
    }
  }

  async saveSettings(section, settings) {
    try {
      const formData = new URLSearchParams();

      Object.entries(settings).forEach(([key, value]) => {
        formData.append(key, typeof value === 'boolean' ? (value ? '1' : '0') : value);
      });

      formData.append('section', section);

      const response = await axios.post(`${this.adminLink}&action=SaveSettings&ajax=1`, formData, {
        headers: {
          'Content-Type': 'application/x-www-form-urlencoded'
        }
      });
      
      if (!response.data.success) {
        throw new Error(response.data.message || `Failed to save ${section} settings`);
      }
      
      return response.data;
    } catch (error) {
      console.error(`Error saving ${section} settings:`, error);
      throw error;
    }
  }

  async downloadPreset(presetId) {
    try {
      const response = await axios.post(`${urls.api.base}/preset`, {
        preset: presetId,
        uuid: this.instanceUuid,
        version: this.moduleVersion
      }, {
        headers: {
          'Content-Type': 'application/json'
        }
      });
      
      return response.data;
    } catch (error) {
      console.error('Error downloading preset:', error);
      throw error;
    }
  }

  async getPresets(container = null) {
    try {
      let url = urls.api.presets;
      let params = new URLSearchParams();
      
      if (container) {
        params.append('container', container);
      } else if (this.isPro) {
        params.append('filter', 'advanced');
      }
      
      const queryString = params.toString();
      if (queryString) {
        url += `?${queryString}`;
      }
      
      const response = await axios.get(url);
      return response.data;
    } catch (error) {
      console.error('Error fetching presets:', error);
      throw error;
    }
  }
}
export default new ApiService(); 