{literal}
<script type="text/javascript" data-tag-concierge-scripts>
  (function(prestashop, tagConcierge) {
    const subscribePrestashop = (prestashop) => {
      if ('function' !== typeof prestashop.on) {
        setTimeout(subscribePrestashop, 500, prestashop);
        return;
      }
      prestashop.on('updateCart', (d) => {
        let data = {...d };
        try {
          if (undefined === data.resp || undefined === data.resp.cart) {
            //bug will be fixed in Prestashop 1.7.8.0
            if (null === tagConcierge.lastPrestashopCartFromResponse) {
              const xhr = new XMLHttpRequest();

              xhr.open('POST', '/index.php?fc=module&module=tagconcierge&controller=ajax&ajax=true', false);
              xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
              xhr.send('action=getCart');

              if (xhr.status === 200) {
                data = {
                  resp: {
                    cart: JSON.parse(xhr.responseText)
                  }
                }
              }
            } else {
              data = {
                resp: {
                  cart: tagConcierge.lastPrestashopCartFromResponse
                }
              }
            }
          }
        } catch (e) {
          console.error(`Tag Concierge - ${e.message}.`);
          return;
        }

        if (undefined === data.resp || undefined === data.resp.cart) {
          console.error('Tag Concierge - unable to acquire cart data.')
          return;
        }

        tagConcierge.dispatch('cartUpdated', data.resp.cart);

        tagConcierge.prestashopCart = {...data.resp.cart};
      });
    }

    subscribePrestashop(prestashop);
  })(prestashop, tagConcierge);
</script>
{/literal}
