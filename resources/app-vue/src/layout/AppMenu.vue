<script setup>
import { ref } from 'vue';

import AppMenuItem from './AppMenuItem.vue';

const model = ref([
    {
        label: 'Menu',
        items: [
            { label: 'Dashboard', icon: 'pi pi-fw pi-th-large', to: '/' },
            { label: 'Transactions', icon: 'pi pi-arrow-right-arrow-left', to: 'transactions'},
            { label: 'Wallets', icon: 'pi pi-wallet', to: 'wallets' },
            { label: 'Categories', icon: 'pi pi-tags', to: 'categories' },
            { label: 'Reports', icon: 'pi pi-chart-bar', to: 'reports' },
            { label: 'Settings', icon: 'pi pi-cog', to: 'settings' },
            { label: 'Help', icon: 'pi pi-question-circle', to: 'help' }
        ]
    }
]);
</script>

<template>
    <ul class="layout-menu">
        <template v-for="(item, i) in model" :key="item">
            <app-menu-item v-if="!item.separator" :item="item" :index="i"></app-menu-item>
            <li v-if="item.separator" class="menu-separator"></li>
        </template>
    </ul>
</template>

<style lang="scss" scoped></style>
