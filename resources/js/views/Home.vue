<template>
    <div class="min-h-screen bg-slate-50">
        <header class="border-b border-slate-200 bg-white">
            <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
                <h1 class="text-lg font-semibold text-slate-800">Countries</h1>
                <div class="flex items-center gap-3">
                    <span v-if="user?.name" class="text-sm text-slate-600">{{ user.name }}</span>
                    <button
                        type="button"
                        @click="handleLogout"
                        class="py-2 px-4 rounded-lg text-sm font-medium text-slate-700 bg-slate-200 hover:bg-slate-300 transition-colors"
                    >
                        Log out
                    </button>
                </div>
            </div>
        </header>

        <main class="max-w-6xl mx-auto px-4 py-8">
            <div v-if="loading" class="text-center py-12 text-slate-500">
                Loading countries…
            </div>
            <div v-else-if="error" class="text-center py-12 text-red-600">
                {{ error }}
            </div>
            <div v-else-if="countries.length === 0" class="text-center py-12 text-slate-500">
                No countries to display.
            </div>
            <template v-else>
                <div
                    class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4"
                >
                    <CountryCard
                        v-for="country in countries"
                        :key="country.name"
                        :country="country"
                    />
                </div>
                <div
                    v-if="meta.last_page > 1"
                    class="mt-8 flex items-center justify-center gap-2 flex-wrap"
                >
                    <button
                        type="button"
                        :disabled="meta.current_page <= 1"
                        class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-200 text-slate-700 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-300 transition-colors"
                        @click="goToPage(meta.current_page - 1)"
                    >
                        Previous
                    </button>
                    <span class="px-3 py-2 text-sm text-slate-600">
                        Page {{ meta.current_page }} of {{ meta.last_page }}
                    </span>
                    <button
                        type="button"
                        :disabled="meta.current_page >= meta.last_page"
                        class="px-4 py-2 rounded-lg text-sm font-medium bg-slate-200 text-slate-700 disabled:opacity-50 disabled:cursor-not-allowed hover:bg-slate-300 transition-colors"
                        @click="goToPage(meta.current_page + 1)"
                    >
                        Next
                    </button>
                </div>
            </template>
        </main>
    </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuth } from '../composables/useAuth';
import api, { withAuth } from '../services/api';
import CountryCard from '../components/CountryCard.vue';

const { user, logout, getAccessTokenSilently } = useAuth();

const audience = (import.meta.env.VITE_AUTH0_AUDIENCE || '').trim();
const perPage = 20;
const countries = ref([]);
const meta = ref({
    current_page: 1,
    last_page: 1,
    per_page: perPage,
    total: 0,
});
const currentPage = ref(1);
const loading = ref(true);
const error = ref(null);

const appUrl = import.meta.env.VITE_APP_URL || window.location.origin;

function handleLogout() {
    logout({
        logoutParams: {
            returnTo: appUrl,
        },
    });
}

async function fetchCountries(page = 1) {
    loading.value = true;
    error.value = null;
    try {
        const token = await getAccessTokenSilently({
            authorizationParams: {
                ...(audience ? { audience } : {}),
            },
        });
        const res = await api.get('/countries', {
            ...withAuth(token),
            params: { page, per_page: perPage },
        });
        const payload = res.data;
        if (!payload || typeof payload !== 'object') {
            throw new Error('Unexpected API response.');
        }
        const rows = payload.data;
        if (!Array.isArray(rows)) {
            throw new Error('API response has no data array.');
        }

        countries.value = rows;
        if (payload.meta && typeof payload.meta === 'object') {
            meta.value = {
                current_page: payload.meta.current_page,
                last_page: payload.meta.last_page,
                per_page: payload.meta.per_page,
                total: payload.meta.total,
            };
        }
    } catch (e) {
        const msg = e.response?.data?.message;
        const detail = e.error_description || e.message;
        error.value =
            msg ||
            (detail ? `Failed to load countries: ${detail}` : 'Failed to load countries.');
    } finally {
        loading.value = false;
    }
}

function goToPage(page) {
    if (page < 1 || page > meta.value.last_page) return;
    currentPage.value = page;
    fetchCountries(page);
}

onMounted(() => fetchCountries(1));
</script>
