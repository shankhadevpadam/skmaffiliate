<script setup lang="ts">
import DataTable from '@/components/DataTable.vue';
import { Badge } from '@/components/ui/badge';
import AppLayout from '@/layouts/AppLayout.vue';
import subscribersRoute from '@/routes/subscribers';
import type { BreadcrumbItem, PaginatedData, Subscriber } from '@/types';
import { Head } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { h } from 'vue';

interface Props {
    subscribers: PaginatedData<Subscriber>;
    filters: {
        search: string;
        per_page: number;
        sort: string;
        direction: 'asc' | 'desc';
    };
}

const props = defineProps<Props>();

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Subscribers',
        href: subscribersRoute.index().url,
    },
];

const columns: ColumnDef<Subscriber>[] = [
    {
        accessorKey: 'id',
        header: 'ID',
        cell: ({ row }) =>
            h('div', { class: 'font-medium' }, row.getValue('id')),
    },
    {
        accessorKey: 'full_name',
        header: 'Name',
        cell: ({ row }) => {
            const subscriber = row.original;
            return h('div', { class: 'flex flex-col' }, [
                h('div', { class: 'font-medium' }, subscriber.full_name),
                h(
                    'div',
                    { class: 'text-sm text-muted-foreground' },
                    subscriber.email,
                ),
            ]);
        },
    },
    {
        accessorKey: 'phone',
        header: 'Phone',
        cell: ({ row }) => h('div', {}, row.getValue('phone')),
    },
    {
        accessorKey: 'is_subscribed',
        header: 'Status',
        cell: ({ row }) => {
            const isSubscribed = row.getValue('is_subscribed');
            return h(
                Badge,
                {
                    variant: isSubscribed ? 'default' : 'secondary',
                },
                () => (isSubscribed ? 'Subscribed' : 'Unsubscribed'),
            );
        },
    },
    {
        accessorKey: 'created_at',
        header: 'Created At',
        cell: ({ row }) => {
            const date = new Date(row.getValue('created_at'));
            return h('div', {}, date.toLocaleDateString());
        },
    },
];
</script>

<template>
    <Head title="Subscribers" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-2xl font-semibold tracking-tight">
                        Subscribers
                    </h1>
                    <p class="text-sm text-muted-foreground">
                        Manage your subscribers and their information.
                    </p>
                </div>
            </div>

            <DataTable
                :columns="columns"
                :data="props.subscribers"
                :filters="props.filters"
                search-placeholder="Search by name, email, or phone..."
            />
        </div>
    </AppLayout>
</template>
