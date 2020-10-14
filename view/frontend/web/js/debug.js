var Debug = {

  printObject: function (o) {
    // Note: cache should not be re-used by repeated calls to JSON.stringify.
    var cache = [];
    var ret = JSON.stringify(o, function(key, value) {
        if (typeof value === 'object' && value !== null) {
            if (cache.indexOf(value) !== -1) {
                // Duplicate reference found, discard key
                return;
            }
            // Store value in our collection
            cache.push(value);
        }
        return value;
    }, 2);
    cache = null; // Enable garbage collection
    return ret;
  }
}