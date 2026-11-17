/**
 * Pixelgrid front-end script. No dependencies; the site works without it.
 *
 * 1. Pixel icons            4. Background grid and cursor trail
 * 2. Mosaics                5. Colour tones and section index
 * 3. Grid unit and snapping
 */
( function () {
	'use strict';

	var reduced = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;
	var root = document.documentElement;
	var body = document.body;
	var N = 11;
	var TONES = [ 'paper', 'blue', 'sun', 'night', 'fog', 'red' ];

	root.classList.add( 'pixelgrid-js' );

	/* 1. Pixel icons, 11 by 11. An image block picks one with the style "is-style-mosaic-{name}". */
	var icons = {
		screen: [ '...........', '###########', '###########', '###########', '###########', '###########', '###########', '###########', '....###....', '....###....', '..#######..' ],
		cloud: [ '...........', '...........', '....####...', '..#######..', '.#########.', '###########', '###########', '###########', '.#########.', '...........', '...........' ],
		lock: [ '...........', '...#####...', '..##...##..', '..##...##..', '..##...##..', '.#########.', '.#########.', '.####.####.', '.####.####.', '.#########.', '...........' ],
		chat: [ '...........', '.#########.', '###########', '###########', '###.#.#.###', '###########', '###########', '.#########.', '..###......', '..##.......', '..#........' ],
		heart: [ '...........', '..##...##..', '.####.####.', '###########', '###########', '###########', '.#########.', '..#######..', '...#####...', '....###....', '.....#.....' ],
		chart: [ '...........', '........###', '........###', '....###.###', '....###.###', '....###.###', '###.###.###', '###.###.###', '###.###.###', '###.###.###', '...........' ],
		people: [ '...........', '..##...##..', '.####.####.', '.####.####.', '..##...##..', '...........', '.####.####.', '###########', '###########', '###########', '...........' ],
		mail: [ '...........', '...........', '###########', '#.#######.#', '##.#####.##', '###.###.###', '####.#.####', '#####.#####', '###########', '###########', '...........' ],
		note: [ '..######...', '..#######..', '..########.', '..#......#.', '..########.', '..#......#.', '..########.', '..#...####.', '..########.', '..########.', '...........' ],
		lost: [ '...........', '...........', '#.#.###.#.#', '#.#.#.#.#.#', '#.#.#.#.#.#', '###.#.#.###', '..#.#.#...#', '..#.#.#...#', '..#.###...#', '...........', '...........' ]
	};
	var cycle = [ 'screen', 'cloud', 'lock', 'chat', 'heart' ];

	// Small repeatable random, so each icon always gets the same accent pixels.
	function seeded( text ) {
		var s = 7;
		for ( var i = 0; i < text.length; i++ ) {
			s = ( s * 31 + text.charCodeAt( i ) ) >>> 0;
		}
		return function () {
			s = ( s * 1664525 + 1013904223 ) >>> 0;
			return s / 4294967296;
		};
	}

	function plan( name ) {
		var rows = icons[ name ] || icons.screen;
		var rnd = seeded( name );
		var on = function ( x, y ) {
			return !! rows[ y ] && rows[ y ][ x ] === '#';
		};
		var cells = [];
		var edgeOff = [];
		var onList = [];
		var x, y, i, k;
		for ( y = 0; y < N; y++ ) {
			for ( x = 0; x < N; x++ ) {
				i = y * N + x;
				cells[ i ] = on( x, y ) ? 'is-on' : '';
				if ( on( x, y ) ) {
					onList.push( i );
				} else if ( on( x - 1, y ) || on( x + 1, y ) || on( x, y - 1 ) || on( x, y + 1 ) ) {
					edgeOff.push( i );
				}
			}
		}
		for ( k = 0; k < 5 && edgeOff.length; k++ ) {
			cells[ edgeOff.splice( Math.floor( rnd() * edgeOff.length ), 1 )[ 0 ] ] = 'is-tint';
		}
		for ( k = 0; k < 6; k++ ) {
			cells[ onList[ Math.floor( rnd() * onList.length ) ] ] = 'is-on is-wash';
		}
		return cells;
	}

	/* 2. Mosaics */
	var mosaics = [];

	function show( m, name ) {
		plan( name ).forEach( function ( state, i ) {
			m.children[ i ].className = 'pixelgrid-cell ' + state;
		} );
	}

	document.querySelectorAll( '.wp-block-image[class*="is-style-mosaic-"]' ).forEach( function ( figure ) {
		var img = figure.querySelector( 'img' );
		var match = figure.className.match( /is-style-mosaic-([a-z]+)/ );
		if ( ! img || ! match ) {
			return;
		}
		var name = match[ 1 ];
		var m = document.createElement( 'div' );
		m.className = 'pixelgrid-mosaic';
		m.setAttribute( 'role', 'img' );
		m.setAttribute( 'aria-label', img.getAttribute( 'alt' ) || '' );
		if ( ! img.getAttribute( 'alt' ) ) {
			m.setAttribute( 'aria-hidden', 'true' );
		}
		m.style.setProperty( '--pixelgrid-img', 'url("' + ( img.currentSrc || img.src ) + '")' );
		for ( var i = 0; i < N * N; i++ ) {
			var cell = document.createElement( 'div' );
			var px = document.createElement( 'i' );
			px.style.backgroundPosition = 'calc(var(--pixelgrid-u) * -' + ( i % N ) + ') calc(var(--pixelgrid-u) * -' + Math.floor( i / N ) + ')';
			px.style.transitionDelay = ( reduced ? 0 : Math.random() * 0.55 ).toFixed( 2 ) + 's';
			cell.appendChild( px );
			m.appendChild( cell );
		}
		show( m, name === 'cycle' ? cycle[ 0 ] : name );

		// Click a pixel to switch it on or off.
		m.addEventListener( 'click', function ( e ) {
			var target = e.target.closest( '.pixelgrid-cell' );
			if ( ! target ) {
				return;
			}
			target.className = target.classList.contains( 'is-on' ) ? 'pixelgrid-cell' : 'pixelgrid-cell is-on';
			m.dataset.edited = '1';
		} );

		if ( name === 'cycle' && ! reduced ) {
			var step = 0;
			window.setInterval( function () {
				if ( m.dataset.edited || document.hidden ) {
					return;
				}
				var r = m.getBoundingClientRect();
				if ( r.bottom < 0 || r.top > window.innerHeight ) {
					return;
				}
				step = ( step + 1 ) % cycle.length;
				show( m, cycle[ step ] );
			}, 3400 );
		}

		figure.insertBefore( m, figure.firstChild );
		figure.classList.add( 'pixelgrid-has-mosaic' );
		mosaics.push( m );
	} );

	if ( 'IntersectionObserver' in window ) {
		var seen = new IntersectionObserver( function ( entries ) {
			entries.forEach( function ( en ) {
				if ( en.isIntersecting ) {
					en.target.classList.add( 'is-in' );
				}
			} );
		}, { threshold: 0.25 } );
		mosaics.forEach( function ( m ) {
			seen.observe( m );
		} );
	} else {
		mosaics.forEach( function ( m ) {
			m.classList.add( 'is-in' );
		} );
	}

	/* 3 and 4. Grid unit, snapping, background canvas */
	var canvas = document.createElement( 'canvas' );
	canvas.id = 'pixelgrid-canvas';
	canvas.setAttribute( 'aria-hidden', 'true' );
	body.insertBefore( canvas, body.firstChild );
	var ctx = canvas.getContext( '2d' );
	var U = 60;
	var W = 0;
	var H = 0;
	var tone = { fg: '#101014', ac: '#2440FF' };
	var lit = new Map();
	var looping = false;

	function readTone() {
		var cs = window.getComputedStyle( body );
		tone = {
			fg: cs.getPropertyValue( '--pixelgrid-fg' ).trim() || '#101014',
			ac: cs.getPropertyValue( '--pixelgrid-ac' ).trim() || '#2440FF'
		};
	}

	function paint() {
		var off = -( window.scrollY % U );
		var firstRow = Math.floor( window.scrollY / U );
		ctx.clearRect( 0, 0, W, H );
		ctx.fillStyle = tone.ac;
		lit.forEach( function ( v, key ) {
			var parts = key.split( ',' );
			ctx.globalAlpha = v;
			ctx.fillRect( parts[ 0 ] * U, ( parts[ 1 ] - firstRow ) * U + off, U, U );
		} );
		ctx.globalAlpha = 0.11;
		ctx.strokeStyle = tone.fg;
		ctx.lineWidth = 1;
		ctx.beginPath();
		for ( var x = U; x < W; x += U ) {
			ctx.moveTo( x + 0.5, 0 );
			ctx.lineTo( x + 0.5, H );
		}
		for ( var y = off; y < H; y += U ) {
			ctx.moveTo( 0, y + 0.5 );
			ctx.lineTo( W, y + 0.5 );
		}
		ctx.stroke();
		ctx.globalAlpha = 1;
	}

	function loop() {
		lit.forEach( function ( v, key ) {
			v -= 0.022;
			if ( v <= 0 ) {
				lit.delete( key );
			} else {
				lit.set( key, v );
			}
		} );
		paint();
		if ( lit.size ) {
			window.requestAnimationFrame( loop );
		} else {
			looping = false;
		}
	}

	// Nudge each mosaic by less than one cell so its pixels land on the page grid.
	function snap() {
		mosaics.forEach( function ( m ) {
			m.style.transform = '';
			var r = m.getBoundingClientRect();
			var x = r.left;
			var y = r.top + window.scrollY;
			m.style.transform = 'translate(' + ( Math.round( x / U ) * U - x ) + 'px, ' + ( Math.round( y / U ) * U - y ) + 'px)';
		} );
	}

	function layout() {
		W = root.clientWidth;
		H = window.innerHeight;
		U = Math.max( 26, Math.round( W / ( W < 782 ? 12 : 24 ) ) );
		root.style.setProperty( '--pixelgrid-u', U + 'px' );
		var dpr = Math.min( 2, window.devicePixelRatio || 1 );
		canvas.width = W * dpr;
		canvas.height = H * dpr;
		ctx.setTransform( dpr, 0, 0, dpr, 0, 0 );
		snap();
		paint();
	}

	window.addEventListener( 'resize', layout );
	window.addEventListener( 'scroll', function () {
		if ( ! looping ) {
			paint();
		}
	}, { passive: true } );
	if ( ! reduced ) {
		window.addEventListener( 'pointermove', function ( e ) {
			if ( e.pointerType === 'touch' ) {
				return;
			}
			lit.set( Math.floor( e.clientX / U ) + ',' + Math.floor( ( e.clientY + window.scrollY ) / U ), 0.9 );
			if ( ! looping ) {
				looping = true;
				window.requestAnimationFrame( loop );
			}
		} );
	}
	if ( 'ResizeObserver' in window ) {
		new ResizeObserver( snap ).observe( body );
	}
	if ( document.fonts && document.fonts.ready ) {
		document.fonts.ready.then( snap );
	}

	/* 5. Colour tones and section index */
	function toneOf( el ) {
		for ( var i = 0; i < TONES.length; i++ ) {
			if ( el.classList.contains( 'is-style-tone-' + TONES[ i ] ) ) {
				return TONES[ i ];
			}
		}
		return 'paper';
	}

	function setTone( name ) {
		TONES.forEach( function ( t ) {
			body.classList.toggle( 'pixelgrid-tone-' + t, t === name );
		} );
		readTone();
		if ( ! looping ) {
			paint();
		}
	}

	var fields = Array.prototype.slice.call( document.querySelectorAll( 'main .wp-block-group[class*="is-style-tone-"]' ) );
	var dots = [];

	// Sections whose first heading gives them a name get a pixel in the index.
	var labelled = fields.filter( function ( f ) {
		return f.querySelector( 'h1, h2' );
	} );
	if ( labelled.length > 2 ) {
		var nav = document.createElement( 'nav' );
		nav.className = 'pixelgrid-index';
		nav.setAttribute( 'aria-label', document.documentElement.lang && document.documentElement.lang.indexOf( 'en' ) !== 0 ? '' : 'Sections on this page' );
		labelled.forEach( function ( f, i ) {
			if ( ! f.id ) {
				f.id = 'pixelgrid-section-' + ( i + 1 );
			}
			var a = document.createElement( 'a' );
			var s = document.createElement( 'span' );
			a.href = '#' + f.id;
			s.textContent = f.querySelector( 'h1, h2' ).textContent.trim();
			a.appendChild( s );
			nav.appendChild( a );
		} );
		body.appendChild( nav );
		dots = Array.prototype.slice.call( nav.children );
	}

	// The section in charge is the last one whose top has passed the middle of the screen.
	// Checking on scroll (not with an observer) means fast jumps and the footer area still land on the right colour.
	var current = null;
	var pending = false;

	function pickField() {
		var middle = window.innerHeight / 2;
		var pick = fields[ 0 ];
		for ( var i = 0; i < fields.length; i++ ) {
			if ( fields[ i ].getBoundingClientRect().top <= middle ) {
				pick = fields[ i ];
			}
		}
		return pick;
	}

	function updateTone() {
		pending = false;
		var field = pickField();
		if ( field === current ) {
			return;
		}
		current = field;
		setTone( toneOf( field ) );
		dots.forEach( function ( d ) {
			d.setAttribute( 'aria-current', d.getAttribute( 'href' ) === '#' + field.id ? 'true' : 'false' );
		} );
	}

	if ( fields.length ) {
		window.addEventListener( 'scroll', function () {
			if ( ! pending ) {
				pending = true;
				window.requestAnimationFrame( updateTone );
			}
		}, { passive: true } );
		window.addEventListener( 'resize', updateTone );
		updateTone();
	} else {
		setTone( 'paper' );
	}

	layout();
}() );
