<script setup lang="ts">
import DataTable from '@/components/datatable/DataTable.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/actions/App/Http/Controllers/Affiliate/SubscribersController';
import type { BreadcrumbItem, PaginatedData, Subscriber } from '@/types';
import { Head } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-vue-next';
import { h, ref } from 'vue';
import DropdownAction from './components/DropdownAction.vue';
import Create from './Create.vue';

interface Props {
    subscribers: PaginatedData<Subscriber>;
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
        title: 'Subscribers',
        href: index().url,
    },
];

const getSortIcon = (columnKey: string) => {
    const currentSort = props.filters.sort;

    if (currentSort === columnKey) {
        return ArrowUp;
    } else if (currentSort === `-${columnKey}`) {
        return ArrowDown;
    }
    return ArrowUpDown;
};

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
        accessorKey: 'email',
        header: () => {
            return h(
                Button,
                {
                    variant: 'ghost',
                    onClick: () => dataTableRef.value?.toggleSort('email'),
                },
                () => [
                    'Email',
                    h(getSortIcon('email'), { class: 'ml-2 h-4 w-4' }),
                ],
            );
        },
        cell: ({ row }) =>
            h('div', { class: 'lowercase' }, row.getValue('email')),
    },
    {
        accessorKey: 'phone',
        header: 'Phone',
        cell: ({ row }) => h('div', {}, row.getValue('phone')),
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const subscriber = row.original;

            return h(
                'div',
                { class: 'relative' },
                h(DropdownAction, {
                    subscriber,
                }),
            );
        },
    },
];

/* provide('actions', [
    {
        label: 'Import Subscribers',
        href: subscribersRoute.create().url,
        icon: Import,
    },
    {
        label: 'Add Subscriber',
        href: subscribersRoute.create().url,
        icon: Plus,
    },
]); */
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
                </div>
            </div>

            <DataTable ref="dataTableRef" :columns="columns" :data="props.subscribers" :filters="props.filters"
                search-placeholder="Search by name, email, or phone...">
                <template #actions>
                    <Create />
                </template>
            </DataTable>
        </div>
    </AppLayout>
</template>
