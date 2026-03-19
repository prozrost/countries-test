import { useAuth0 } from '@auth0/auth0-vue';

/**
 * Simple auth composable wrapping Auth0.
 * Use isAuthenticated, user, loginWithRedirect, logout, getAccessTokenSilently.
 */
export function useAuth() {
    const auth0 = useAuth0();
    return {
        isAuthenticated: auth0.isAuthenticated,
        isLoading: auth0.isLoading,
        user: auth0.user,
        loginWithRedirect: auth0.loginWithRedirect,
        logout: auth0.logout,
        getAccessTokenSilently: auth0.getAccessTokenSilently,
    };
}
