<div class="panel">
    <div class="panel-heading">
        <i class="icon icon-plug"></i> GTM Presets
    </div>
    <div class="form-wrapper">
        <div id="gtm-ecommerce-woo-presets-loader" style="text-align: center;">
            <span class="spinner is-active" style="float: none;"></span>
        </div>

        <div id="gtm-ecommerce-woo-presets-grid">
            <div id="gtm-ecommerce-woo-preset-tmpl" class="preset" style="display: none;">

                <div class="col-md-4 m-2">
                    <div class="panel">
                        <p class="card-title name">Google Analytics 4</p><span class="recently-added" style="float:right; display: none;">recently added</span>
                        <div class="card-body">
                            <p class="description">Description</p>
                            <p>
                                <b>Supported eCommerce events:</b> <span class="events-count">2</span>
                            </p>
                            <p><a class="download btn btn-primary" href="#">Download</a></p><p>Version: <span class="version">N/A</span> <div class="upgrade" style="text-align: center; display: none;"><a class="button button-primary" href="https://tagconcierge.com/tag-concierge-for-prestashop" target="_blank">Upgrade to PRO</a></div></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br />
        <div id="gtm-ecommerce-woo-presets-upgrade" style="text-align: center; display: none;">
{*            <a class="button button-primary" href="https://tagconcierge.com/tag-concierge-for-prestashop" target="_blank">Upgrade to PRO</a>*}
        </div>
    </div>
    <div class="panel-footer" style="clear: both"></div>
</div>
    <script>
    (function($) {

    function downloadObjectAsJson(exportObj, exportName) {
        var dataStr = "data:text/json;charset=utf-8," + encodeURIComponent(JSON.stringify(exportObj));
        var downloadAnchorNode = document.createElement('a');
        downloadAnchorNode.setAttribute("href",     dataStr);
        downloadAnchorNode.setAttribute("download", exportName + ".json");
        document.body.appendChild(downloadAnchorNode); // required for firefox
        downloadAnchorNode.click();
        downloadAnchorNode.remove();
      }

    function getPresets() {
        return $.ajax({
            url: '{$tc_presets_url}',
        }).then(function(res) {
            return res;
        });
    }

    jQuery(function($) {
        var presetTemplateHtml = $("#gtm-ecommerce-woo-preset-tmpl").html();
        var $presetsGrid = $("#gtm-ecommerce-woo-presets-grid");

        getPresets()
            .then(function (res) {
                $("#gtm-ecommerce-woo-presets-loader").css("display", "none");
                var locked = false;
                res.map(function (preset) {
                    var $preset = $(presetTemplateHtml);
                    $(".name", $preset).text(preset.name);
                    $(".description", $preset).text(preset.description);

                    if (preset.locked !== true) {
                        $(".download", $preset).attr("data-id", preset.id);
                    } else {
                        locked = true;
                        $(".download", $preset).attr("disabled", "disabled");
                        $(".download", $preset).attr("title", "Requires PRO version, upgrade below.");
                        $(".upgrade", $preset).show();
                    }

                    if (true === preset.latest) {
                      $($preset).toggleClass('latest');
                    }
                    $(".events-count", $preset).text((preset.events || []).length);

                    $(".version", $preset).text(preset.version || "N/A");
                    $presetsGrid.append($preset);
                });
                // if something is locked then we show the button
                if (locked === true) {
                    $("#gtm-ecommerce-woo-presets-upgrade").css("display", "block");
                }
            })
            .then(function() {
                $(".events-list", $presetsGrid).click(function(ev) {
                    $(ev.currentTarget).pointer("open");
                });
                $(".download", $presetsGrid).click(function(ev) {
                    ev.preventDefault();
                    var preset = $(ev.currentTarget).attr("data-id");

                    return $.ajax({
                        method: 'POST',
                        url: 'https://api.tagconcierge.com/v2/preset',
                        contentType: "application/json",
                        dataType: "json",
                        data: JSON.stringify({
                            preset: preset,
                            uuid: "{$instance_uuid}",
                            version: "{$plugin_version}"
                        })
                    }).then(function(res) {
                        downloadObjectAsJson(res, preset.replace("presets/", ""));
                    });
                });
            })
    });
})(jQuery);
</script>
<style>
    #gtm-ecommerce-woo-presets-grid .latest .panel {
        border-color: rgb(6, 147, 45) !important;
        border-width: 3px !important;
    }
    #gtm-ecommerce-woo-presets-grid .latest .panel .recently-added {
        display: block !important;
        color: rgb(6, 147, 45);
        font-weight: bold;
        position: absolute;
        top: 20px;
        right: 20px;
    }
</style>
