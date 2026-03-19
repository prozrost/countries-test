import { createApp } from 'vue';
import { createAuth0 } from '@auth0/auth0-vue';
import App from './App.vue';
import router from './router';

const domain = (import.meta.env.VITE_AUTH0_DOMAIN || '').trim();
const clientId = (import.meta.env.VITE_AUTH0_CLIENT_ID || '').trim();

if (!domain || !clientId) {
    const app = createApp({
        template: `
            <div class="min-h-screen flex items-center justify-center bg-slate-100 p-6">
                <div class="max-w-md rounded-lg border border-amber-200 bg-amber-50 p-6 text-center text-slate-800">
                    <p class="font-medium">Auth0 is not configured</p>
                    <p class="mt-2 text-sm text-slate-600">
                        Set VITE_AUTH0_DOMAIN and VITE_AUTH0_CLIENT_ID in your .env file, then rebuild the frontend.
                    </p>
                </div>
            </div>
        `,
    });
    app.mount('#app');
} else {
    const audience = (import.meta.env.VITE_AUTH0_AUDIENCE || '').trim();
    const appUrl = (import.meta.env.VITE_APP_URL || window.location.origin).replace(/\/$/, '');
    const redirectUri = `${appUrl}/callback`;

    const app = createApp(App);
    app.use(
        createAuth0({
            domain,
            clientId,
            authorizationParams: {
                redirect_uri: redirectUri,
                audience: audience || undefined,
            },
        })
    );
    app.use(router);
    app.mount('#app');
}
