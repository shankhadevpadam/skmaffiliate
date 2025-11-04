<script setup lang="ts">
import { createCampaign } from '@/actions/App/Http/Controllers/Affiliate/TemplatesController';
import Button from '@/components/ui/button/Button.vue';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/dropdown-menu/DropdownMenuLabel.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import { ModalLink } from '@inertiaui/modal-vue';
import { MoreHorizontal } from 'lucide-vue-next';

const props = defineProps<{
    template: {
        id: number;
    };
}>();
</script>

<template>
    <DropdownMenu>
        <DropdownMenuTrigger as-child>
            <Button variant="ghost" class="h-8 w-8 p-0">
                <span class="sr-only">Open menu</span>
                <MoreHorizontal class="h-4 w-4" />
            </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
            <DropdownMenuLabel>Actions</DropdownMenuLabel>
            <ModalLink :href="createCampaign(props.template.id).url" max-width="2xl" :close-explicitly="true"
                padding-classes="p-5" position="top">
                <DropdownMenuItem> Create Campaign </DropdownMenuItem>
            </ModalLink>
        </DropdownMenuContent>
    </DropdownMenu>
</template>
