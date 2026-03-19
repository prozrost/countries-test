<template>
    <div class="min-h-screen flex flex-col items-center justify-center gap-4 p-6 bg-slate-50">
        <p class="text-slate-600">Completing sign in…</p>
        <p v-if="errorMessage" class="text-sm text-red-600">{{ errorMessage }}</p>
    </div>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import { useAuth } from '../composables/useAuth';

const router = useRouter();
const route = useRoute();
const { isAuthenticated, isLoading } = useAuth();
const errorMessage = ref('');

onMounted(() => {
    const err = route.query.error;
    if (err) {
        errorMessage.value = route.query.error_description || `Sign-in failed: ${err}. Try again.`;
    }
});

watch([isAuthenticated, isLoading], ([auth, loading]) => {
    if (!loading && auth) {
        router.replace({ name: 'home' });
        return;
    }
    if (!loading && !auth && errorMessage.value) {
        router.replace({ name: 'login', query: { error: route.query.error } });
    }
}, { immediate: true });
</script>
