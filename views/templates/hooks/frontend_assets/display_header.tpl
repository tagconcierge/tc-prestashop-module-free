<script>
    window.dataLayer = window.dataLayer || [];
    window.tagConcierge = {
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

        return value;
      }
    };

    /*
     * empty cart bug, fixed in PrestaShop 1.7.8.0
     */
    window.tagConcierge.origOpen = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function () {
      this.addEventListener('load', function () {
        try {
          let response = JSON.parse(this.responseText);

          if (undefined === response.cart) {
            return;
          }

          window.lastPrestashopCartFromResponse = response.cart;
        } catch (e) {

        }
      });
      window.tagConcierge.origOpen.apply(this, arguments);
    };
</script>
