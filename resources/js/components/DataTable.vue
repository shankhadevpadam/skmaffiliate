<script setup lang="ts" generic="TData, TValue">
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import type { PaginatedData } from '@/types';
import { router } from '@inertiajs/vue3';
import {
    FlexRender,
    getCoreRowModel,
    useVueTable,
    type ColumnDef,
} from '@tanstack/vue-table';
import {
    ChevronLeft,
    ChevronRight,
    ChevronsLeft,
    ChevronsRight,
    Search,
} from 'lucide-vue-next';
import { computed, ref, watch } from 'vue';

interface Props {
    columns: ColumnDef<TData, TValue>[];
    data: PaginatedData<TData>;
    filters?: {
        search?: string;
        per_page?: number;
        sort?: string;
        direction?: 'asc' | 'desc';
    };
    searchable?: boolean;
    searchPlaceholder?: string;
}

const props = withDefaults(defineProps<Props>(), {
    searchable: true,
    searchPlaceholder: 'Search...',
});

const searchQuery = ref(props.filters?.search || '');

const table = useVueTable({
    get data() {
        return props.data.data;
    },
    get columns() {
        return props.columns;
    },
    getCoreRowModel: getCoreRowModel(),
    manualPagination: true,
    manualSorting: true,
    manualFiltering: true,
    pageCount: props.data.meta.last_page,
});

const currentPage = computed(() => props.data.meta.current_page);
const totalPages = computed(() => props.data.meta.last_page);
const from = computed(() => props.data.meta.from || 0);
const to = computed(() => props.data.meta.to || 0);
const total = computed(() => props.data.meta.total);

const canGoPrevious = computed(() => currentPage.value > 1);
const canGoNext = computed(() => currentPage.value < totalPages.value);

function goToPage(page: number) {
    router.get(
        window.location.pathname,
        {
            ...props.filters,
            page,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

function search() {
    router.get(
        window.location.pathname,
        {
            ...props.filters,
            search: searchQuery.value,
            page: 1,
        },
        {
            preserveState: true,
            preserveScroll: true,
        },
    );
}

watch(
    () => props.filters?.search,
    (newSearch) => {
        searchQuery.value = newSearch || '';
    },
);
</script>

<template>
    <div class="w-full space-y-4">
        <div v-if="searchable" class="flex items-center gap-2">
            <div class="relative w-96">
                <Search
                    class="absolute top-1/2 left-3 size-4 -translate-y-1/2 text-muted-foreground"
                />
                <Input
                    v-model="searchQuery"
                    :placeholder="searchPlaceholder"
                    class="pl-9"
                    @keydown.enter="search"
                />
            </div>
            <Button @click="search"> Search </Button>
        </div>

        <div class="overflow-hidden rounded-lg border">
            <div class="overflow-x-auto">
                <table class="w-full caption-bottom text-sm">
                    <thead
                        class="border-b bg-muted/50 dark:bg-muted/20 [&_tr]:border-b"
                    >
                        <tr
                            v-for="headerGroup in table.getHeaderGroups()"
                            :key="headerGroup.id"
                            class="border-b transition-colors hover:bg-muted/50 dark:hover:bg-muted/30"
                        >
                            <th
                                v-for="header in headerGroup.headers"
                                :key="header.id"
                                class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&:has([role=checkbox])]:pr-0"
                            >
                                <FlexRender
                                    v-if="!header.isPlaceholder"
                                    :render="header.column.columnDef.header"
                                    :props="header.getContext()"
                                />
                            </th>
                        </tr>
                    </thead>
                    <tbody class="[&_tr:last-child]:border-0">
                        <template v-if="table.getRowModel().rows?.length">
                            <tr
                                v-for="row in table.getRowModel().rows"
                                :key="row.id"
                                class="border-b transition-colors hover:bg-muted/50 dark:hover:bg-muted/20"
                            >
                                <td
                                    v-for="cell in row.getVisibleCells()"
                                    :key="cell.id"
                                    class="p-4 align-middle [&:has([role=checkbox])]:pr-0"
                                >
                                    <FlexRender
                                        :render="cell.column.columnDef.cell"
                                        :props="cell.getContext()"
                                    />
                                </td>
                            </tr>
                        </template>
                        <tr v-else>
                            <td
                                :colspan="columns.length"
                                class="h-24 text-center"
                            >
                                No results.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div
            class="flex flex-col items-center justify-between gap-4 sm:flex-row"
        >
            <div class="text-sm text-muted-foreground">
                Showing {{ from }} to {{ to }} of {{ total }} results
            </div>

            <div class="flex items-center gap-2">
                <Button
                    variant="outline"
                    size="icon"
                    :disabled="!canGoPrevious"
                    @click="goToPage(1)"
                >
                    <ChevronsLeft class="size-4" />
                </Button>
                <Button
                    variant="outline"
                    size="icon"
                    :disabled="!canGoPrevious"
                    @click="goToPage(currentPage - 1)"
                >
                    <ChevronLeft class="size-4" />
                </Button>

                <div class="text-sm font-medium">
                    Page {{ currentPage }} of {{ totalPages }}
                </div>

                <Button
                    variant="outline"
                    size="icon"
                    :disabled="!canGoNext"
                    @click="goToPage(currentPage + 1)"
                >
                    <ChevronRight class="size-4" />
                </Button>
                <Button
                    variant="outline"
                    size="icon"
                    :disabled="!canGoNext"
                    @click="goToPage(totalPages)"
                >
                    <ChevronsRight class="size-4" />
                </Button>
            </div>
        </div>
    </div>
</template>
