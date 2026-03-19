/**
 * Auth helpers – redirect URLs for Auth0.
 * Actual login/logout is done via @auth0/auth0-vue (useAuth composable).
 */

const getRedirectUri = () => {
    const base = import.meta.env.VITE_APP_URL || window.location.origin;
    return `${base}/callback`;
};

const getLogoutRedirectUri = () => {
    const base = import.meta.env.VITE_APP_URL || window.location.origin;
    return base;
};

export { getRedirectUri, getLogoutRedirectUri };
