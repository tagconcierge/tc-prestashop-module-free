{hook h='tcDisplayBeforePurchaseEvent' order_id=$tc_order_id}
{literal}
<script type="text/javascript" data-tag-concierge-scripts>
  (function(dataLayer, tagConcierge) {
    const order = JSON.parse(atob('{/literal}{base64_encode($tc_order|json_encode)}{literal}'));
    let event = {
      ...tagConcierge.eventBase(),
      event: 'purchase',
      ecommerce: {
        transaction_id: order.id,
        affiliation: order.affiliation,
        currency: order.currency,
        value: order.value,
        tax: order.tax,
        shipping: order.shipping,
        coupon: order.coupon,
        items: [],
      }
    };

    for (let product of order.products) {
      event.ecommerce.items.push({
        ...tagConcierge.mapProductToItem(product),
        quantity: product.order_quantity,
      });
    }

    dataLayer.push(event);
  })(dataLayer, tagConcierge);
</script>
{/literal}
