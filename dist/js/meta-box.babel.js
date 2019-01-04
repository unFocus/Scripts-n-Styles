 /* eslint-disable camelcase */
import $ from 'jquery';
import 'chosen-js';
import 'chosen-js/chosen.css';
import '../css/meta-box.less';

let CodeMirror = wp.CodeMirror;

if ( CodeMirror ) {
	CodeMirror.modeURL = _SnSOptions.root + 'codemirror/mode/%N/%N.js';
}

$( function() {
	if ( ! CodeMirror ) {

		// Temp bailout.
		return;
	}

	let context = '#SnS_meta_box',
		currentCodeMirror = [],
		contentEditors = [],
		gutenMCE = false,
		nonce = $( '#scripts_n_styles_noncename' ).val(),
		defaultSettings = $.extend({}, wp.codeEditor.defaultSettings );

	if ( window.wpEditorL10n && wpEditorL10n.tinymce && wpEditorL10n.tinymce.settings ) {
		gutenMCE = wpEditorL10n.tinymce.settings;
	}

	if ( gutenMCE ) {
		console.log( 'yep' );
	} else {
		console.log( 'nope' );
	}

	// For CPTs that don't have an editor, prevent "tinyMCEPreInit is 'undefined'"
	let initDatas = ( 'undefined' !== typeof tinyMCEPreInit && tinyMCEPreInit.mceInit ) ? tinyMCEPreInit.mceInit : false;

	for ( let contentEditor in initDatas ) {

		// contentEditors are tinyMCE instances, because there can be more than one.
		contentEditors.push( contentEditor );
	}

	let mceBodyClass = getMCEBodyClasses();

	// $( '#SnS_enqueue_scripts' ).data( 'placeholder', 'Enqueue Registered Scripts...' ).chosen({ width: '356px' });
	// $( '.chosen-container-multi .chosen-choices .search-field input' ).height( '26px' );
	// $( '.chosen-container .chosen-results' ).css( 'max-height', '176px' );

	//$('textarea', context).attr('autocomplete','off');

	// Refresh when panel becomes unhidden
	$( '#adv-settings' ).on( 'click', context + '-hide', refreshCodeMirrors );
	$( context ).on( 'click', '.hndle, .handlediv', refreshCodeMirrors );

	// add tab-switch handler
	$( context ).on( 'click', '.wp-tab-bar a', onTabSwitch );

	// activate first run
	$( '.wp-tab-active a', context ).click();

	// must run before ajax click handlers are added.
	setupAjaxUI();

	refreshDeleteBtns();

	if ( wp.data && wp.data.select ) {
		let editPost = wp.data.select( 'core/edit-post' );
		wp.data.subscribe( function() {
			if ( ! editPost.isSavingMetaBoxes() ) {
				return;
			}
			$( currentCodeMirror ).each( function() {
				this.save();
			});
		});
	}

	$( '#sns-ajax-update-scripts' ).click( function( event ) {
		event.preventDefault();
		$( this ).next().show();
		$( currentCodeMirror ).each( function() {
			this.save();
		});
		let args = {
			_ajax_nonce: nonce, // eslint-disable-line camelcase
			post_id: $( '#post_ID' ).val()
		};

		args.action = 'sns_scripts';
		args.scripts = $( '#SnS_scripts' ).val();
		args.scripts_in_head = $( '#SnS_scripts_in_head' ).val();

		$.post( ajaxurl, args, function() {
			refreshMCE();
		});
	});

	$( '#sns-ajax-update-styles' ).click( function( event ) {
		event.preventDefault();
		$( this ).next().show();
		$( currentCodeMirror ).each( function() {
			this.save();
		});
		let args = {
			_ajax_nonce: nonce, // eslint-disable-line camelcase
			post_id: $( '#post_ID' ).val()
		};

		args.action = 'sns_styles';
		args.styles = $( '#SnS_styles' ).val();

		$.post( ajaxurl, args, function() {
			refreshMCE();
		});
	});

	/*
	 * Expects return data.
	 */
	$( '#sns-ajax-update-classes' ).click( function( event ) {
		event.preventDefault();
		$( this ).next().show();
		let args = {
			_ajax_nonce: nonce, // eslint-disable-line camelcase
			post_id: $( '#post_ID' ).val()
		};

		args.action = 'sns_classes';
		args.classes_body = $( '#SnS_classes_body' ).val();
		args.classes_post = $( '#SnS_classes_post' ).val();

		$.post( ajaxurl, args, function( data ) {
			refreshBodyClass( data );
		});
	});
	$( '#SnS_classes_body, #SnS_classes_body' ).keypress( function( event ) {
		if ( 13 == event.which ) {
			event.preventDefault();
			$( '#sns-ajax-update-classes' ).click();
		}
	});

	/*
	 * Expects return data.
	 */
	$( '#sns-ajax-update-dropdown' ).click( function( event ) {
		event.preventDefault();
		$( this ).next().show();
		let args = {
			_ajax_nonce: nonce, // eslint-disable-line camelcase
			post_id: $( '#post_ID' ).val()
		};

		args.action = 'sns_dropdown';
		let format = {};
		format.title = $( '#SnS_classes_mce_title' ).val();
		format.classes = $( '#SnS_classes_mce_classes' ).val();
		switch ( $( '#SnS_classes_mce_type' ).val() ) {
			case 'inline':
				format.inline = $( '#SnS_classes_mce_element' ).val();
				break;
			case 'block':
				format.block = $( '#SnS_classes_mce_element' ).val();
				if ( $( '#SnS_classes_mce_wrapper' ).prop( 'checked' ) ) {
					format.wrapper = true;
				}
				break;
			case 'selector':
				format.selector = $( '#SnS_classes_mce_element' ).val();
				break;
			default:
				return;
		}
		args.format = format;

		$.post( ajaxurl, args, function( data ) {
			refreshStyleFormats( data );
		});
	});
	$( '#SnS_classes_mce_classes, #SnS_classes_mce_element, #SnS_classes_mce_title' ).keypress( function( event ) {
		if ( 13 == event.which ) {
			event.preventDefault();
			$( '#sns-ajax-update-dropdown' ).click();
		}
	});

	/*
	 * Expects return data.
	 */
	$( '#delete-mce-dropdown-names' ).on( 'click', '.sns-ajax-delete', function( event ) {
		event.preventDefault();
		$( this ).next().show();
		let args = {
			_ajax_nonce: nonce, // eslint-disable-line camelcase
			post_id: $( '#post_ID' ).val()
		};

		args.action = 'sns_delete_class';
		args.delete = $( this ).attr( 'id' );

		$.post( ajaxurl, args, function( data ) {
			refreshStyleFormats( data );
		});
	});


	/*
	 * Expects return data.
	 */
	$( '#sns-ajax-add-shortcode' ).click( function( event ) {
		event.preventDefault();
		$( this ).next().show();
		$( currentCodeMirror ).each( function() {
			this.save();
		});

		let args = {
			_ajax_nonce: nonce, // eslint-disable-line camelcase
			post_id: $( '#post_ID' ).val()
		};

		args.action = 'sns_shortcodes';
		args.subaction = 'add';
		args.name = $( '#SnS_shortcodes' ).val();
		args.shortcode = $( '#SnS_shortcodes_new' ).val();

		$.post( ajaxurl, args, function( data ) {
			refreshShortcodes( data );
		});
	});
	$( '#SnS_shortcodes' ).keypress( function( event ) {
		if ( 13 == event.which ) {
			event.preventDefault();
			$( '#sns-ajax-add-shortcode' ).click();
		}
	});

	$( '#sns-shortcodes' ).on( 'click', '.sns-ajax-delete-shortcode', function( event ) {
		event.preventDefault();
		if ( $( this ).data( 'lock' ) ) {
			return;
		} else {
			$( this ).data( 'lock', true );
		}

		$( this ).next().show();
		$( currentCodeMirror ).each( function() {
			this.save();
		});
		let args = {
			_ajax_nonce: nonce, // eslint-disable-line camelcase
			post_id: $( '#post_ID' ).val()
		};

		args.action = 'sns_shortcodes';
		args.subaction = 'delete';
		args.name = $( this ).parent().siblings( 'textarea' ).attr( 'data-sns-shortcode-key' );

		$.post( ajaxurl, args, function( data ) {
			refreshShortcodes( data );
		});
	});
	$( '#sns-shortcodes' ).on( 'click', '.sns-ajax-update-shortcode', function( event ) {
		event.preventDefault();
		$( this ).next().show();
		$( currentCodeMirror ).each( function() {
			this.save();
		});
		let args = {
			_ajax_nonce: nonce, // eslint-disable-line camelcase
			post_id: $( '#post_ID' ).val()
		};

		args.action = 'sns_shortcodes';
		args.subaction = 'update';
		args.name = $( this ).parent().siblings( 'textarea' ).attr( 'data-sns-shortcode-key' );
		args.shortcode = $( this ).parent().siblings( 'textarea' ).val();

		$.post( ajaxurl, args, function( data ) {
			refreshShortcodes( data );
		});
	});

	/*
	 * Returns the body_class of TinyMCE minus the Scripts n Styles values.
	 */
	function getMCEBodyClasses() {
		var t = [],
			a = [],
			b = [],
			c = [];
		if ( gutenMCE.body_class ) {
			b = gutenMCE.body_class.trim().split( ' ' );
		}
		$( contentEditors ).each( function( index, element ) {
			var data = initDatas[element];
			if ( data.body_class ) {
				t = data.body_class.split( ' ' );
			}

			let bc = $( '#SnS_classes_body' ).val().split( ' ' ),
				pc = $( '#SnS_classes_post' ).val().split( ' ' ),
				p;
			for ( let i = 0; i < t.length; i++ ) {
				p = $.inArray( bc[i], t );
				if ( -1 != p ) {
					t.splice( p, 1 );
				}
			}
			for ( let i = 0; i < t.length; i++ ) {
				p = $.inArray( pc[i], t );
				if ( -1 != p ) {
					t.splice( p, 1 );
				}
			}
			t = t.join( ' ' );

			a[element] = t;
		});
		c = a.concat( b );
		return c;
	}

	/*
	 * Builds and Adds the DOM for AJAX functionality.
	 */
	function setupAjaxUI() {

		// set up ajax ui. (need to come up with a better ID naming scheme.)
		$( '#SnS_scripts-tab' ).append(
			'<div class="sns-ajax-wrap">' +
				'<a id="sns-ajax-update-scripts" href="#" class="button">Update Scripts</a>' +
				' ' +
				'<span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span>' +
				'</div>'
			);

		$( '#SnS_styles-tab' ).append(
			'<div class="sns-ajax-wrap">' +
				'<a id="sns-ajax-update-styles" href="#" class="button">Update Styles</a>' +
				' ' +
				'<span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span>' +
				'</div>'
			);

		$( '#sns-classes' ).append(
			'<div class="sns-ajax-wrap">' +
				'<a id="sns-ajax-update-classes" href="#" class="button">Update Classes</a>' +
				' ' +
				'<span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span>' +
				'</div>'
			);

		$( '#add-mce-dropdown-names' ).append(
			'<div class="sns-ajax-wrap">' +
				'<a id="sns-ajax-update-dropdown" href="#" class="button">Add Class</a>' +
				' ' +
				'<span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span>' +
				'</div>'
			);

		$( '#SnS_shortcodes' ).after(
			' &nbsp; ' +
				'<a id="sns-ajax-add-shortcode" href="#" class="button">Add New</a>' +
				' ' +
				'<span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span>'
			);
		$( '#sns-shortcodes .sns-shortcode .inside' ).append(
			'<div class="sns-ajax-wrap">' +
				'<a class="sns-ajax-delete-shortcode button" href="#">Delete</a>' +
				' &nbsp; ' +
				'<a class="sns-ajax-update-shortcode button" href="#">Update</a>' +
				' ' +
				'<span class="sns-ajax-loading"><span class="spinner" style="display: inline-block;"></span></span>' +
				'</div>'
			);

		$( '.sns-ajax-loading' ).hide();

		if ( 'block' == $( '#SnS_classes_mce_type' ).val() ) {
			$( '#add-mce-dropdown-names .sns-mce-wrapper' ).show();
		} else {
			$( '#add-mce-dropdown-names .sns-mce-wrapper' ).hide();
		}

		$( '#SnS_classes_mce_type' ).change( function() {
			if ( 'block' == $( this ).val() ) {
				$( '#add-mce-dropdown-names .sns-mce-wrapper' ).show();
			} else {
				$( '#add-mce-dropdown-names .sns-mce-wrapper' ).hide();
			}
		});

		$( '.wp-tab-bar li', context ).show();
	}

	/*
	 * Main Tab Switch Handler.
	 */
	function onTabSwitch( event ) {
		event.preventDefault();

		clearCodeMirrors();

		/*
		 * There is a weird bug where if clearCodeMirrors() is called right before
		 * loadCodeMirrors(), loading the page with the Styles tab active, and
		 * then switching to the Script tab, you can lose data from the second
		 * CodeMirror if leaving and returning to that tab. I've no idea what's
		 * going on there. Leaving code inbetween them is a fraggle, but working,
		 * workaround. Maybe has to do with execution time? No idea.
		 */

		// switch active classes
		$( '.wp-tab-active', context ).removeClass( 'wp-tab-active' );
		$( this ).parent( 'li' ).addClass( 'wp-tab-active' );

		$( '.wp-tabs-panel-active', context ).hide().removeClass( 'wp-tabs-panel-active' );
		$( $( this ).attr( 'href' ) ).show().addClass( 'wp-tabs-panel-active' );

		loadCodeMirrors();

		$.post( ajaxurl, {
				action: 'sns_update_tab',
				_ajax_nonce: nonce, // eslint-disable-line camelcase
				active_tab: $( '.wp-tab-bar li', context ).index( $( this ).parent( 'li' ).get( 0 ) )
			}
		);
	}

	/*
	 * CodeMirror Utilities.
	 */
	function clearCodeMirrors() {
		$( currentCodeMirror ).each( function() {
			this.toTextArea();
		});
		currentCodeMirror = [];
	}
	function refreshCodeMirrors() {
		$( currentCodeMirror ).each( function() {
			this.refresh();
		});
	}
	function loadCodeMirrors() {

		// collect codemirrors
		var settings;

		// loop codemirrors
		$( '.wp-tabs-panel-active textarea.codemirror', context ).each( function() {
			if ( $( this ).hasClass( 'js' ) ) {
				settings = {
					mode: 'text/javascript',
					lineNumbers: true,
					tabMode: 'shift',
					indentUnit: 4,
					indentWithTabs: true
				};
			} else if ( $( this ).hasClass( 'css' ) ) {
				settings = {
					mode: 'text/css',
					lineNumbers: true,
					tabMode: 'shift',
					indentUnit: 4,
					indentWithTabs: true
				};
			} else if ( $( this ).hasClass( 'less' ) ) {
				settings = {
					mode: 'text/x-less',
					lineNumbers: true,
					tabMode: 'shift',
					indentUnit: 4,
					indentWithTabs: true
				};
			} else if ( $( this ).hasClass( 'htmlmixed' ) ) {
				settings = {
					mode: 'text/html',
					lineNumbers: true,
					tabMode: 'shift',
					indentUnit: 4,
					indentWithTabs: true,
					enterMode: 'keep',
					matchBrackets: true
				};
			} else {
				return;
			}

			// initialize and store active codemirrors
			let cm = wp.codeEditor.initialize( this, $.extend({}, defaultSettings, {
				codemirror: $.extend({}, defaultSettings.codemirror, settings )
			}) ).codemirror;
			currentCodeMirror.push( cm );
		});
	}

	/*
	 * Refresh after AJAX.
	 */
	function refreshDeleteBtns() {

		// responsible for clearing out Delete Buttons, and Adding new ones.
		// initData should always contain the latest settings.
		var formats = [];

		$( contentEditors ).each( function( index, key ) {
			var initData = initDatas[key];
			if ( initData.style_formats && initData.style_formats.length ) {
				formats = initData.style_formats;
			}
		});
		if ( gutenMCE.style_formats && gutenMCE.style_formats.length ) {
			formats = gutenMCE.style_formats;
		}

		if ( ! formats.length ) {
			$( '#delete-mce-dropdown-names', context ).hide();
			return;
		}

		$( '#delete-mce-dropdown-names .sns-ajax-delete-p' ).remove();
		$( '#delete-mce-dropdown-names', context ).show();

		for ( let i = 0; i < formats.length; i++ ) {
			let deleteBtn = {};
			if ( formats[i].inline ) {
				deleteBtn.element =  formats[i].inline;
				deleteBtn.wrapper = '';
			} else if ( formats[i].block ) {
				deleteBtn.element =  formats[i].block;
				if ( formats[i].wrapper ) {
					deleteBtn.wrapper = ' (wrapper)';
				} else {
					deleteBtn.wrapper = '';
				}
			} else if ( formats[i].selector ) {
				deleteBtn.element =  formats[i].selector;
				deleteBtn.wrapper = '';
			} else {
				console.warn( 'ERROR!' );
			}
			deleteBtn.title = formats[i].title;
			deleteBtn.classes = formats[i].classes;
			$( '#instructions-mce-dropdown-names', context ).after(
				'<p class="sns-ajax-delete-p"><a title="delete" class="sns-ajax-delete" id="' +
				deleteBtn.title + '">X</a> "' +
				deleteBtn.title + '" <code>&lt;' +
				deleteBtn.element + ' class="' +
				deleteBtn.classes + '"&gt;</code>' +
				deleteBtn.wrapper + '</p>'
			);
		}
	}
	function refreshBodyClass( data ) {
		$( contentEditors ).each( function( index, key ) {
			initDatas[key].body_class = mceBodyClass[key] + ' ' + data.classes_body + ' ' + data.classes_post;
		});
		refreshMCE();
	}
	function refreshStyleFormats( data ) {
		var initData = false;
		$( contentEditors ).each( function( index, key ) {
			initData = initDatas[key];
		});

		// error check
		//console.log(data.classes_mce);
		if ( 'undefined' === typeof data.classes_mce ) {
			console.warn( data );

			/*$( '.sns-ajax-loading' ).hide();
			return;*/ // Don't block
		} else if ( data.classes_mce.length && 'Empty' != data.classes_mce ) {
			let style_formats = [];

			for ( let i = 0; i < data.classes_mce.length; i++ ) { // loop returned classes_mce
				let format = {};
				format.title = data.classes_mce[i].title;

				if ( data.classes_mce[i].inline ) {
					format.inline = data.classes_mce[i].inline;
				} else if ( data.classes_mce[i].block ) {
					format.block = data.classes_mce[i].block;
					if ( data.classes_mce[i].wrapper ) {
						format.wrapper = true;
					}
				} else if ( data.classes_mce[i].selector ) {
					format.selector = data.classes_mce[i].selector;
				} else {
					console.warn( 'dropdown format has bad type.' );
				}

				format.classes = data.classes_mce[i].classes;
				style_formats.push( format );
			}
			initData.style_formats = style_formats;

			if ( -1 == initData.toolbar2.indexOf( 'styleselect' ) ) {
				let tempString = 'styleselect,';
				initData.toolbar2 = tempString.concat( initData.toolbar2 );
			}

			$( '#delete-mce-dropdown-names', context ).show();
		} else {
			delete initData.style_formats;
			initData.toolbar2 = initData.toolbar2.replace( 'styleselect,', '' );

			$( '#delete-mce-dropdown-names', context ).hide();
		}

		refreshDeleteBtns();
		refreshMCE();
	}
	if ( 0 == $( '.sns-shortcode', '#sns-shortcodes' ).length ) {
		$( 'h4', '#sns-shortcodes' ).hide();
	}
	function refreshShortcodes( data ) {
		if ( data.code ) {
			switch ( data.code ) {
				case 2:
					console.log( data.message );
					break;
				case 3:
					$( 'textarea[data-sns-shortcode-key=' + data.message + ']', '#sns-shortcodes' ).closest( '.sns-shortcode' ).slideUp( function() {
						$( this ).remove();
						if ( 0 == $( '.sns-shortcode', '#sns-shortcodes' ).length ) {
							$( 'h4', '#sns-shortcodes' ).slideUp();
						}
					});
					break;
			}
		} else {
			if ( 0 == data.indexOf( '<' ) ) {
				$( '#sns-shortcodes-wrap' ).prepend( data ).find( '.widget' ).hide().slideDown();
				$( '.codemirror-new' ).parent().prepend( '<span class="sns-collapsed-shortcode-btn"></span>' );
				let codemirrorNew = $( '.codemirror-new' ).removeClass( 'codemirror-new' ).addClass( 'codemirror' ).get( 0 );
				currentCodeMirror.push( CodeMirror.fromTextArea( codemirrorNew, {
					mode: 'text/html',
					lineNumbers: true,
					tabMode: 'shift',
					indentUnit: 4,
					indentWithTabs: true,
					enterMode: 'keep',
					matchBrackets: true
				}) );
				if ( 0 == $( 'h4', '#sns-shortcodes' ).length ) {
					$( '#sns-shortcodes' ).prepend( '<h4>Existing Codes: </h4>' );
				}
				if ( ! $( 'h4', '#sns-shortcodes' ).is( ':visible' )  ) {
					$( 'h4', '#sns-shortcodes' ).slideDown();
				}
				clearCodeMirrors();
				$( '#SnS_shortcodes' ).val( '' );
				$( '#SnS_shortcodes_new' ).val( '' );
				loadCodeMirrors();

			} else if ( 0 == data.indexOf( 'empty value.' ) ) {
				console.log( 'empty value' );
			} else if ( 0 == data.indexOf( 'Use delete instead.' ) ) {
				console.log( 'Use delete instead' );
			} else {
				console.warn( 'Scripts n Styles: ' + '\n\n' + 'Sorry, there was an AJAX error: (' + data + ')' + '\n\n' + 'Please use the post update button instead.' );
			}
		}
		$( '.sns-ajax-loading' ).hide();
	}
	addShortcodeBtns();
	function addShortcodeBtns() {
		$( '.sns-shortcode > .inside > p' ).before( '<span class="sns-collapsed-shortcode-btn"></span>' );
		$( '#sns-shortcodes-wrap' ).on( 'click', '.sns-collapsed-shortcode-btn', function( event ) {
			$( this ).parent().toggleClass( 'sns-collapsed-shortcode' );
		});
		$( '.sns-collapsed-shortcode-btn' ).click();
	}
	function refreshMCE() {
		$( tinyMCE.editors ).each( function( index, ed ) {

			// If Visual has been activated.
			if ( ed ) {
				if ( ed.isHidden() ) {
					refreshMCEhelper( ed );
				} else {
					$( '#' + ed.id + '-html' ).click(); // 3.3

					refreshMCEhelper( ed  );

					$( '#' + ed.id + '-tmce' ).click(); // 3.3
				}
			}
		});
		$( '.sns-ajax-loading' ).hide();
	}
	function refreshMCEhelper( ed ) {
		if ( gutenMCE ) {
			return;
		}
		ed.save();
		ed.destroy();
		ed.remove();
		if ( initDatas[ed.id] && initDatas[ed.id].wpautop ) {
			$( '#' + ed.id ).val( switchEditors.wpautop( $( '#' + ed.id ).val() ) );
		}
		ed = new tinymce.Editor( ed.id, initDatas[ed.id], tinymce.EditorManager );
		ed.render();
		ed.hide();
	}

});
