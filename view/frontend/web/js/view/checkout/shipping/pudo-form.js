define([
  'jquery',
  'ko',
  'uiComponent',
  'Magento_Checkout/js/model/quote',
  'Magento_Checkout/js/model/shipping-rate-registry',
  'Magento_Checkout/js/model/cart/cache',
  'Jadlog_Embarcador/js/model/pudo-model',
  'Magento_Checkout/js/model/shipping-service',
  'Jadlog_Embarcador/js/view/checkout/shipping/pudo-service',
  'mage/translate',
  'Magento_Catalog/js/price-utils',
  'jquery/ui'
], function($, ko, Component, quote, rateRegistry, cartCache, pudoModel, shippingService, pudoService, t, priceUtils) {
  'use strict';

  return Component.extend({
    defaults: {
      template: 'Jadlog_Embarcador/checkout/shipping/pudo-form'
    },

    initialize: function(config) {
      this.pudos = ko.observableArray();
      this.selectedPudo = ko.observable();

      this.selectedPudoObject = ko.computed(function() {
        if(this.selectedPudo()) {
          return JSON.parse(this.selectedPudo());
        } else {
          return {};
        }
      }, this);

      this.selectedMethod = ko.computed(function() {
        var method = quote.shippingMethod();
        var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
        return selectedMethod;
      }, this);

      this.shouldShowJadlogPickupInfo = ko.computed(function() {
        return (this.selectedMethod() == 'jadlog_pickup_jadlog_pickup');
      }, this);

      this.shouldShowPopupJadlogPickup = ko.computed(function() {
        if (pudoModel.getReloading()) {
          return false;
        } else {
          return this.shouldShowJadlogPickupInfo();
        }
      }, this);

      this.showPopupJadlogPickup = function() {
        if (this.shouldShowPopupJadlogPickup()) {
          this.reloadPudos();
        }
      }

      this.formatPrice = function(price) {
        return priceUtils.formatPrice(price, quote.getPriceFormat());
      }

      this.reloadRates = function() {
        var address = quote.shippingAddress();

        if (address) {
          pudoModel.setReloading(true);
          //address.trigger_reload = new Date().getTime();

          rateRegistry.set(address.getKey(), null);
          rateRegistry.set(address.getCacheKey(), null);

          cartCache.clear('rates');

          quote.shippingAddress(address);
        }
      }

      this._super();

      pudoModel.setReloading(false);

      return this;
    },

    initObservable: function() {
      this._super();

      this.showPudoSelection = ko.computed(function() {
        return this.pudos().length != 0
      }, this);

      quote.shippingMethod.subscribe(function(method) {
        //var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
        //if (selectedMethod == 'jadlog_pickup_jadlog_pickup') {
        //  this.reloadPudos();
        //}
        if(pudoModel.getReloading()) {
          pudoModel.setReloading(false);
        } else {
          this.showPopupJadlogPickup();
        }
      }, this);

      this.selectedPudo.subscribe(function(pudo) {
        pudoModel.setData(pudo);
        pudoService.setPudo(pudoModel.sanitized());
      });

      return this;
    },

    setPudoList: function(list) {
      this.pudos(list);
    },

    reloadPudos: function() {
      pudoService.getPudoList(quote.shippingAddress(), this);
      var defaultPudo = this.pudos()[0];
      if (defaultPudo) {
        this.selectedPudo(defaultPudo.id);
      }
      $("#popup-jadlog-pickup").modal("openModal");
    },
/*
    getPudo: function() {
      var pudo;
      if (this.selectedPudo()) {
        for (var i in this.pudos()) {
          var m = this.pudos()[i];
          if (m.id == this.selectedPudo().id) {
            pudo = m;
          }
        }
      } else {
        pudo = this.pudos()[0];
      }
      return pudo;
    },
    initSelector: function() {
      var startPudo = this.getPudo();
    },

    getPudoValue: function() {
      return this.selectedPudo();
    }
*/
  });
});
