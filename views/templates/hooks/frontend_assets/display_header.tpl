<script type="text/javascript" data-tag-concierge-scripts>
  window.tagConciergeItemIdConfig = {$tc_item_id_config|default:'{"itemIdSource":"id","itemIdPattern":""}' nofilter};
</script>
{literal}
<script type="text/javascript" data-tag-concierge-scripts>
  window.dataLayer = window.dataLayer || [];
  window.tagConcierge = {
    /*
     * empty cart bug, fixed in PrestaShop 1.7.8.0
     */
    originalXhrOpen: XMLHttpRequest.prototype.open,
    lastPrestashopCartFromResponse: null,
    lastViewedProduct: null,
    prestashopCart: { ...prestashop.cart },
    eventListeners: {},
    config: window.tagConciergeItemIdConfig || { itemIdSource: 'id', itemIdPattern: '' },
    resolveItemId: (product) => {
      const config = window.tagConcierge.config || { itemIdSource: 'id', itemIdPattern: '' };
      const source = config.itemIdSource || 'id';
      const pattern = (config.itemIdPattern || '').trim();
      const id = null != product.id ? String(product.id) : '';
      const variantId = null != product.variant_id ? String(product.variant_id) : '0';
      const sku = (product.sku || '').toString().trim();
      const variantSku = (product.variant_sku || '').toString().trim();
      const resolvedSku = '' !== sku ? sku : id;
      const resolvedVariantSku = '' !== variantSku ? variantSku : resolvedSku;

      if ('sku' === source || ('pattern' === source && '' === pattern)) {
        return resolvedSku;
      }

      if ('variant_sku' === source) {
        return resolvedVariantSku;
      }

      if ('pattern' === source) {
        return pattern
          .replace(/\{variant_id\}/g, variantId)
          .replace(/\{variant_sku\}/g, resolvedVariantSku)
          .replace(/\{sku\}/g, resolvedSku)
          .replace(/\{id\}/g, id);
      }

      return id;
    },
    mapProductToItem: (product) => {
      return {
        item_id: window.tagConcierge.resolveItemId(product),
        item_name: product.name,
        price: parseFloat(product.price),
        item_brand: product.brand,
        item_category: product.category,
        item_variant: product.variant,
        quantity: product.minimal_quantity,
      };
    },
    eventBase: () => {
      return {
        event: null,
        ecommerce: {
          currency: prestashop.currency.iso_code,
          items: [],
        }
      };
    },
    getProductsValue: (event) => {
      let value = 0;
      for (let item of event.ecommerce.items) {
        value += item.price * item.quantity;
      }
      return value.toFixed(2);
    },
    on: (event, callback) => {
      if (false === window.tagConcierge.eventListeners.hasOwnProperty(event)) {
        window.tagConcierge.eventListeners[event] = [];
      }

      window.tagConcierge.eventListeners[event].push(callback);
    },
    dispatch: (event, data) => {
      if (false === window.tagConcierge.eventListeners.hasOwnProperty(event)) {
       return;
      }

      window.tagConcierge.eventListeners[event].forEach((callback) => {
        callback(data);
      });
    }
  };

  if ('undefined' === typeof window.tagConcierge.prestashopCart.products) {
      window.tagConcierge.prestashopCart.products = [];
  }

  /*
   * empty cart bug, fixed in PrestaShop 1.7.8.0
   */
  XMLHttpRequest.prototype.open = function () {
    this.addEventListener('load', function () {
      try {
        let response = JSON.parse(this.responseText);
        if (undefined === response.cart) {
          return;
        }
        window.tagConcierge.lastPrestashopCartFromResponse = response.cart;
      } catch (e) {
      }
    });
    window.tagConcierge.originalXhrOpen.apply(this, arguments);
  };
</script>
{/literal}
{hook h='tcDisplayAfterFrontendAssets'}