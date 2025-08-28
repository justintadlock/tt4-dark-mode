
import { store } from '@wordpress/interactivity';

const prefersDarkScheme = window.matchMedia( '(prefers-color-scheme: dark)' );

const { callbacks, state } = store( 'tt4-dark-mode/color-scheme', {
	state: {
		get isLoggedIn() {
			return 0 < state.userId;
		}
	},
	actions: {
		toggle() {
			state.isDark      = ! state.isDark;
			state.colorScheme = state.isDark ? 'dark' : 'light';

			// If the user is logged in, get the value from their
			// user meta instead of a cookie.
			if ( state.isLoggedIn ) {
				wp.apiFetch( {
					path: `/wp/v2/users/${state.userId}`,
					method: 'POST',
					data: {
						meta: {
							[state.name]: state.colorScheme
						}
					}
				} );
				return;
			}

			// Define the cookie path and domain.
			let path   = state.cookiePath || '/';
			let domain = state.cookieDomain ? "; domain=" + state.cookieDomain : '';

			// Save preference to a cookie.
			document.cookie = `${state.name}=${state.colorScheme};path=${path}${domain}`;
		}
	},
	callbacks: {
		init() {
			if ('dark' === state.colorScheme || 'light' === state.colorScheme) {
				return;
			}

			state.isDark = prefersDarkScheme.matches;
		},
		updateScheme() {
			document.documentElement.style.setProperty(
				'color-scheme',
				state.colorScheme
			);
		}
	}
});

prefersDarkScheme.addEventListener( 'change', () => {
	callbacks.init();
} );
