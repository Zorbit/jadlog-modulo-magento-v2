define([
  'jquery',
  'ko',
  'uiComponent',
  'Magento_Checkout/js/model/quote',
  'Jadlog_Embarcador/js/model/pudo-model'
], function($, ko, Component, quote, pudoModel) {
  'use strict';

  return Component.extend({
    defaults: {
      template: 'Jadlog_Embarcador/checkout/payment/pudo-reminder'
    },

    initialize: function(config) {
      this.selectedMethod = ko.computed(function() {
        var method = quote.shippingMethod();
        var selectedMethod = method != null ? method.carrier_code + '_' + method.method_code : null;
        return selectedMethod;
      }, this);

      this.shouldShowJadlogPickupInfo = ko.computed(function() {
        return (this.selectedMethod() == 'jadlog_pickup_jadlog_pickup');
      }, this);

      this.pudo_name = ko.observable();
      this.pudo_id = ko.observable();
      this.pudo_location = ko.observable();
      this.pudo_opening_hours = ko.observable();

      this.getMessage = ko.computed(function() {
        var pudoData = pudoModel.getData();
        if (pudoData) {
          this.pudo_name(JSON.parse(pudoData).Name);
          this.pudo_id(JSON.parse(pudoData).PudoId);
          this.pudo_location(JSON.parse(pudoData).Location);
          this.pudo_opening_hours(JSON.parse(pudoData).OpeningHours);
        }
        return true;
      }, this);

      this._super();
    },

  });
});