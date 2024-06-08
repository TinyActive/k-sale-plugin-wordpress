<template>
    <Layout>
        <template v-slot:title>Danh sách hóa đơn</template>
        <!-- <template v-slot:action>
        </template> -->
        <template v-slot:content>
            <div class="content-page">
                <v-data-table :hide-default-footer="false" :headers="headers" :items="items" item-value="name">
                    <template v-slot:item.InvoiceID="{ item }">
                        <div class="text-link" @click="handleToViewDetail(item.InvoiceID)">#{{ item.InvoiceID }}</div>
                    </template>
                    <template v-slot:item.InvoiceDate="{ item }">
                        <div @click="">{{ item.InvoiceDate }}</div>
                    </template>
                    <template v-slot:item.TotalAmount="{ item }">
                        <div @click="">{{ utilsService.toCurrency(item.TotalAmount) }}</div>
                    </template>
                </v-data-table>
            </div>
        </template>
    </Layout>
    <InvoiceDetail v-if="isShowInvoiceDetail" v-model="isShowInvoiceDetail" :invoice="invoice"></InvoiceDetail>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue';
import Layout from '@/layout/Layout.vue';
import UtilsService from '@/services/utilsService';
import invoiceService from '../services/invoiceService';
import InvoiceDetail from '../components/InvoiceDetail.vue';

export default {
    name: 'ProductPage',
    components: {
        Layout,
        InvoiceDetail
    },
    setup() {
        const utilsService = new UtilsService();
        const isShowInvoiceDetail = ref(false);
        const invoice = ref({});

        let headers = [
            {
                title: 'Số hóa đơn',
                align: 'center',
                key: 'InvoiceID',
                width: '150px',
            },
            {
                title: 'Số đơn hàng',
                align: 'end',
                key: 'OrderID',
            },
            {
                title: 'Ngày hóa đơn',
                align: 'center',
                key: 'InvoiceDate',
            },
            {
                title: 'Tổng tiền',
                align: 'center',
                key: 'TotalAmount',
            }
        ]

        let items = ref([]);

        const handleToViewDetail = (InvoiceID) => {
            invoiceService.getInvoice({ InvoiceID }).then((data) => {
                invoice.value = data;
                isShowInvoiceDetail.value = true;
            });
        }

        // Handle product list
        const handleInvoiceList = async () => {
            // Call API to get product list
            invoiceService.list().then((data) => {
                items.value = data.items;
            });
        }

        const init = () => {
            handleInvoiceList();
        }

        init();

        return {
            utilsService,
            headers,
            items,
            handleToViewDetail,
            isShowInvoiceDetail,
            invoice
        };
    },
};
</script>

<style scoped>
img.image-product {
    height: 40px;
    width: 40px;
    object-fit: cover;
}
</style>