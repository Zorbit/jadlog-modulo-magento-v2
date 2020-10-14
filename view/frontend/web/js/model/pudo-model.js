define([
  'ko',
  'underscore',
  'domReady!'
], function(ko, _) {
  'use strict';

  var data = ko.observable();
  var reloading = ko.observable();

  return {
    data: data,
    reloading: reloading,

    getData: function() {
      return data();
    },

    setData: function(x) {
      data(x);
    },

    getReloading: function() {
      return reloading();
    },

    setReloading: function(x) {
      reloading(x);
    },

    sanitized: function() {
      var j_pudo = JSON.parse(data());
      delete j_pudo['OpeningHours'];
      delete j_pudo['Latitude'];
      delete j_pudo['Longitude'];
      delete j_pudo['Distance'];
      return JSON.stringify(j_pudo);
    }
  };
});