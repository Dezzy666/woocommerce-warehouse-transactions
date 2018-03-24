/* KEYBOARD CODES*/
var KEYBOARDCODES = {
    32: " ",
    48: "0",
    49: "1",
    50: "2",
    51: "3",
    52: "4",
    53: "5",
    54: "6",
    55: "7",
    56: "8",
    57: "9",
    65: "A",
    66: "B",
    67: "C",
    68: "D",
    69: "E",
    70: "F",
    71: "G",
    72: "H",
    73: "I",
    74: "J",
    75: "K",
    76: "L",
    77: "M",
    78: "N",
    79: "O",
    80: "P",
    81: "Q",
    82: "R",
    83: "S",
    84: "T",
    85: "U",
    86: "V",
    87: "W",
    88: "X",
    89: "Y",
    90: "Z",
    96: "0",
    97: "1",
    98: "2",
    99: "3",
    100: "4",
    101: "5",
    102: "6",
    103: "7",
    104: "8",
    105: "9"
};

jQuery.widget("medinatur.SimpleBarcodeReadingWrapper", {
    options: {
        charset: KEYBOARDCODES,
        onCodeInserted: function(code) {
            console.log("Code inserted: " + code);
        }
    },

    _create: function () {
        this.element.on("keydown", this._keyPressedHandler.bind(this));
    },

    _keyPressedHandler: function (eventArgs) {
        var pressedKey = eventArgs.keyCode;
        eventArgs.preventDefault();
        if (pressedKey == 13) {
            this.options.onCodeInserted(this.element.val());
            this.element.val("");
        } else {
            if (this.options.charset[pressedKey] !== undefined) {
                this.element.val(this.element.val() + this.options.charset[pressedKey]);
            }
        }
    }
});
