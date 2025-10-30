<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { update } from '@/actions/App/Http/Controllers/Affiliate/SubscribersController';
import { useForm } from '@inertiajs/vue3';
import { Modal } from '@inertiaui/modal-vue';
import { Loader2Icon } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const props = defineProps<{
    subscriber: {
        id: number;
        first_name: string;
        last_name: string;
        email: string;
        phone: string;
    };
}>();

const modalRef = ref<{ close?: () => void } | null>(null);

const form = useForm({
    first_name: props.subscriber.first_name,
    last_name: props.subscriber.last_name,
    email: props.subscriber.email,
    phone: props.subscriber.phone,
});

const handleSuccess = () => {
    if (modalRef.value && typeof modalRef.value.close === 'function') {
        modalRef.value.close();
    }
    
    toast.success('Subscriber edit successfully.');
}
</script>

<template>
    <Modal ref="modalRef">
        <h1 class="mb-5 border-b pb-2 text-xl font-semibold">
            Edit Subscriber
        </h1>

        <form @submit.prevent="form.submit(update(props.subscriber.id), {
            preserveScroll: false,
            onSuccess: handleSuccess
        })">
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-6">
                    <Label class="mb-2 text-right"> First Name </Label>
                    <Input
                        id="first_name"
                        type="text"
                        name="first_name"
                        v-model="form.first_name"
                        class="py-5"
                        :class="{
                            'border-red-500 ring ring-red-500':
                                form.errors.first_name,
                        }"
                        placeholder="First Name"
                    />
                    <InputError
                        v-if="form.errors.first_name"
                        :message="form.errors.first_name"
                    />
                </div>
                <div class="col-span-6">
                    <Label class="mb-2 text-right"> Last Name </Label>
                    <Input
                        id="last_name"
                        type="text"
                        name="last_name"
                        v-model="form.last_name"
                        class="py-5"
                        :class="{
                            'border-red-500 ring ring-red-500':
                                form.errors.last_name,
                        }"
                        placeholder="Last Name"
                    />
                    <InputError
                        v-if="form.errors.last_name"
                        :message="form.errors.last_name"
                    />
                </div>
                <div class="col-span-6">
                    <Label class="mb-2 text-right"> Email </Label>
                    <Input
                        id="email"
                        type="email"
                        name="email"
                        v-model="form.email"
                        class="py-5"
                        :class="{
                            'border-red-500 ring ring-red-500':
                                form.errors.email,
                        }"
                        placeholder="Email"
                    />
                    <InputError
                        v-if="form.errors.email"
                        :message="form.errors.email"
                    />
                </div>
                <div class="col-span-6">
                    <Label class="mb-2 text-right"> Phone </Label>
                    <Input
                        id="phone"
                        type="text"
                        name="phone"
                        v-model="form.phone"
                        class="py-5"
                        :class="{
                            'border-red-500 ring ring-red-500':
                                form.errors.phone,
                        }"
                        placeholder="Phone"
                    />
                    <InputError
                        v-if="form.errors.phone"
                        :message="form.errors.phone"
                    />
                </div>
            </div>

            <Button type="submit" class="mt-5" :disabled="form.processing"
                >Update
                <Loader2Icon
                    v-if="form.processing"
                    class="h-4 w-4 animate-spin"
            /></Button>
        </form>
    </Modal>
</template>
