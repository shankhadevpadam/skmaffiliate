<script setup lang="ts">
import { index } from '@/actions/App/Http/Controllers/Affiliate/CampaignsController';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Campaigns',
        href: index().url,
    },
];
</script>

<template>

    <Head title="Campaigns" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Campaigns
                    </h1>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
