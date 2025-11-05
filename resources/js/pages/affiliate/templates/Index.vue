<script setup lang="ts">
import DataTable from '@/components/datatable/DataTable.vue';
import Button from '@/components/ui/button/Button.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { index } from '@/actions/App/Http/Controllers/Affiliate/TemplatesController';
import type { BreadcrumbItem, PaginatedData, Subscriber } from '@/types';
import { Head } from '@inertiajs/vue3';
import type { ColumnDef } from '@tanstack/vue-table';
import { ArrowDown, ArrowUp, ArrowUpDown } from 'lucide-vue-next';
import { h, ref } from 'vue';
import DropdownAction from './components/DropdownAction.vue';

interface Template {
    id: number;
    name: string;
    content: string;
    created_at: string;
    updated_at: string;
}

interface Props {
    templates: PaginatedData<Template>;
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
        title: 'Templates',
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

const columns: ColumnDef<Template>[] = [
    {
        accessorKey: 'id',
        header: 'ID',
        cell: ({ row }) => h('div', { class: 'font-medium' }, row.getValue('id')),
    },
    {
        accessorKey: 'name',
        header: 'Name',
        cell: ({ row }) => {
            const template = row.original;

            return h('div', { class: 'flex flex-col' }, [
                h('div', { class: 'font-medium' }, template.name),
            ]);
        },
    },
    {
        id: 'actions',
        enableHiding: false,
        cell: ({ row }) => {
            const template = row.original;

            return h(
                'div',
                { class: 'relative' },
                h(DropdownAction, {
                    template,
                }),
            );
        },
    },
];
</script>

<template>

    <Head title="Templates" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 p-4">
            <h1 class="text-2xl font-semibold tracking-tight">
                Templates
            </h1>

            <DataTable ref="dataTableRef" :columns="columns" :data="props.templates" :filters="props.filters"
                search-placeholder="Search by name...">
            </DataTable>
        </div>
    </AppLayout>
</template>
