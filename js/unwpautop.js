(function() {
	var unFltr, unFltrPre, chnks;
	jQuery('body').bind({
		beforePreWpautop: function(e, o) {
			unFltrPre = o.data;
		},
		afterPreWpautop: function(e, o) {
			if (null !== chnks) {
				var chnk;
				for (var i = 0; i < chnks.length; ++i) {
					// if the line remains intact, restore line break.
					// Regex strips left whitespace to get better compare.
					chnk = chnks[i].replace(/^\s+/,"");
					if (unFltrPre.indexOf(chnk) > -1) {
						// When the html is converted to rich text, an extra
						// space char is added to replace the \n. We need to
						// account for that here, so we get it at the end of
						// the previous line, rather than the start of the 
						// next line.
						unFltrPre = unFltrPre.replace(
							/* checks the trailing space as optional */
							new RegExp(chnk.replace(/[-[\]{}()*+?.,\\^$|#\s]/g, "\\$&") + " ?"),
							/* replaces chnk with the untrimmed chnk */
							chnks[i] + "\n"
						);
					}
				}
			}
			o.data = unFltrPre;
		},
		beforeWpautop: function(e, o) {
			// store individual lines, so we can restore breaks later.
			chnks = o.data.split("\n");
			unFltr = o.data;
		},
		afterWpautop: function(e, o) {
			o.data = unFltr;
		}
	});
})();