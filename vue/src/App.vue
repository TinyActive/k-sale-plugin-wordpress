<template>
    <component :is="currentComponent" />
    <Snackbar ref="snackbarRef" />
    <v-overlay :model-value="overlay" class="align-center justify-center" persistent>
        <v-progress-circular color="primary" size="64" indeterminate></v-progress-circular>
    </v-overlay>
</template>

<script>
import { computed, getCurrentInstance, provide, ref } from 'vue';

import SalePage from './pages/SalePage.vue';
import SeatSetting from './pages/SeatSetting.vue';
import ProductPage from './pages/ProductPage.vue';
import ProductAddPage from './pages/ProductAddPage.vue';
import InvoicePage from './pages/InvoicePage.vue';
import Snackbar from './common/global/Snackbar.vue';

export default {
    components: {
        SalePage,
        SeatSetting,
        ProductPage,
        ProductAddPage,
        InvoicePage,
        Snackbar
    },
    setup() {
        const snackbarRef = ref(null);
        const overlay = ref(false);
        provide('snackbar', (message, color = 'success') => {
            snackbarRef.value.show(message, color)
        });
        provide('overlay', overlay);
        const currentComponent = computed(() => {
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page');
            switch (page) {
                case 'setting-seat-page':
                    return 'SeatSetting';
                case 'k-sale':
                    return 'SalePage';
                case 'product-page':
                    return 'ProductPage';
                case 'add-product':
                    return 'ProductAddPage';
                case 'invoice':
                    return 'InvoicePage';
                default:
                    return null;
            }
        });

        // if url has page query param is k-sale, let add .fullscreen class to #app
        const init = () => {
            const urlParams = new URLSearchParams(window.location.search);
            const page = urlParams.get('page');
            if (page === 'k-sale') {
                document.getElementById('app').classList.add('fullscreen');
                document.querySelector('html').classList.add('over-hidden');
            }
        };

        init();

        return {
            currentComponent,
            snackbarRef,
            overlay
        };
    }
}
</script>

<style scoped>
::v-deep .v-card-text {
    padding: 0 !important;
}
</style>
