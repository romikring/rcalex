
function CharCounter() {
    this._field = null;
    this._banner = null;
    this._template = 'Осталось %d знак%t';
    this._max = 0;
}

/**
 * @return CharCounter
 */
CharCounter.prototype.max = function() {
    if ( arguments.length == 1 && typeof arguments[0] == 'number' ) {
        this._max = parseInt(arguments[0], 10);
        return this;
    }
    
    return this._max;
}

/**
 * @return CharCounter
 */
CharCounter.prototype.field = function() {
    if ( arguments.length == 1 ) {
        if ( typeof arguments[0] == 'string' ) {
            var el = document.getElementById(arguments[0]);
            if ( el != null )   this._field = el;
            else                throw new Error("Element by id " + arguments[0] + " not found");
            return this;
        }
        else if ( typeof arguments[0] == 'object' && arguments[0].value != undefined ) {
            this._field = arguments[0];
            return this;
        }
        else {
            throw new Error("Incorrect field object");
        }
    }
    
    return this._field;
}

/**
 * @return CharCounter
 */
CharCounter.prototype.banner = function() {
    if ( arguments.length == 1 ) {
        if ( typeof arguments[0] == 'string' ) {
            var el = document.getElementById(arguments[0]);
            if ( el != null )   this._banner = el;
            else                throw new Error("Element by id " + arguments[0] + " not found");
            return this;
        }
        else if ( typeof arguments[0] == 'object' && arguments[0].innerHTML != undefined ) {
            this._banner = arguments[0];
            return this;
        }
        else {
            throw new Error("Incorrect field object");
        }
    }
    
    return this._banner;
}

/**
 * @return CharCounter
 */
CharCounter.prototype.template = function() {
    if ( arguments.length == 1 ) {
        if ( typeof arguments[0] == 'string' ) {
            this._template = arguments[0];
            return this;
        } else {
            throw new Error("Incorrect argument: " + arguments[0]);
        }
    }
    
    return this._template;
}

CharCounter.prototype._applyTermination = function( str ) {
    var avaiableChars = this._max - str.length;
    
    avaiableChars = (avaiableChars < 0) ? 0 : avaiableChars;
    if ( avaiableChars > 20 ) {
        avaiableChars = parseInt(avaiableChars.toString().slice(-1), 10);
    }
    
    if ( avaiableChars < 5 ) {
        switch( avaiableChars ) {
            case 0:  return 'ов';
            case 1:  return '';
            default: return 'а';
        }
    }
    
    return 'ов';
}

CharCounter.prototype._process = function(field, max) {
    var string = field.value,
        termination = this._applyTermination(string),
        t = this.template();
        
    var availableChars = max - string.length;
    if ( availableChars < 0 )
        availableChars = 0;
        
    if ( t == null || t == '' )
        throw new Error("Output template error cannon be empty");
    
    return t.replace("%d", availableChars).replace("%t", termination);
}

/**
 * @return CharCounter
 */
CharCounter.prototype.process = function() {
    var f = this.field(),
        b = this.banner(),
        m = this.max();
        
    if ( f == null || b == null )
        throw new Error("Required objects didn't set");
    if ( m < 1 )
        throw new Error("Length of string cannon be less then 1 character");
    
    this.banner().innerHTML = this._process(f, m);
    
    
    
    return this;
}

CharCounter.prototype.run = function() {
    var self = this;
    
    this.process();
    
    this.field().addEventListener("keyup", function(){self.process()}, false);
    this.field().addEventListener("keypress", function(e) {
        e = e || window.event;
        
        if (self.max()-self.field().value.length <= 0) {
            if ( e.preventDefault ) e.preventDefault();
            else e.returnValue = false;
        }
    }, false);
}
