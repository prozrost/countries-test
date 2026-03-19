import { watch } from 'vue';
import { createRouter, createWebHistory } from 'vue-router';
import { useAuth } from '../composables/useAuth';
import Home from '../views/Home.vue';
import Login from '../views/Login.vue';
import Callback from '../views/Callback.vue';

// Wait for Auth0 to finish hydrating before guards that need a token.
function waitUntilAuthReady(isLoading) {
    if (!isLoading.value) {
        return Promise.resolve();
    }
    return new Promise((resolve) => {
        const stop = watch(
            isLoading,
            (loading) => {
                if (!loading) {
                    stop();
                    resolve();
                }
            },
            { immediate: true }
        );
    });
}

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Login,
        meta: { guest: true },
    },
    {
        path: '/callback',
        name: 'callback',
        component: Callback,
        meta: { guest: true },
    },
    {
        path: '/',
        name: 'home',
        component: Home,
        meta: { requiresAuth: true },
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach(async (to) => {
    const { isAuthenticated, isLoading, loginWithRedirect } = useAuth();

    if (to.meta.requiresAuth) {
        await waitUntilAuthReady(isLoading);
        if (!isAuthenticated.value) {
            await loginWithRedirect({ appState: { targetUrl: to.fullPath } });
            return false;
        }
    }

    if (to.meta.guest && isAuthenticated.value && to.name === 'login') {
        return { name: 'home' };
    }

    return true;
});

export default router;
