<script setup lang="ts">
import { ModalLink } from '@inertiaui/modal-vue';
import { inject, type Component } from 'vue';
import Button from '../ui/button/Button.vue';

const actions = inject<{ label: string; href: string; icon?: Component }[]>(
    'actions',
    [],
);
</script>

<template>
    <div v-if="actions.length" class="flex gap-2">
        <ModalLink
            v-for="action in actions"
            :key="action.label"
            :href="action.href"
            max-width="2xl"
            :close-explicitly="true"
            padding-classes="p-5"
            position="top"
        >
            <Button :href="action.href" type="button">
                <component :is="action.icon"></component> {{ action.label }}
            </Button>
        </ModalLink>
    </div>
</template>
