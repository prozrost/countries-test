<template>
    <div class="min-h-screen flex flex-col items-center justify-center p-6 bg-slate-100">
        <div class="w-full max-w-sm rounded-xl bg-white shadow-lg border border-slate-200 p-8 text-center">
            <h1 class="text-xl font-semibold text-slate-800 mb-2">Countries</h1>
            <p class="text-slate-600 text-sm mb-6">Sign in to view the country grid.</p>
            <p v-if="errorFromQuery" class="mb-4 text-sm text-red-600">{{ errorFromQuery }}</p>
            <button
                type="button"
                @click="login"
                class="w-full py-3 px-4 rounded-lg bg-slate-800 text-white font-medium hover:bg-slate-700 transition-colors"
            >
                Log in with Auth0
            </button>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const route = useRoute();
const { loginWithRedirect } = useAuth();

const errorFromQuery = computed(() => {
    const err = route.query.error_description || route.query.error;
    return err ? String(err) : '';
});

function login() {
    loginWithRedirect();
}
</script>
