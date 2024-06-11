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
    mapProductToItem: (product) => {
      return {
        item_id: product.id,
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
