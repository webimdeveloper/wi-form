(function ($) {
  // DEPRECATED: legacy jQuery implementation kept for parity testing.
  // New Vue-based implementation lives in `assets/js/frontend/*` and
  // calculations have been moved to `assets/js/frontend/lib/calculations/trademark.js`.
  // Remove this file after verification.
  $(function () {
    var $root = $('.wiform-trademark-calculator');

    if (!$root.length) {
      return;
    }

    //
    // SETTINGS — safe defaults + merge with PHP localized data
    //
    function getSettings() {
      var defaults = {
        buc_to_uzs: 412000,
        usd_to_uzs: 12000,
        company: {
          submit_first: 6.0,
          submit_additional: 1.0,
          cert_first: 11.6,
          cert_additional: 4.0,
          service_per_tm_usd: 200.0
        },
        private: {
          submit_first: 4.0,
          submit_additional: 0.5,
          cert_first: 6.8,
          cert_additional: 1.0,
          service_per_tm_usd: 200.0
        }
      };

      if (typeof window.wiformTrademarkSettings === 'object') {
        return $.extend(true, {}, defaults, window.wiformTrademarkSettings);
      }

      return defaults;
    }

    function formatUSD(num) {
      return Math.round(num).toLocaleString(undefined, {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
      });
    }

    // Get initial settings
    var settings = getSettings();
    var bucToUzs = Number(settings.buc_to_uzs) || 412000;
    var usdToUzs = Number(settings.usd_to_uzs) || 12000;
    var bucToUsd = bucToUzs / usdToUzs;

    //
    // INITIAL UI STATE
    //
    // Hide email field until calculation is done
    $root.find('.wiform-field--email').hide();
    $root.find('.wiform-results').hide();

    //
    // REPEATER LOGIC
    //

    // Add new row
    $root.on('click', '.wiform-add-row', function () {
      var $container = $root.find('.wiform-trademarks');
      var $first = $container.find('.wiform-trademark-row').first();
      var $clone = $first.clone();

      $clone.find('.wiform-input-classes').val(1);
      $clone.find('.wiform-remove-row').show();

      $container.append($clone);

      // Ensure first row's remove button stays hidden
      $first.find('.wiform-remove-row').hide();
    });

    // Remove row
    $root.on('click', '.wiform-remove-row', function () {
      $(this).closest('.wiform-trademark-row').remove();

      var $rows = $root.find('.wiform-trademark-row');
      if ($rows.length === 1) {
        $rows.first().find('.wiform-remove-row').hide();
      }
    });

    //
    // CALCULATION LOGIC
    //
    $root.on('click', '.wiform-calculate', function () {
      // Re-read settings in case we want to support dynamic updates later
      settings = getSettings();
      bucToUzs = Number(settings.buc_to_uzs) || 412000;
      usdToUzs = Number(settings.usd_to_uzs) || 12000;
      bucToUsd = bucToUzs / usdToUzs;

      var mode = $root.find('input[name="wiform_customer_type"]:checked').val() || 'company';
      var config = settings[mode];

      if (!config) {
        console.error('Missing config for mode', mode, settings);
        alert('Configuration error. Please contact site administrator.');
        return;
      }

      var submitFirst = Number(config.submit_first);
      var submitAdditional = Number(config.submit_additional);
      var certFirst = Number(config.cert_first);
      var certAdditional = Number(config.cert_additional);
      var serviceUSD = Number(config.service_per_tm_usd);

      var totalBUC = 0;
      var totalClasses = 0;

      var $rows = $root.find('.wiform-trademark-row');
      var trademarkCount = $rows.length;

      if (trademarkCount === 0) {
        alert('Please specify at least one trademark.');
        return;
      }

      var hasError = false;

      $rows.each(function () {
        var classes = Number($(this).find('.wiform-input-classes').val());
        if (isNaN(classes) || classes < 1) {
          hasError = true;
          return false; // break loop
        }

        totalClasses += classes;

        var extra = classes - 1;
        if (extra < 0) extra = 0;

        var submitBUC = submitFirst + submitAdditional * extra;
        var certBUC = certFirst + certAdditional * extra;

        totalBUC += (submitBUC + certBUC);
      });

      if (hasError) {
        alert('Please enter a valid number of classes (minimum 1) for each trademark.');
        return;
      }

      // State duty in USD
      var stateDutyUSD = totalBUC * bucToUsd;

      // Service: per trademark
      var serviceTotalUSD = serviceUSD * trademarkCount;

      // Final total
      var totalUSD = stateDutyUSD + serviceTotalUSD;

      //
      // PRINT RESULTS
      //
      var $res = $root.find('.wiform-results');
      $res.show();

      $res.find('.wiform-result-trademarks')
        .text('Number of trademarks: ' + trademarkCount);

      $res.find('.wiform-result-classes')
        .text('Number of classes: ' + totalClasses);

      $res.find('.wiform-result-state-duty')
        .text('State duty: ' + formatUSD(stateDutyUSD) + ' USD');

      $res.find('.wiform-result-service')
        .text('Service: ' + formatUSD(serviceTotalUSD) + ' USD');

      $res.find('.wiform-result-total')
        .text('Total: ' + formatUSD(totalUSD) + ' USD');

      // Show email field only after a successful calculation
      $root.find('.wiform-field--email').show();
    });
  });
})(jQuery);