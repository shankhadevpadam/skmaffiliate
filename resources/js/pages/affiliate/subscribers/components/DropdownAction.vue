<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import DropdownMenu from '@/components/ui/dropdown-menu/DropdownMenu.vue';
import DropdownMenuContent from '@/components/ui/dropdown-menu/DropdownMenuContent.vue';
import DropdownMenuItem from '@/components/ui/dropdown-menu/DropdownMenuItem.vue';
import DropdownMenuLabel from '@/components/ui/dropdown-menu/DropdownMenuLabel.vue';
import DropdownMenuTrigger from '@/components/ui/dropdown-menu/DropdownMenuTrigger.vue';
import { destroy, edit } from '@/actions/App/Http/Controllers/Affiliate/SubscribersController';
import { router } from '@inertiajs/vue3';
import { ModalLink } from '@inertiaui/modal-vue';
import { Loader2, MoreHorizontal } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    subscriber: {
        id: number;
    };
}>();

const isDeleteDialogOpen = ref(false);
const isDeleting = ref(false);

function handleDelete() {
    isDeleting.value = true;

    router.delete(destroy(props.subscriber.id).url, {
        preserveState: true,
        preserveScroll: true,
        onSuccess: () => {
            isDeleteDialogOpen.value = false;
            isDeleting.value = false;
            toast.success('Subscriber delete successfully.');
        },
        onError: () => {
            isDeleting.value = false;
        },
    });
}
</script>

<template>
    <Dialog v-model:open="isDeleteDialogOpen">
        <DropdownMenu>
            <DropdownMenuTrigger as-child>
                <Button variant="ghost" class="h-8 w-8 p-0">
                    <span class="sr-only">Open menu</span>
                    <MoreHorizontal class="h-4 w-4" />
                </Button>
            </DropdownMenuTrigger>
            <DropdownMenuContent align="end">
                <DropdownMenuLabel>Actions</DropdownMenuLabel>
                <ModalLink
                    :href="edit(props.subscriber.id).url"
                    max-width="2xl"
                    :close-explicitly="true"
                    padding-classes="p-5"
                    position="top"
                >
                    <DropdownMenuItem> Edit </DropdownMenuItem>
                </ModalLink>
                <DropdownMenuItem @click="isDeleteDialogOpen = true">
                    Delete
                </DropdownMenuItem>
            </DropdownMenuContent>
        </DropdownMenu>

        <DialogContent>
            <DialogHeader>
                <DialogTitle>Delete Subscriber</DialogTitle>
                <DialogDescription>
                    Are you sure you want to delete this subscriber? This action
                    cannot be undone.
                </DialogDescription>
            </DialogHeader>
            <DialogFooter>
                <Button
                    variant="outline"
                    :disabled="isDeleting"
                    @click="isDeleteDialogOpen = false"
                >
                    Cancel
                </Button>
                <Button
                    variant="destructive"
                    :disabled="isDeleting"
                    @click="handleDelete"
                >
                    <Loader2
                        v-if="isDeleting"
                        class="mr-2 h-4 w-4 animate-spin"
                    />
                    Delete
                </Button>
            </DialogFooter>
        </DialogContent>
    </Dialog>
</template>
