<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import Button from '@/components/ui/button/Button.vue';
import Input from '@/components/ui/input/Input.vue';
import Label from '@/components/ui/label/Label.vue';
import { store } from '@/actions/App/Http/Controllers/Affiliate/SubscribersController'
import { Form } from '@inertiajs/vue3';
import { Modal, ModalLink } from '@inertiaui/modal-vue';
import { FileCheck, FileSpreadsheet, Import, Loader2Icon, Plus } from 'lucide-vue-next';
import { ref } from 'vue';
import { toast } from 'vue-sonner';

const modalRef = ref<{ close?: () => void } | null>(null);

const selectedFileName = ref<string | null>(null);

const handleFileChange = (event: Event) => {
    const target = event.target as HTMLInputElement;
    const file = target.files?.[0];
    selectedFileName.value = file ? file.name : null;
};

const handleSuccess = () => {
    if (modalRef.value && typeof modalRef.value.close === 'function') {
        modalRef.value.close();
    }

    toast.success('Subscriber added successfully.');
};
</script>

<template>
    <div class="flex space-x-2">
        <ModalLink href="#import-subscriber" max-width="2xl" :close-explicitly="true" padding-classes="p-5"
            position="top">
            <Button type="button">
                Import Subscriber <Import />
            </Button>
        </ModalLink>
        <ModalLink href="#add-subscriber" max-width="2xl" :close-explicitly="true" padding-classes="p-5" position="top">
            <Button type="button">
                Add Subscriber <Plus />
            </Button>
        </ModalLink>
    </div>

    <Modal ref="modalRef" name="add-subscriber">
        <h1 class="mb-5 border-b pb-2 text-xl font-semibold">Add Subscriber</h1>

        <Form :action="store()" #default="{ errors, processing }" @success="handleSuccess" resetOnSuccess>
            <div class="grid grid-cols-12 gap-4">
                <div class="col-span-6">
                    <Label class="mb-2 text-right"> First Name </Label>
                    <Input id="first_name" type="text" name="first_name" class="py-5" :class="{
                        'border-red-500 ring ring-red-500':
                            errors.first_name,
                    }" placeholder="First Name" />
                    <InputError v-if="errors.first_name" :message="errors.first_name" />
                </div>
                <div class="col-span-6">
                    <Label class="mb-2 text-right"> Last Name </Label>
                    <Input id="last_name" type="text" name="last_name" class="py-5" :class="{
                        'border-red-500 ring ring-red-500':
                            errors.last_name,
                    }" placeholder="Last Name" />
                    <InputError v-if="errors.last_name" :message="errors.last_name" />
                </div>
                <div class="col-span-6">
                    <Label class="mb-2 text-right"> Email </Label>
                    <Input id="email" type="email" name="email" class="py-5" :class="{
                        'border-red-500 ring ring-red-500': errors.email,
                    }" placeholder="Email" />
                    <InputError v-if="errors.email" :message="errors.email" />
                </div>
                <div class="col-span-6">
                    <Label class="mb-2 text-right"> Phone </Label>
                    <Input id="phone" type="text" name="phone" class="py-5" :class="{
                        'border-red-500 ring ring-red-500': errors.phone,
                    }" placeholder="Phone" />
                    <InputError v-if="errors.phone" :message="errors.phone" />
                </div>
            </div>

            <Button type="submit" class="mt-5" :disabled="processing">Submit
                <Loader2Icon v-if="processing" class="h-4 w-4 animate-spin" />
            </Button>
        </Form>
    </Modal>

    <Modal ref="modalRef" name="import-subscriber">
        <h1 class="mb-5 border-b pb-2 text-xl font-semibold">Import Subscriber</h1>

        <div class="space-y-2">
            <div :class="[
                'relative rounded-lg border-2 border-dashed bg-white p-6 transition-colors',
                selectedFileName
                    ? 'border-green-500 bg-green-50'
                    : 'border-gray-300 hover:border-blue-500',
            ]">
                <input type="file" name="file" @change="handleFileChange" accept=".csv,application/csv,.xlsx"
                    class="absolute inset-0 h-full w-full cursor-pointer opacity-0" />
                <div class="text-center">
                    <FileCheck v-if="selectedFileName" :size="32" class="mx-auto text-green-600" />
                    
                    <FileSpreadsheet v-else :size="32" class="mx-auto text-gray-400" />

                    <p v-if="selectedFileName" class="mt-2 text-sm font-medium text-green-700">
                        {{ selectedFileName }}
                    </p>
                    <p v-else class="mt-2 text-sm text-gray-600">
                        Click to upload or drag and drop
                    </p>
                    <p class="text-xs text-gray-500">
                        CSV, XLSX up to 10MB
                    </p>
                </div>
            </div>

            <Button type="button">Import</Button>
        </div>
    </Modal>
</template>
