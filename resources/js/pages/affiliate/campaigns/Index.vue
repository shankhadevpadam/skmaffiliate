<script setup lang="ts">
import { index } from '@/actions/App/Http/Controllers/Affiliate/CampaignsController';
import DataTable from '@/components/datatable/DataTable.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { ucFirst } from '@/lib/utils';
import type { BreadcrumbItem, PaginatedData } from '@/types';
import { Head } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { h, ref } from 'vue';

interface Campaign {
    id: number;
    subject: string;
    content: string;
    status: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    campaigns: PaginatedData<Campaign>;
    filters: {
        filter?: {
            search?: string;
        };
        sort?: string;
    };
}

const props = defineProps<Props>();

interface DataTableInstance {
    toggleSort: (key: string) => void;
}

const dataTableRef = ref<DataTableInstance | null>(null);

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Campaigns',
        href: index().url,
    },
];

const columns: ColumnDef<Campaign>[] = [
    {
        accessorKey: 'id',
        header: 'ID',
        cell: ({ row }) =>
            h('div', { class: 'font-medium' }, row.getValue('id')),
    },
    {
        accessorKey: 'subject',
        header: 'Subject',
        cell: ({ row }) => {
            const campaign = row.original;

            return h('div', { class: 'flex flex-col' }, [
                h('div', { class: 'font-medium' }, campaign.subject),
            ]);
        },
    },
    {
        accessorKey: 'status',
        header: 'Status',
        cell: ({ row }) => {
            const status = String(row.getValue('status') || '').toLowerCase();
            const bgClasses: Record<string, string> = {
                success: 'bg-green-100 text-green-800',
                pending: 'bg-yellow-100 text-yellow-800',
                failed: 'bg-red-100 text-red-800',
                draft: 'bg-gray-100 text-gray-800',
            };
            const cls = `text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm ${bgClasses[status] ?? 'bg-gray-50 text-gray-700'}`;

            return h('span', { class: cls }, ucFirst(row.getValue('status')));
        },
    },
];
</script>

<template>

    <Head title="Campaigns" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-semibold tracking-tight">Campaigns</h1>

            <DataTable ref="dataTableRef" :columns="columns" :data="props.campaigns" :filters="props.filters"
                search-placeholder="Search by subject...">
            </DataTable>
        </div>
    </AppLayout>
</template>
