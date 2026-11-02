/**
 * AK Studio blocks — editor side. Texts are typed straight into the block;
 * films, photos, links, numbers and lists are set in the block sidebar.
 * The front end is rendered by PHP (includes/blocks.php), so the design
 * stays the same whatever is typed here.
 */
( function ( wp ) {
	'use strict';

	var registerBlockType = wp.blocks.registerBlockType;
	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var be = wp.blockEditor;
	var useBlockProps = be.useBlockProps;
	var RichText = be.RichText;
	var InspectorControls = be.InspectorControls;
	var MediaUpload = be.MediaUpload;
	var MediaUploadCheck = be.MediaUploadCheck;
	var c = wp.components;
	var SSR = wp.serverSideRender;
	var useSelect = wp.data.useSelect;
	var data = window.akBlocks || { types: [], disciplines: [], reel: [] };

	/* ── Field helpers ─────────────────────────────────────────────── */

	function setter( props, key ) {
		return function ( value ) {
			var o = {};
			o[ key ] = value;
			props.setAttributes( o );
		};
	}

	function text( props, key, label, help, type ) {
		return el( c.TextControl, { label: label, help: help, type: type || 'text', value: props.attributes[ key ] || '', onChange: setter( props, key ), __nextHasNoMarginBottom: true, __next40pxDefaultSize: true } );
	}

	function toggle( props, key, label, help ) {
		return el( c.ToggleControl, { label: label, help: help, checked: !! props.attributes[ key ], onChange: setter( props, key ), __nextHasNoMarginBottom: true } );
	}

	/** A media picker. value: { id, url }; onPick receives the media object (or null to clear). */
	function media( value, onPick, label, type ) {
		var url = value && value.url;
		return el( MediaUploadCheck, null, el( MediaUpload, {
			allowedTypes: [ type ],
			value: value && value.id,
			onSelect: function ( m ) { onPick( m ); },
			render: function ( r ) {
				return el( 'div', { className: 'ak-field-media' },
					el( 'p', { className: 'ak-field-media__label' }, label ),
					url ? ( 'video' === type
						? el( 'video', { src: url, muted: true, playsInline: true, className: 'ak-field-media__preview' } )
						: el( 'img', { src: url, alt: '', className: 'ak-field-media__preview' } ) ) : null,
					el( 'div', { className: 'ak-field-media__actions' },
						el( c.Button, { variant: 'secondary', onClick: r.open }, url ? 'Replace' : 'Choose' ),
						url ? el( c.Button, { variant: 'link', isDestructive: true, onClick: function () { onPick( null ); } }, 'Remove' ) : null
					)
				);
			},
		} ) );
	}

	/**
	 * A list of items, each a small form. fields: [ { key, label, type: text|textarea|video|image } ].
	 */
	function repeater( props, key, fields, itemName, titleKey ) {
		var list = ( props.attributes[ key ] || [] ).slice();
		var update = function ( i, patch ) {
			var next = list.slice();
			next[ i ] = Object.assign( {}, next[ i ], patch );
			setter( props, key )( next );
		};
		var move = function ( i, d ) {
			var j = i + d;
			if ( j < 0 || j >= list.length ) { return; }
			var next = list.slice();
			var t = next[ i ]; next[ i ] = next[ j ]; next[ j ] = t;
			setter( props, key )( next );
		};
		return el( 'div', { className: 'ak-repeater' },
			list.map( function ( item, i ) {
				var title = ( item[ titleKey ] || '' ).replace( /<[^>]+>/g, '' ) || ( itemName + ' ' + ( i + 1 ) );
				return el( c.PanelBody, { key: i, title: ( i + 1 ) + '. ' + title, initialOpen: false },
					fields.map( function ( f ) {
						if ( 'video' === f.type || 'image' === f.type ) {
							return el( 'div', { key: f.key }, media( { id: item[ f.key + 'Id' ], url: item[ f.key ] }, function ( m ) {
								var p = {};
								p[ f.key ] = m ? m.url : '';
								p[ f.key + 'Id' ] = m ? m.id : 0;
								update( i, p );
							}, f.label, f.type ) );
						}
						var Control = 'textarea' === f.type ? c.TextareaControl : c.TextControl;
						return el( Control, { key: f.key, label: f.label, help: f.help, value: item[ f.key ] || '', onChange: function ( v ) { var p = {}; p[ f.key ] = v; update( i, p ); }, __nextHasNoMarginBottom: true } );
					} ),
					el( 'div', { className: 'ak-repeater__actions' },
						el( c.Button, { variant: 'tertiary', onClick: function () { move( i, -1 ); }, disabled: 0 === i }, '↑' ),
						el( c.Button, { variant: 'tertiary', onClick: function () { move( i, 1 ); }, disabled: i === list.length - 1 }, '↓' ),
						el( c.Button, { variant: 'link', isDestructive: true, onClick: function () { var next = list.slice(); next.splice( i, 1 ); setter( props, key )( next ); } }, 'Remove' )
					)
				);
			} ),
			el( c.Button, { variant: 'secondary', className: 'ak-repeater__add', onClick: function () { setter( props, key )( list.concat( [ {} ] ) ); } }, '+ Add ' + itemName.toLowerCase() )
		);
	}

	function rich( props, key, tag, className, placeholder, formats ) {
		return el( RichText, { tagName: tag, className: className, value: props.attributes[ key ] || '', onChange: setter( props, key ), placeholder: placeholder, allowedFormats: formats || [ 'core/italic', 'core/bold', 'core/link' ] } );
	}

	function preview( name, props, extraClass ) {
		var blockProps = useBlockProps( { className: 'ak-ssr ' + ( extraClass || '' ) } );
		return el( 'div', blockProps, el( SSR, { block: name, attributes: props.attributes, EmptyResponsePlaceholder: function () { return el( 'p', { className: 'ak-empty' }, 'Nothing to show yet — check the settings in the sidebar.' ); } } ) );
	}

	var save = function () { return null; };

	/* ── Page header ───────────────────────────────────────────────── */

	registerBlockType( 'ak/page-header', {
		edit: function ( props ) {
			var a = props.attributes;
			var blockProps = useBlockProps( { className: 'page-head page-head--' + a.size + ( a.mediaUrl ? ' page-head--media' : '' ) + ' ak-full' } );
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'Header' },
						el( c.SelectControl, { label: 'Size', value: a.size, onChange: setter( props, 'size' ), __nextHasNoMarginBottom: true, options: [
							{ label: 'Large', value: 'large' }, { label: 'Compact', value: 'compact' }, { label: 'Article', value: 'article' }, { label: 'Full-screen cover', value: 'cover' } ] } ),
						el( c.SelectControl, { label: 'Background', value: a.mediaType, onChange: setter( props, 'mediaType' ), __nextHasNoMarginBottom: true, options: [
							{ label: 'Photo', value: 'image' }, { label: 'Silent looping film', value: 'video' } ] } ),
						media( { id: a.mediaId, url: a.mediaUrl }, function ( m ) { props.setAttributes( { mediaId: m ? m.id : 0, mediaUrl: m ? m.url : '' } ); }, 'video' === a.mediaType ? 'Film' : 'Photo', 'video' === a.mediaType ? 'video' : 'image' ),
						'video' === a.mediaType ? media( { url: a.posterUrl }, function ( m ) { props.setAttributes( { posterUrl: m ? m.url : '' } ); }, 'Poster (shown while the film loads)', 'image' ) : null,
						( a.mediaUrl && 'video' !== a.mediaType && c.FocalPointPicker ) ? el( c.FocalPointPicker, { label: 'What stays in frame', help: 'Click the face or the subject. Without a choice, the point set for the image in the Media Library is used.', url: a.mediaUrl, value: a.focus || { x: 0.5, y: 0.3 }, onChange: function ( v ) { props.setAttributes( { focus: v } ); }, __nextHasNoMarginBottom: true } ) : null,
						a.focus ? el( c.Button, { variant: 'link', onClick: function () { props.setAttributes( { focus: undefined } ); } }, 'Use the image’s own focus point' ) : null,
						text( props, 'questionLabel', 'Label above the question' )
					)
				),
				el( 'header', blockProps,
					a.mediaUrl ? el( 'div', { className: 'page-head__media' },
						'video' === a.mediaType ? el( 'video', { src: a.mediaUrl, poster: a.posterUrl, muted: true } ) : el( 'img', { src: a.mediaUrl, alt: '', style: a.focus ? { objectPosition: ( a.focus.x * 100 ) + '% ' + ( a.focus.y * 100 ) + '%' } : undefined } ),
						el( 'div', { className: 'page-head__scrim' } ) ) : null,
					el( 'div', { className: 'ak-wrap page-head__inner' },
						rich( props, 'eyebrow', 'p', 'eyebrow', 'Eyebrow (small line above)', [ 'core/link' ] ),
						rich( props, 'heading', 'h1', 'display page-head__title', 'Heading — italic words turn orange', [ 'core/italic' ] ),
						el( 'div', { className: 'page-head__foot' },
							rich( props, 'lede', 'p', 'lede', 'Lead paragraph' ),
							el( 'div', { className: 'page-head__q' }, a.question ? el( 'span', null, a.questionLabel ) : null, rich( props, 'question', 'span', '', 'Question (optional)', [] ) )
						)
					)
				)
			);
		},
		save: save,
	} );

	/* ── Hero reel ─────────────────────────────────────────────────── */

	registerBlockType( 'ak/hero', {
		edit: function ( props ) {
			var a = props.attributes;
			var clips = ( a.clips && a.clips.length ) ? a.clips : data.reel;
			var poster = clips[ 0 ] && clips[ 0 ].poster;
			var blockProps = useBlockProps( { className: 'hero hero--editor ak-full' } );
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'Films in the reel', initialOpen: true },
						el( 'p', { className: 'ak-help' }, a.clips && a.clips.length ? 'They play in this order, one after another.' : 'Using the built-in footage. Add films to replace it.' ),
						repeater( props, 'clips', [
							{ key: 'video', label: 'Film (silent, wide)', type: 'video' },
							{ key: 'videoSm', label: 'Smaller film for phones (optional)', type: 'video' },
							{ key: 'poster', label: 'Poster image', type: 'image' },
							{ key: 'label', label: 'Caption — first line' },
							{ key: 'kind', label: 'Caption — second line' },
							{ key: 'focus', label: 'What stays in frame on phones', help: 'From the left and from the top, e.g. 40% 30%. Empty: the centre.' },
						], 'Film', 'label' )
					),
					el( c.PanelBody, { title: 'Numbers', initialOpen: false },
						repeater( props, 'stats', [
							{ key: 'value', label: 'Number or text', help: 'Numbers count up, e.g. 15' },
							{ key: 'suffix', label: 'After the number', help: 'e.g. +' },
							{ key: 'label', label: 'Label' },
						], 'Number', 'label' )
					),
					el( c.PanelBody, { title: 'Links', initialOpen: false },
						text( props, 'primaryUrl', 'Main button link' ),
						text( props, 'secondaryUrl', 'Second button link' ),
						text( props, 'pillUrl', 'Small badge link (optional)' )
					)
				),
				el( 'section', blockProps,
					poster ? el( 'div', { className: 'hero__media' }, el( 'img', { src: poster, alt: '', className: 'hero__poster' } ), el( 'div', { className: 'hero__scrim' } ) ) : null,
					el( 'div', { className: 'hero__inner' },
						el( 'div', { className: 'hero__meta ak-wrap' }, el( 'p', { className: 'slate' }, el( 'span', { className: 'rec' } ), 'REC / ', rich( props, 'slate', 'span', '', 'Small line at the top', [] ) ) ),
						el( 'div', { className: 'kinetic' },
							el( 'div', { className: 'kinetic__row kinetic__row--outline' }, rich( props, 'row1', 'span', 'kinetic__phrase', 'First line (outline)', [ 'core/italic' ] ) ),
							el( 'div', { className: 'kinetic__row' }, rich( props, 'row2', 'span', 'kinetic__phrase', 'Second line — italic words turn orange', [ 'core/italic' ] ) ),
							el( 'div', { className: 'kinetic__row kinetic__row--outline' }, rich( props, 'row3', 'span', 'kinetic__phrase', 'Third line (outline)', [ 'core/italic' ] ) )
						),
						el( 'div', { className: 'hero__intro ak-wrap' },
							el( 'div', { className: 'hero__lede' },
								el( 'p', { className: 'pill' }, el( 'span', { className: 'pill__dot' } ), rich( props, 'pillText', 'span', '', 'Small badge (optional)', [] ) ),
								rich( props, 'lede', 'p', 'hero__text', 'What the studio does, in two sentences' ),
								el( 'div', { className: 'hero__ctas' },
									rich( props, 'primaryLabel', 'span', 'btn btn--accent', 'Main button', [] ),
									rich( props, 'secondaryLabel', 'span', 'btn btn--ghost', 'Second button', [] )
								)
							),
							( a.stats && a.stats.length ) ? el( 'dl', { className: 'stats' }, a.stats.map( function ( s, i ) {
								return el( 'div', { className: 'stat', key: i }, el( 'dt', null, s.label ), el( 'dd', null, ( s.value || '' ) + ( s.suffix || '' ) ) );
							} ) ) : null
						)
					)
				)
			);
		},
		save: save,
	} );

	/* ── Moving band ───────────────────────────────────────────────── */

	registerBlockType( 'ak/marquee', {
		edit: function ( props ) {
			var a = props.attributes;
			var items = a.items || [];
			var blockProps = useBlockProps( { className: 'band ak-full' + ( 'outline' === a.variant ? ' band--outline' : '' ) } );
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'Band' },
						el( c.TextareaControl, { label: 'Words, one per line', rows: 9, value: items.join( '\n' ), onChange: function ( v ) { props.setAttributes( { items: v.split( '\n' ) } ); }, __nextHasNoMarginBottom: true } ),
						el( c.SelectControl, { label: 'Look', value: a.variant, onChange: setter( props, 'variant' ), __nextHasNoMarginBottom: true, options: [ { label: 'Orange band', value: 'band' }, { label: 'Outline on dark', value: 'outline' } ] } ),
						el( c.RangeControl, { label: 'Seconds per loop (higher is slower)', min: 10, max: 120, value: a.speed, onChange: setter( props, 'speed' ) } )
					)
				),
				el( 'div', blockProps, el( 'div', { className: 'band__strip' }, el( 'div', { className: 'band__track band__track--static' },
					items.filter( Boolean ).map( function ( w, i ) { return el( Fragment, { key: i }, el( 'span', { className: 'band__item' }, w ), el( 'span', { className: 'band__dot' }, '✦' ) ); } ) ) ) )
			);
		},
		save: save,
	} );

	/* ── Services list ─────────────────────────────────────────────── */

	registerBlockType( 'ak/services', {
		edit: function ( props ) {
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'Services' },
						el( 'p', { className: 'ak-help' }, 'Lists the pages under Services. To change a service’s name, question or photo, edit that service’s page.' ),
						text( props, 'parent', 'Parent page address', 'e.g. services' ),
						toggle( props, 'excludeCurrent', 'Hide the page you are on' )
					)
				),
				preview( 'ak/services', props )
			);
		},
		save: save,
	} );

	/* ── Work ──────────────────────────────────────────────────────── */

	function slugChecks( props, key, options, label ) {
		var chosen = ( props.attributes[ key ] || '' ).split( ',' ).map( function ( s ) { return s.trim(); } ).filter( Boolean );
		return el( c.BaseControl, { label: label, __nextHasNoMarginBottom: true }, options.map( function ( o ) {
			return el( c.CheckboxControl, { key: o.slug, label: o.name, checked: chosen.indexOf( o.slug ) > -1, __nextHasNoMarginBottom: true, onChange: function ( on ) {
				var next = chosen.filter( function ( s ) { return s !== o.slug; } );
				if ( on ) { next.push( o.slug ); }
				setter( props, key )( next.join( ',' ) );
			} } );
		} ) );
	}

	function ProjectPicker( props ) {
		var projects = useSelect( function ( select ) {
			return select( 'core' ).getEntityRecords( 'postType', 'ak_project', { per_page: 100, _fields: 'id,title', status: 'publish' } );
		}, [] ) || [];
		var byId = {};
		var byTitle = {};
		projects.forEach( function ( p ) {
			var t = ( p.title && p.title.rendered ? p.title.rendered : '#' + p.id ).replace( /&#8217;/g, '’' ).replace( /&amp;/g, '&' );
			byId[ p.id ] = t;
			byTitle[ t ] = p.id;
		} );
		var ids = props.attributes.ids || [];
		return el( c.FormTokenField, {
			label: 'Choose projects (optional)',
			value: ids.map( function ( id ) { return byId[ id ] || ( '#' + id ); } ),
			suggestions: Object.keys( byTitle ),
			onChange: function ( tokens ) { props.setAttributes( { ids: tokens.map( function ( t ) { return byTitle[ t ]; } ).filter( Boolean ) } ); },
			__experimentalExpandOnFocus: true,
			__nextHasNoMarginBottom: true,
		} );
	}

	registerBlockType( 'ak/work', {
		edit: function ( props ) {
			var a = props.attributes;
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'Work' },
						el( c.SelectControl, { label: 'Layout', value: a.layout, onChange: setter( props, 'layout' ), __nextHasNoMarginBottom: true, options: [
							{ label: 'Grid', value: 'grid' }, { label: 'Moving rail', value: 'rail' }, { label: 'Large feature cards', value: 'feature' } ] } ),
						el( ProjectPicker, props ),
						el( 'p', { className: 'ak-help' }, 'Or leave empty and filter:' ),
						slugChecks( props, 'types', data.types, 'Types' ),
						slugChecks( props, 'disciplines', data.disciplines, 'Services' ),
						toggle( props, 'featured', 'Only projects marked for the home page' ),
						el( c.RangeControl, { label: 'How many', min: 1, max: 60, value: a.count, onChange: setter( props, 'count' ) } ),
						'grid' === a.layout ? el( c.SelectControl, { label: 'Columns', value: String( a.columns ), onChange: function ( v ) { props.setAttributes( { columns: Number( v ) } ); }, __nextHasNoMarginBottom: true, options: [ { label: '2', value: '2' }, { label: '3', value: '3' } ] } ) : null,
						'grid' === a.layout ? toggle( props, 'showFilters', 'Show type filters', 'Links like /work/?type=website open with that filter on.' ) : null,
						text( props, 'moreLabel', 'Button label (optional)', 'Italic with *asterisks* in rails' ),
						text( props, 'moreUrl', 'Button link' )
					)
				),
				preview( 'ak/work', props )
			);
		},
		save: save,
	} );

	/* ── Website screens ───────────────────────────────────────────── */

	function shotPicker( value, onPick, label, className ) {
		return el( MediaUploadCheck, null, el( MediaUpload, {
			allowedTypes: [ 'image' ],
			value: value && value.id,
			onSelect: function ( m ) { onPick( m ); },
			render: function ( r ) {
				return value && value.url
					? el( Fragment, null, el( 'img', { src: value.url, alt: '' } ), el( c.Button, { variant: 'primary', className: 'screens__replace', onClick: r.open }, 'Replace' ) )
					: el( c.Button, { variant: 'secondary', className: 'screens__add ' + ( className || '' ), onClick: r.open }, label );
			},
		} ) );
	}

	function shotList( props, key, label, help ) {
		var list = props.attributes[ key ] || [];
		return el( c.BaseControl, { label: label, help: help, __nextHasNoMarginBottom: true },
			el( 'div', { className: 'ak-shots' }, list.map( function ( p, i ) { return el( 'img', { key: i, src: p.url, alt: '' } ); } ) ),
			el( MediaUploadCheck, null, el( MediaUpload, {
				allowedTypes: [ 'image' ], multiple: true, gallery: false,
				value: list.map( function ( p ) { return p.id; } ),
				onSelect: function ( items ) { setter( props, key )( ( items || [] ).map( function ( m ) { return { id: m.id, url: m.url }; } ) ); },
				render: function ( r ) { return el( 'div', { className: 'ak-repeater__actions' }, el( c.Button, { variant: 'secondary', onClick: r.open }, list.length ? 'Change' : 'Choose' ), list.length ? el( c.Button, { variant: 'link', isDestructive: true, onClick: function () { setter( props, key )( [] ); } }, 'Remove all' ) : null ); },
			} ) )
		);
	}

	registerBlockType( 'ak/screens', {
		edit: function ( props ) {
			var a = props.attributes;
			var blockProps = useBlockProps( { className: 'screens ak-wide', style: a.accent ? { '--glow': a.accent } : undefined } );
			var pick = function ( key ) { return function ( m ) { var o = {}; o[ key + 'Id' ] = m ? m.id : 0; o[ key + 'Url' ] = m ? m.url : ''; props.setAttributes( o ); }; };
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'Website' },
						el( 'p', { className: 'ak-help' }, 'Computer: a screenshot of the first screen in a wide browser window. Phone: an ordinary screenshot from the phone. The browser and phone frames are drawn by the site — upload plain screenshots.' ),
						text( props, 'domain', 'Address shown in the browser bar', 'e.g. lenieboya.com' ),
						el( c.BaseControl, { label: 'Glow behind the screens', __nextHasNoMarginBottom: true },
							el( c.ColorPalette, { value: a.accent, onChange: setter( props, 'accent' ), colors: [ { name: 'AK orange', color: '#f37021' }, { name: 'Burgundy', color: '#8c1c2b' }, { name: 'Blue', color: '#2f5bd3' }, { name: 'Gold', color: '#b8892d' }, { name: 'Stone', color: '#6f665c' } ] } ) )
					),
					el( c.PanelBody, { title: 'More pages', initialOpen: false },
						shotList( props, 'pages', 'More computer screenshots', 'Other pages of the site, shown in browser windows below.' ),
						shotList( props, 'phones', 'More phone screenshots', 'Shown as a row of phones.' )
					)
				),
				el( 'div', blockProps,
					el( 'div', { className: 'screens__stage' },
						el( 'figure', { className: 'screens__browser' },
							el( 'div', { className: 'screens__bar' }, el( 'i' ), el( 'i' ), el( 'i' ), el( 'span', null, a.domain ) ),
							el( 'div', { className: 'screens__view' }, shotPicker( { id: a.desktopId, url: a.desktopUrl }, pick( 'desktop' ), 'Add the computer screenshot' ) ) ),
						el( 'figure', { className: 'screens__phone' }, el( 'div', { className: 'screens__screen' }, shotPicker( { id: a.mobileId, url: a.mobileUrl }, pick( 'mobile' ), 'Add the phone screenshot' ) ) )
					),
					( a.pages && a.pages.length ) ? el( 'div', { className: 'screens__pages' }, a.pages.map( function ( p, i ) {
						return el( 'figure', { key: i, className: 'screens__browser' }, el( 'div', { className: 'screens__bar' }, el( 'i' ), el( 'i' ), el( 'i' ), el( 'span', null, a.domain ) ), el( 'div', { className: 'screens__view' }, el( 'img', { src: p.url, alt: '' } ) ) );
					} ) ) : null,
					( a.phones && a.phones.length ) ? el( 'div', { className: 'screens__phones' }, a.phones.map( function ( p, i ) {
						return el( 'figure', { key: i, className: 'screens__phone' }, el( 'div', { className: 'screens__screen' }, el( 'img', { src: p.url, alt: '' } ) ) );
					} ) ) : null
				)
			);
		},
		save: save,
	} );

	/* ── Facts ─────────────────────────────────────────────────────── */

	registerBlockType( 'ak/facts', {
		edit: function ( props ) {
			var a = props.attributes;
			var blockProps = useBlockProps( { className: 'facts ak-wide' } );
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'Facts' },
						repeater( props, 'items', [ { key: 'label', label: 'Label', help: 'e.g. When' }, { key: 'value', label: 'Value', type: 'textarea' } ], 'Fact', 'label' ),
						text( props, 'linkUrl', 'Link (optional)' ),
						text( props, 'linkLabel', 'Link text', 'Empty uses the site address' )
					)
				),
				el( 'div', blockProps,
					( a.items && a.items.length ) ? el( 'dl', { className: 'facts__list' }, a.items.map( function ( f, i ) {
						return el( 'div', { key: i }, el( 'dt', null, f.label ), el( 'dd', null, f.value ) );
					} ) ) : el( 'p', { className: 'ak-empty' }, 'Add facts in the sidebar.' ),
					a.linkUrl ? el( 'span', { className: 'link-arrow' }, a.linkLabel || a.linkUrl ) : null
				)
			);
		},
		save: save,
	} );

	/* ── Event ─────────────────────────────────────────────────────── */

	registerBlockType( 'ak/event', {
		edit: function ( props ) {
			var a = props.attributes;
			var blockProps = useBlockProps( { className: 'event ak-full' } );
			var d = a.date ? new Date( a.date + 'T12:00:00' ) : null;
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'When and where' },
						text( props, 'date', 'Date', 'Shown as upcoming until this day, then as the latest.', 'date' ),
						text( props, 'venue', 'Venue' ),
						media( { id: a.imageId, url: a.imageUrl }, function ( m ) { props.setAttributes( { imageId: m ? m.id : 0, imageUrl: m ? m.url : '' } ); }, 'Image', 'image' )
					),
					el( c.PanelBody, { title: 'Before the date', initialOpen: false },
						text( props, 'eyebrowUpcoming', 'Small line above' ),
						text( props, 'primaryLabel', 'Button label' ),
						text( props, 'primaryUrl', 'Button link' )
					),
					el( c.PanelBody, { title: 'After the date', initialOpen: false },
						text( props, 'eyebrowPast', 'Small line above' ),
						text( props, 'pastLabel', 'Button label' ),
						text( props, 'pastUrl', 'Button link' )
					),
					el( c.PanelBody, { title: 'Second button', initialOpen: false },
						text( props, 'secondaryLabel', 'Label' ),
						text( props, 'secondaryUrl', 'Link' ),
						text( props, 'anchor', 'Section id', 'For links like #event' )
					)
				),
				el( 'section', blockProps, el( 'div', { className: 'ak-wrap event__grid' },
					el( 'div', { className: 'event__media' }, a.imageUrl ? el( 'img', { src: a.imageUrl, alt: '' } ) : null ),
					el( 'div', { className: 'event__body' },
						el( 'p', { className: 'eyebrow' }, a.eyebrowUpcoming + ' — ', rich( props, 'label', 'span', '', 'Event name', [] ) ),
						rich( props, 'title', 'h2', 'event__title display', 'Title', [ 'core/italic' ] ),
						el( 'div', { className: 'event__when' }, el( 'span', { className: 'event__day' }, d ? String( d.getDate() ) : '—' ),
							el( 'span', { className: 'event__month' }, d ? d.toLocaleDateString( 'en-GB', { month: 'long', year: 'numeric' } ) : 'Set a date in the sidebar', el( 'br' ), a.venue ) ),
						rich( props, 'note', 'p', 'lede', 'A line about the event' ),
						el( 'div', { className: 'event__ctas' }, a.primaryLabel ? el( 'span', { className: 'btn btn--accent' }, a.primaryLabel ) : null, a.secondaryLabel ? el( 'span', { className: 'btn btn--ghost' }, a.secondaryLabel ) : null )
					)
				) )
			);
		},
		save: save,
	} );

	/* ── Brief form ────────────────────────────────────────────────── */

	registerBlockType( 'ak/brief', {
		edit: function ( props ) {
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'Form' },
						toggle( props, 'compact', 'Short version', 'Name, email, needs and message only.' ),
						text( props, 'service', 'Preselected service (optional)', 'The address of a service page, e.g. website-development' ),
						text( props, 'source', 'Name in the brief notification', 'e.g. Home page' )
					)
				),
				preview( 'ak/brief', props )
			);
		},
		save: save,
	} );

	/* ── Journal ───────────────────────────────────────────────────── */

	registerBlockType( 'ak/journal', {
		edit: function ( props ) {
			var a = props.attributes;
			return el( Fragment, null,
				el( InspectorControls, null,
					el( c.PanelBody, { title: 'Journal' },
						el( c.RangeControl, { label: 'How many entries', min: 1, max: 12, value: a.count, onChange: setter( props, 'count' ) } ),
						text( props, 'category', 'Only this category (optional)', 'e.g. news' )
					)
				),
				preview( 'ak/journal', props )
			);
		},
		save: save,
	} );

	/* ── Card details for projects (and short titles for pages) ────── */

	var plugins = wp.plugins;
	var Panel = ( wp.editor && wp.editor.PluginDocumentSettingPanel ) || ( wp.editPost && wp.editPost.PluginDocumentSettingPanel );
	if ( plugins && Panel && wp.coreData ) {
		plugins.registerPlugin( 'ak-card-details', {
			render: function () {
				var postType = useSelect( function ( select ) { return select( 'core/editor' ).getCurrentPostType(); }, [] );
				var entity = wp.coreData.useEntityProp( 'postType', postType || 'post', 'meta' );
				var meta = entity[ 0 ];
				var setMeta = entity[ 1 ];
				if ( ( 'ak_project' !== postType && 'page' !== postType ) || ! meta ) { return null; }
				var field = function ( key, label, help, type ) {
					return el( c.TextControl, { key: key, label: label, help: help, type: type || 'text', value: meta[ key ] || '', __nextHasNoMarginBottom: true, onChange: function ( v ) { var p = {}; p[ key ] = v; setMeta( Object.assign( {}, meta, p ) ); } } );
				};
				if ( 'page' === postType ) {
					return el( Panel, { name: 'ak-card-details', title: 'Short title' }, field( 'ak_short_title', 'Short title for lists', 'Used by the Services list, e.g. “Photo & film”.' ) );
				}
				return el( Panel, { name: 'ak-card-details', title: 'Project card' },
					field( 'ak_short_title', 'Card title', 'Short name on cards, e.g. “LFD Season 8”.' ),
					field( 'ak_kind', 'Card label', 'e.g. Online store, Magazine, Runway season.' ),
					field( 'ak_location', 'City' ),
					field( 'ak_year', 'Year', 'Shown in the card corner.' ),
					field( 'ak_status', 'Status', 'Replaces the year, e.g. “In development”.' ),
					field( 'ak_date', 'Event date', 'A future date shows “Upcoming”.', 'date' ),
					el( c.ToggleControl, { label: 'Show on the home page', checked: !! meta.ak_featured, __nextHasNoMarginBottom: true, onChange: function ( v ) { setMeta( Object.assign( {}, meta, { ak_featured: v } ) ); } } )
				);
			},
		} );
	}
}( window.wp ) );
