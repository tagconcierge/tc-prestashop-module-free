{literal}
<script type="text/javascript" data-tag-concierge-scripts>
  (function(dataLayer, tagConcierge) {
    tagConcierge.on('cartUpdated', (cart) => {
      const mapProduct = (product) => {
        let attributes = [];
        for (let attribute in product.attributes) {
          attributes.push(`${attribute.trim().toLowerCase()}_${product.attributes[attribute].trim().toLowerCase()}`)
        }

        return {
          id: parseInt(product['id']),
          name: product['name'],
          price: product['price_with_reduction'],
          brand: product['manufacturer_name'],
          category: product['category'],
          variant: attributes.join('___'),
          stock_quantity: parseInt(product['stock_quantity']),
          minimal_quantity: parseInt(product['minimal_quantity']),
          cart_quantity: parseInt(product['cart_quantity']),
        }
      };

      let products = [];

      for (let localCartProduct of tagConcierge.prestashopCart.products) {
        let productExists = false;
        for (let cartProduct of cart.products) {
          let sameProduct = cartProduct.id === localCartProduct.id
            && cartProduct.attributes_small === localCartProduct.attributes_small;
          if (false === sameProduct) {
            continue;
          }

          productExists = true;

          let localCartProductQuantity = parseInt(localCartProduct.quantity);
          let cartProductQuantity = parseInt(cartProduct.quantity);
          let quantityDiff = cartProductQuantity - localCartProductQuantity;

          if (0 === quantityDiff) {
            break;
          }

          if (0 < quantityDiff) {
            let product = mapProduct(localCartProduct);
            products.push({
              product: product,
              quantity: quantityDiff
            });
            break;
          }
        }
      }

      for (let cartProduct of cart.products) {
        let productExists = false;
        for (let localCartProduct of tagConcierge.prestashopCart.products) {
          let sameProduct = cartProduct.id === localCartProduct.id
            && cartProduct.attributes_small === localCartProduct.attributes_small;
          if (false === sameProduct) {
            continue;
          }

          productExists = true;
        }

        if (false === productExists) {
          let product = mapProduct(cartProduct);
          products.push({
            product: product,
            quantity: cartProduct.cart_quantity
          });
        }
      }

      if (0 === products.length) {
        return;
      }

      for (let product of products) {
        let event = {
          ...tagConcierge.eventBase(),
          event: 'add_to_cart',
        };

        event.ecommerce.items.push({...tagConcierge.mapProductToItem(product.product), quantity: product.quantity});
        event.ecommerce.value = tagConcierge.getProductsValue(event);

        dataLayer.push(event);
      }
    });
  })(dataLayer, tagConcierge);
</script>
{/literal}
