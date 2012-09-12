if(typeof EventSource == 'undefined') {
    EventSource = function(url, _delay) {
        _delay = _delay || 1111;
        if(!String.prototype.trim) {
            String.prototype.trim = function() {
                return this.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
            };
        }
        this.onmessage = function() {};
        this.onopen = function() {};
        this.onclose = function() {};
        
        _xhr = function() {
          if (typeof XMLHttpRequest === 'undefined') {
            XMLHttpRequest = function() {
              try { return new ActiveXObject("Msxml2.XMLHTTP.6.0"); }
                catch(e) {}
              try { return new ActiveXObject("Msxml2.XMLHTTP.3.0"); }
                catch(e) {}
              try { return new ActiveXObject("Msxml2.XMLHTTP"); }
                catch(e) {}
              try { return new ActiveXObject("Microsoft.XMLHTTP"); }
                catch(e) {}
              throw new Error("This browser does not support XMLHttpRequest.");
            };
          }
          return new XMLHttpRequest();
        };
        
        var listen = function(url, xhr, self, _delay) {
            xhr.open("GET", url, true);
            xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
            xhr.setRequestHeader("Accept", "text/xhr-event-stream");
            xhr.onreadystatechange = function() {
                if (xhr.readyState == 4) {
                    self.onmessage({data : String(xhr.responseText.split("data:")[1]).trim()});
                    setTimeout(function() {
                        listen(url, xhr, self, _delay);
                    }, _delay);
                }
            };
            xhr.send(null);
        };
        listen(url, _xhr(), this, _delay);
        this.onopen();
    };
}
