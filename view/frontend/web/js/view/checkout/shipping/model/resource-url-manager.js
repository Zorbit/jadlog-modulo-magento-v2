define(
  [
    'Magento_Customer/js/model/customer',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'mageUtils'
  ],
  function(customer, quote, urlBuilder, utils) {
    "use strict";
    return {
      getUrlForGetPudoList: function(quote, limit) {
        var params = {
          postcode: quote.shippingAddress().postcode,
          city: quote.shippingAddress().city
        };
        var urls = {
          'default': '/jadlog_embarcador/get-pudo-list/:postcode/:city'
        };
        return this.getUrl(urls, params);
      },

      getUrlForSetPudo: function() {
        var params = {};
        var urls = {
          'default': '/jadlog_embarcador/set-pudo'
        };
        return this.getUrl(urls, params);
      },

      /** Get url for service */
      getUrl: function(urls, urlParams) {
        var url;

        if (utils.isEmpty(urls)) {
          return 'Provided service call does not exist.';
        }

        if (!utils.isEmpty(urls['default'])) {
          url = urls['default'];
        } else {
          url = urls[this.getCheckoutMethod()];
        }
        return urlBuilder.createUrl(url, urlParams);
      },

      getCheckoutMethod: function() {
        return customer.isLoggedIn() ? 'customer' : 'guest';
      }
    };
  }
);