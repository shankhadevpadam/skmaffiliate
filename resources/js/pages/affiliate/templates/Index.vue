<script setup lang="ts">
import { index } from '@/actions/App/Http/Controllers/Affiliate/TemplatesController';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Templates',
        href: index().url,
    },
];

interface Template {
    id: number;
    name: string;
    content: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    templates: Template[];
}

const props = defineProps<Props>();
</script>

<template>

    <Head title="Templates" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-semibold tracking-tight">
                Templates
            </h1>

            <div v-for="template in props.templates" :key="template.id" class="mb-4 p-4 border rounded-md bg-gray-50">
                <h2 class="font-bold">{{ template.name }}</h2>
            </div>
        </div>
    </AppLayout>
</template>
