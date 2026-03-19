<template>
    <article
        class="rounded-xl border border-slate-200 bg-white shadow-sm overflow-hidden hover:shadow-md transition-shadow"
    >
        <div class="aspect-video bg-slate-100 flex items-center justify-center p-2">
            <img
                :src="imageSrc"
                :alt="country.name"
                class="max-h-full w-auto object-contain"
                loading="lazy"
                @error="broken = true"
            />
        </div>
        <div class="p-3">
            <h2 class="font-medium text-slate-800 truncate">{{ country.name }}</h2>
        </div>
    </article>
</template>

<script setup>
import { ref, computed, watch } from 'vue';

const placeholder = '/images/flag-placeholder.svg';

const props = defineProps({
    country: {
        type: Object,
        required: true,
        validator: (c) => c && typeof c.name === 'string',
    },
});

const broken = ref(false);
watch(
    () => props.country.name,
    () => {
        broken.value = false;
    }
);

const imageSrc = computed(() =>
    broken.value || !props.country.flag ? placeholder : props.country.flag
);
</script>
