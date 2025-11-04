<script setup lang="ts">
import { storeCampaign } from '@/actions/App/Http/Controllers/Affiliate/TemplatesController';
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import Textarea from '@/components/ui/textarea/Textarea.vue';
import { useForm } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Loader2Icon } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    template: {
        id: number;
        name: string;
    };
}>();

const modalRef = ref<{ close?: () => void } | null>(null);

const form = useForm({
    subject: '',
    content: '',
});

const handleSuccess = () => {
    if (modalRef.value && typeof modalRef.value.close === 'function') {
        modalRef.value.close();
    }

    toast.success('Campaign created successfully.');
}
</script>

<template>
    <Modal ref="modalRef">
        <h1 class="mb-5 border-b pb-2 text-xl font-semibold">
            Create Campaign from Template
        </h1>

        <form @submit.prevent="form.submit(storeCampaign(props.template.id), {
            preserveScroll: false,
            onSuccess: handleSuccess
        })">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-12">
                    <Label class="mb-2 text-right"> Campaign Subject </Label>
                    <Input id="subject" type="text" name="subject" v-model="form.subject" class="py-5" :class="{
                        'border-red-500 ring ring-red-500':
                            form.errors.subject,
                    }" placeholder="Campaign Subject" />
                    <InputError v-if="form.errors.subject" :message="form.errors.subject" />
                </div>

                <div class="col-span-12">
                    <Label class="mb-2 text-right"> Campaign Content </Label>
                    <Textarea id="content" name="content" v-model="form.content" :class="{
                        'border-red-500 ring ring-red-500':
                            form.errors.content,
                    }" placeholder="Type your content here." />
                    <InputError v-if="form.errors.content" :message="form.errors.content" />
                </div>
            </div>

            <Button type="submit" class="mt-5" :disabled="form.processing"
                >Create Campaign
                <Loader2Icon
                    v-if="form.processing"
                    class="h-4 w-4 animate-spin"
            /></Button>
        </form>
    </Modal>
</template>