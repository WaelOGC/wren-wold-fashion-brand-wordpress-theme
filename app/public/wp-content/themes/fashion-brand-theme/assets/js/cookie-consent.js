/**
 * Cookie consent bar — accept / reject / customize + durable cookie storage.
 *
 * @package Fashion_Brand_Theme
 */
( function () {
	'use strict';

	const root = document.querySelector( '[data-cookie-consent]' );

	if ( ! root ) {
		return;
	}

	const cfg = window.fashionBrandThemeCookieConsent || {};
	const cookieName = cfg.cookieName || 'wrenwold_cookie_consent';
	const version = typeof cfg.version === 'number' ? cfg.version : 1;
	const maxAgeDays = typeof cfg.maxAgeDays === 'number' ? cfg.maxAgeDays : 365;

	const prefs = root.querySelector( '[data-cookie-prefs]' );
	const customizeBtn = root.querySelector( '[data-cookie-customize]' );
	const analyticsInput = root.querySelector( '[data-cookie-category="analytics"]' );
	const marketingInput = root.querySelector( '[data-cookie-category="marketing"]' );

	function buildConsent( analytics, marketing ) {
		return {
			necessary: true,
			analytics: !! analytics,
			marketing: !! marketing,
			ts: Math.floor( Date.now() / 1000 ),
			v: version,
		};
	}

	function writeConsentCookie( consent ) {
		const maxAge = Math.max( 1, maxAgeDays ) * 24 * 60 * 60;
		const value = encodeURIComponent( JSON.stringify( consent ) );
		const secure = window.location.protocol === 'https:' ? '; Secure' : '';
		document.cookie =
			cookieName +
			'=' +
			value +
			'; path=/; max-age=' +
			maxAge +
			'; SameSite=Lax' +
			secure;
	}

	function emitConsentUpdated( consent ) {
		try {
			document.dispatchEvent(
				new CustomEvent( 'wrenwold:consent-updated', {
					detail: consent,
				} )
			);
		} catch ( e ) {
			/* ignore */
		}
	}

	function dismissBanner() {
		root.hidden = true;
		document.body.classList.remove( 'has-cookie-consent' );
	}

	function commitConsent( consent ) {
		writeConsentCookie( consent );
		emitConsentUpdated( consent );
		dismissBanner();
	}

	function setPrefsOpen( open ) {
		if ( ! prefs || ! customizeBtn ) {
			return;
		}

		prefs.hidden = ! open;
		customizeBtn.setAttribute( 'aria-expanded', open ? 'true' : 'false' );
		root.classList.toggle( 'is-prefs-open', open );
	}

	document.body.classList.add( 'has-cookie-consent' );

	root.querySelector( '[data-cookie-accept-all]' )?.addEventListener( 'click', function () {
		commitConsent( buildConsent( true, true ) );
	} );

	root.querySelector( '[data-cookie-reject]' )?.addEventListener( 'click', function () {
		commitConsent( buildConsent( false, false ) );
	} );

	customizeBtn?.addEventListener( 'click', function () {
		const willOpen = !!( prefs && prefs.hidden );
		setPrefsOpen( willOpen );
	} );

	root.querySelector( '[data-cookie-save]' )?.addEventListener( 'click', function () {
		commitConsent(
			buildConsent(
				analyticsInput ? analyticsInput.checked : false,
				marketingInput ? marketingInput.checked : false
			)
		);
	} );

	// Escape collapses Customize only — never dismisses without an explicit choice.
	document.addEventListener( 'keydown', function ( event ) {
		if ( event.key !== 'Escape' ) {
			return;
		}

		if ( prefs && ! prefs.hidden ) {
			setPrefsOpen( false );
			customizeBtn?.focus();
		}
	} );
}() );
