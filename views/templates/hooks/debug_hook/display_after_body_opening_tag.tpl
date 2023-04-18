<style>
    #tc-debug {
        padding: 20px;
        border-top: 1px solid #000;
        position: fixed;
        bottom: 0;
        z-index: 9999;
        width: 100%;
        max-height: 30%;
        background: #fff;
        text-align: center;
        overflow: scroll;
    }

    #tc-debug p {
        font-weight: bold;
        color: #000;
        cursor: pointer;
    }

    #tc-debug .header {
        width: 100%;
        font-weight: bold;
        font-size: 16px;
        text-transform: uppercase;
        display: inline-block;
    }

    #tc-debug .app-link {
        margin-bottom: 20px;
        font-weight: bold;
        font-size: 16px;
        text-transform: uppercase;
        color: red;
    }

    #tc-debug .app-link a {
        color: red;
        text-decoration: underline;
    }

    #tc-debug #clear {
        cursor: pointer;
        color: red;
        margin-top: -4px;
    }

    #tc-debug .events {
        margin-top: 20px;
    }

    #tc-debug .events.hidden {
        display: none;
    }

    .swal2-container {
        z-index: 99999
    }

    #tc-debug .header #hide {
        position: absolute;
        display: inline-block;
        right: 10px;
        cursor: pointer;
        font-size: .7em;
    }

    #tc-debug .header #hide .up {
        display: none;
    }

    #tc-debug .header #hide.down .up {
        display: inline-block;
    }

    #tc-debug .header #hide.down .down {
        display: none;
    }
</style>
<div id="tc-debug">
    <div class="header">
        Debug console <i id="clear" class="material-icons">delete</i>
        <span id="hide">
            <span class="up">
                show<i class="material-icons">arrow_upward</i>
            </span>
            <span class="down">
                hide<i class="material-icons">arrow_downward</i>
            </span>
    </div>
    <div class="events hidden">

    </div>
</div>
{literal}
<script>
  (function(dataLayer) {
    let dataLayerIndex = 0;

    function checkDataLayer() {
      let currentDataLayerLength = dataLayer.length || 0;
      if (currentDataLayerLength > dataLayerIndex) {
        let newDataLayer = dataLayer.slice(dataLayerIndex);
        dataLayerIndex = currentDataLayerLength;
        let existingStoredEvents = JSON.parse(sessionStorage.getItem('TC_DEBUG_CONSOLE')) || [];

        newDataLayer.map(function(event) {
          if ((!event.event && !event.ecommerce) || (event.event && event.event.substring(0, 4) === "gtm.")) {
            return
          }
          existingStoredEvents.push(event);
        });
        sessionStorage.setItem('TC_DEBUG_CONSOLE', JSON.stringify(existingStoredEvents));

        const eventList = document.querySelector('#tc-debug .events');
        eventList.innerHTML = '';
        for (let existingStoredEvent of existingStoredEvents) {
          renderItem(existingStoredEvent)
        }
      }
    }

    function renderItem(data) {
      let eventName = data.event;
      if (!data.event && data.ecommerce && data.ecommerce.purchase) {
        eventName = "Purchase (UA)";
      }

      if (!data.event && data.ecommerce && data.ecommerce.impressions) {
        eventName = "Product Impression (UA)";
      }

      if (!data.event && data.ecommerce && data.ecommerce.detail) {
        eventName = "Product Detail (UA)";
      }

      if (data.event === "addToCart") {
        eventName = "addToCart (UA)";
      }

      if (data.event === "productClick") {
        eventName = "productClick (UA)";
      }

      if (data.event === "removeFromCart") {
        eventName = "removeFromCart (UA)";
      }

      if (data.event === "checkout") {
        eventName = "checkout (UA)";
      }

      const count = document.querySelectorAll('#tc-debug .events p').length;
      const tag = document.createElement('p');
      const text = document.createTextNode(`${count +1 }. ${eventName}`);
      tag.appendChild(text);
      tag.addEventListener('click', function() {
        alert(JSON.stringify(data, null, 2));
      });
      const eventList = document.querySelector('#tc-debug .events');

      eventList.prepend(tag);
    }

    let existingStoredEvents = JSON.parse(sessionStorage.getItem("TC_DEBUG_CONSOLE")) || [];
    for (let existingStoredEvent of existingStoredEvents) {
      renderItem(existingStoredEvent)
    }
    const eventList = document.querySelector('#tc-debug .events');
    eventList.classList.remove('hidden');
    setInterval(checkDataLayer, 100);
  })(dataLayer);
</script>
{/literal}
<script>
    const clearButton = document.querySelector('#tc-debug #clear');
    const hideButton = document.querySelector('#tc-debug #hide');

    clearButton.addEventListener('click', () => {
        sessionStorage.removeItem('TC_DEBUG_CONSOLE');
        const events = document.querySelectorAll('#tc-debug .events p');
        for (let e of events) {
            e.remove();
        }
    })

    hideButton.addEventListener('click', function () {
      const eventList = document.querySelector('#tc-debug .events');
      this.classList.toggle('down');
      eventList.classList.toggle('hidden');
    });
</script>
