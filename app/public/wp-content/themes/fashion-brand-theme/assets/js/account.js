/**
 * My Account — auth toggle, coupon copy, avatar modal upload.
 *
 * @package Fashion_Brand_Theme
 */

( function () {
	'use strict';

	function showAuth( which ) {
		var loginPanel = document.getElementById( 'auth-login' );
		var registerPanel = document.getElementById( 'auth-register' );

		if ( ! loginPanel ) {
			return;
		}

		loginPanel.classList.toggle( 'active', which === 'login' );

		if ( registerPanel ) {
			registerPanel.classList.toggle( 'active', which === 'register' );
		}
	}

	document.addEventListener( 'click', function ( event ) {
		var toggle = event.target.closest( '.auth-toggle[data-auth-panel]' );

		if ( toggle ) {
			event.preventDefault();
			showAuth( toggle.getAttribute( 'data-auth-panel' ) );
		}
	} );

	function copyText( text ) {
		if ( navigator.clipboard && navigator.clipboard.writeText ) {
			return navigator.clipboard.writeText( text );
		}

		return new Promise( function ( resolve, reject ) {
			var input = document.createElement( 'textarea' );
			input.value = text;
			input.setAttribute( 'readonly', '' );
			input.style.position = 'fixed';
			input.style.opacity = '0';
			document.body.appendChild( input );
			input.select();

			try {
				var ok = document.execCommand( 'copy' );
				document.body.removeChild( input );
				if ( ok ) {
					resolve();
				} else {
					reject();
				}
			} catch ( e ) {
				document.body.removeChild( input );
				reject( e );
			}
		} );
	}

	function showCopied( button ) {
		var defaultLabel =
			button.getAttribute( 'data-label-default' ) || button.textContent.trim();
		var copiedLabel =
			button.getAttribute( 'data-label-copied' ) || 'Copied ✓';

		button.textContent = copiedLabel;
		button.classList.add( 'is-copied' );
		button.setAttribute( 'aria-live', 'polite' );

		window.setTimeout( function () {
			button.textContent = defaultLabel;
			button.classList.remove( 'is-copied' );
		}, 1500 );
	}

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '[data-coupon-copy], .copy-btn[data-code]' );

		if ( ! button ) {
			return;
		}

		event.preventDefault();

		var code =
			button.getAttribute( 'data-coupon-code' ) ||
			button.getAttribute( 'data-code' ) ||
			'';

		if ( ! code ) {
			return;
		}

		copyText( code )
			.then( function () {
				showCopied( button );
			} )
			.catch( function () {
				/* ignore */
			} );
	} );

	/* -------------------------------------------------------------------------
	   Avatar change modal
	   ------------------------------------------------------------------------- */

	function initAvatarModal() {
		var modal = document.getElementById( 'avatar-modal' );
		var input = document.getElementById( 'account_avatar' );
		var cfg = window.fashionBrandThemeAccount || {};

		if ( ! modal || ! input || ! cfg.ajaxUrl ) {
			return;
		}

		var drop = modal.querySelector( '[data-avatar-drop]' );
		var preview = modal.querySelector( '[data-avatar-preview]' );
		var dropCopy = modal.querySelector( '[data-avatar-drop-copy]' );
		var errorEl = modal.querySelector( '[data-avatar-error]' );
		var saveBtn = modal.querySelector( '[data-avatar-save]' );
		var selectedFile = null;
		var objectUrl = '';

		function setError( message ) {
			if ( ! errorEl ) {
				return;
			}

			if ( message ) {
				errorEl.hidden = false;
				errorEl.textContent = message;
			} else {
				errorEl.hidden = true;
				errorEl.textContent = '';
			}
		}

		function clearSelection() {
			selectedFile = null;
			input.value = '';

			if ( objectUrl ) {
				URL.revokeObjectURL( objectUrl );
				objectUrl = '';
			}

			if ( preview ) {
				preview.hidden = true;
				preview.innerHTML = '';
			}

			if ( dropCopy ) {
				dropCopy.hidden = false;
			}

			if ( saveBtn ) {
				saveBtn.disabled = true;
			}

			setError( '' );
		}

		function openModal() {
			clearSelection();

			if ( typeof modal.showModal === 'function' ) {
				modal.showModal();
			} else {
				modal.setAttribute( 'open', '' );
			}

			document.documentElement.classList.add( 'avatar-modal-open' );
		}

		function closeModal() {
			clearSelection();

			if ( typeof modal.close === 'function' && modal.open ) {
				modal.close();
			} else {
				modal.removeAttribute( 'open' );
			}

			document.documentElement.classList.remove( 'avatar-modal-open' );
		}

		function acceptFile( file ) {
			if ( ! file || ! /^image\/(jpeg|png|gif|webp)$/i.test( file.type ) ) {
				setError( cfg.invalidType || 'Please choose a JPG, PNG, GIF or WebP image.' );
				return;
			}

			selectedFile = file;
			setError( '' );

			if ( objectUrl ) {
				URL.revokeObjectURL( objectUrl );
			}

			objectUrl = URL.createObjectURL( file );

			if ( preview ) {
				preview.hidden = false;
				preview.innerHTML =
					'<img src="' +
					objectUrl +
					'" alt="" class="avatar-modal__preview-img" />';
			}

			if ( dropCopy ) {
				dropCopy.hidden = true;
			}

			if ( saveBtn ) {
				saveBtn.disabled = false;
			}
		}

		function updatePageAvatars( url ) {
			document.querySelectorAll( '[data-account-avatar]' ).forEach( function ( el ) {
				var img = el.querySelector( '.avatar__img' );

				if ( img ) {
					img.src = url;
				} else {
					el.textContent = '';
					img = document.createElement( 'img' );
					img.className = 'avatar__img';
					img.src = url;
					img.alt = '';
					img.width = 96;
					img.height = 96;
					img.decoding = 'async';
					el.appendChild( img );
				}

				el.classList.add( 'avatar--has-img' );
			} );
		}

		function saveAvatar() {
			if ( ! selectedFile || ! saveBtn ) {
				return;
			}

			var formData = new FormData();
			formData.append( 'action', 'fashion_brand_theme_upload_avatar' );
			formData.append( 'nonce', cfg.nonce || '' );
			formData.append( 'account_avatar', selectedFile, selectedFile.name );

			saveBtn.disabled = true;
			saveBtn.classList.add( 'is-loading' );
			setError( '' );

			fetch( cfg.ajaxUrl, {
				method: 'POST',
				credentials: 'same-origin',
				body: formData,
			} )
				.then( function ( response ) {
					return response.json().then( function ( data ) {
						return { ok: response.ok, data: data };
					} );
				} )
				.then( function ( result ) {
					if (
						! result.data ||
						! result.data.success ||
						! result.data.data ||
						! result.data.data.url
					) {
						var message =
							( result.data &&
								result.data.data &&
								result.data.data.message ) ||
							cfg.uploadError ||
							'Upload failed. Please try again.';
						setError( message );
						saveBtn.disabled = false;
						return;
					}

					updatePageAvatars( result.data.data.url );
					closeModal();
				} )
				.catch( function () {
					setError( cfg.uploadError || 'Upload failed. Please try again.' );
					saveBtn.disabled = false;
				} )
				.finally( function () {
					saveBtn.classList.remove( 'is-loading' );
				} );
		}

		document.addEventListener( 'click', function ( event ) {
			if ( event.target.closest( '[data-avatar-open]' ) ) {
				event.preventDefault();
				openModal();
				return;
			}

			if ( event.target.closest( '[data-avatar-close]' ) ) {
				event.preventDefault();
				closeModal();
				return;
			}

			if ( event.target.closest( '[data-avatar-save]' ) ) {
				event.preventDefault();
				saveAvatar();
			}
		} );

		if ( drop ) {
			drop.addEventListener( 'click', function () {
				input.click();
			} );

			drop.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Enter' || event.key === ' ' ) {
					event.preventDefault();
					input.click();
				}
			} );

			drop.addEventListener( 'dragenter', function ( event ) {
				event.preventDefault();
				drop.classList.add( 'is-dragover' );
			} );

			drop.addEventListener( 'dragover', function ( event ) {
				event.preventDefault();
				drop.classList.add( 'is-dragover' );
			} );

			drop.addEventListener( 'dragleave', function () {
				drop.classList.remove( 'is-dragover' );
			} );

			drop.addEventListener( 'drop', function ( event ) {
				event.preventDefault();
				drop.classList.remove( 'is-dragover' );

				var files = event.dataTransfer && event.dataTransfer.files;

				if ( files && files[0] ) {
					acceptFile( files[0] );
				}
			} );
		}

		input.addEventListener( 'change', function () {
			if ( input.files && input.files[0] ) {
				acceptFile( input.files[0] );
			}
		} );

		modal.addEventListener( 'cancel', function ( event ) {
			event.preventDefault();
			closeModal();
		} );

		modal.addEventListener( 'click', function ( event ) {
			if ( event.target === modal ) {
				closeModal();
			}
		} );
	}

	if ( document.readyState === 'loading' ) {
		document.addEventListener( 'DOMContentLoaded', initAvatarModal );
	} else {
		initAvatarModal();
	}
}() );
