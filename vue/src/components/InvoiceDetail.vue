<template>
    <div class="pay-order">
        <v-dialog max-width="600" v-model="dialog" transition="dialog-bottom-transition" class="pay-order-dialog"
            persistent>
            <template v-slot:default="{ isActive }">
                <v-card rounded="lg">
                    <v-card-title class="d-flex justify-space-between align-center">
                        <div class="text-h5 text-medium-emphasis ps-2 align-center">
                            Hóa đơn thanh toán
                        </div>

                        <v-btn icon="mdi-close" variant="text" @click="isActive.value = false"></v-btn>
                    </v-card-title>

                    <v-divider></v-divider>

                    <v-card-text>
                        <div class="main-invoice">
                            <div class="title">Số hóa đơn: #{{ invoiceModal.InvoiceID }}</div>
                            <div class="content">
                                <div class="content-item">
                                    <div class="label">Ngày tạo:</div>
                                    <div class="value">{{ invoiceModal.InvoiceDate }}</div>
                                </div>
                                <div class="content-item">
                                    <div class="label">Người tạo:</div>
                                    <div class="value">admin</div>
                                </div>
                                <div class="content-item">
                                    <div class="label">Ngày thanh toán:</div>
                                    <div class="value">{{ invoiceModal.InvoiceDate }}</div>
                                </div>
                            </div>
                            <div class="list-detail-invoice">
                                <v-data-table :hide-default-footer="true" :headers="headers" :items="items"
                                    item-value="name">
                                    <template v-slot:item.Price="{ item }">
                                        {{ utilsService.toCurrency(item.Price) }}
                                    </template>
                                    <template v-slot:item.TotalAmount="{ item }">
                                        {{ utilsService.toCurrency(item.TotalAmount) }}
                                    </template>
                                </v-data-table>
                            </div>
                            <div class="content-sum">
                                <div class="content-item-sum">
                                    <div class="label">Tổng thanh toán:</div>
                                    <div class="value">{{ utilsService.toCurrency(invoiceModal.TotalAmount) }}</div>
                                </div>
                            </div>
                        </div>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions class="v-card-actions">

                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script>
import { ref, reactive, computed, watch } from "vue";
import UtilsService from "../services/utilsService";

export default {
    name: "InvoiceDetail",
    props: {
        modelValue: {
            type: Boolean,
            required: true,
        },
        invoice: {
            type: Object,
            required: true,
        },
    },
    setup(props, { emit }) {
        const dialog = ref(props.modelValue);
        const invoiceDetails = props.invoice.InvoiceDetails;
        const invoiceModal = props.invoice.Invoice;
        const utilsService = new UtilsService();
        const headers = [
            {
                title: 'STT',
                align: 'center',
                key: 'Index',
                width: '50px',
                sortable: false,
            },
            {
                title: 'Tên sản phẩm',
                align: 'start',
                key: 'ProductName',
                sortable: false,
            },
            {
                title: 'Đơn giá',
                align: 'end',
                key: 'Price',
                sortable: false,
            },
            {
                title: 'SL',
                align: 'end',
                key: 'Quantity',
                sortable: false,
            },
            {
                title: 'Tổng tiền',
                align: 'end',
                key: 'TotalAmount',
                sortable: false,
            }
        ]

        let items = ref([]);

        watch(
            () => props.modelValue,
            (newVal) => {
                dialog.value = newVal;
            }
        );

        watch(dialog, (newValue) => {
            emit("update:modelValue", newValue);
        });



        const init = () => {
            if (invoiceDetails && invoiceDetails.length > 0) {
                for (let i = 0; i < invoiceDetails.length; i++) {
                    items.value.push({
                        Index: i + 1,
                        InvoiceID: invoiceDetails[i].InvoiceID,
                        ProductName: invoiceDetails[i].ProductName,
                        Quantity: invoiceDetails[i].Quantity,
                        Price: invoiceDetails[i].Price,
                        TotalAmount: invoiceDetails[i].TotalAmount,
                    });
                }
            }
        };

        init();
        return {
            dialog,
            headers,
            items,
            invoiceModal,
            utilsService
        };
    },
};
</script>

<style scoped>
.title {
    text-align: center;
    font-size: 18px;
    font-weight: bold;
}

.content-item .label {
    width: 130px;
    font-weight: bold;
}

.content-item {
    display: flex;
    margin-bottom: 8px;
   
}

.content-item-sum{
    display: flex;
    justify-content: space-between;
    margin-bottom: 8px;
    margin-top: 16px;
    font-weight: bold;
    font-size: 18px;
}

.content {
    margin-top: 26px;
    margin-bottom: 23px;
}
</style>