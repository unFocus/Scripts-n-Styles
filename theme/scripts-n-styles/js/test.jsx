"use strict";

var HelloWorld = React.createClass({

  render: function() {
    return (
      <h1>Hello World!!!</h1>
    );
  },

});

var mainElement = document.querySelector("main");

ReactDOM.render(<HelloWorld></HelloWorld>, mainElement);