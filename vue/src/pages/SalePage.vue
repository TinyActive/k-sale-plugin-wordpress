<template>
    <div class="sale-page">
        <v-card>
            <v-tabs v-model="tab" bg-color="primary">
                <v-tab value="one">Tất cả đơn</v-tab>
                <!-- <v-tab value="two">Sơ đồ bàn</v-tab> -->
            </v-tabs>

            <v-card-text>
                <v-tabs-window v-model="tab">
                    <v-tabs-window-item value="one">
                        <ListOrder />
                    </v-tabs-window-item>

                    <v-tabs-window-item value="two">
                        213
                    </v-tabs-window-item>
                </v-tabs-window>
            </v-card-text>
        </v-card>
        
    </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue';
import ListOrder from '@/components/ListOrder.vue';
import ActionPanel from '@/components/ActionPanel.vue';
import CreateOrder from '@/components/CreateOrder.vue';

export default {
    name: 'SalePage',
    components: {
        ListOrder,
        ActionPanel,
        CreateOrder
    },
    setup() {
        const tab = ref('one');
        const isShowCreateOrder = ref(false);
        return {
            tab,
            isShowCreateOrder
        };
    },
};
</script>

<style scoped>
/* Your CSS styles here */
</style>