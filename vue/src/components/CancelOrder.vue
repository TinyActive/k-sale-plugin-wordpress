<template>
    <div class="cancel-order">
        <v-dialog max-width="600" v-model="dialog" transition="dialog-bottom-transition" class="cancel-order-dialog"
            persistent>
            <template v-slot:default="{ isActive }">
                <v-card rounded="lg">
                    <v-card-title class="d-flex justify-space-between align-center">
                        <div class="text-h5 text-medium-emphasis ps-2">
                            Hủy đơn - {{ order.LocationName }} - Bàn {{ order.TableName }}
                        </div>

                        <v-btn icon="mdi-close" variant="text" @click="isActive.value = false"></v-btn>
                    </v-card-title>

                    <v-divider></v-divider>

                    <v-card-text>
                        <div class="main-cancelorder">
                            <v-radio-group v-model="reasonCancel" label="Nhập lý do hủy">
                                <v-radio label="Thêm nhầm đơn" value="Thêm nhầm đơn"></v-radio>
                                <v-radio label="Khách báo hủy" value="Khách báo hủy"></v-radio>
                                <v-radio label="Đổi trả lại" value="Đổi trả lại"></v-radio>
                                <v-radio label="Lý do khác" value="other"></v-radio>
                            </v-radio-group>
                            <v-textarea v-model="reasonCancelText" :disabled="reasonCancel != 'other'" clearable label="Lý do khác" variant="outlined"></v-textarea>
                        </div>
                    </v-card-text>

                    <v-divider></v-divider>

                    <v-card-actions class="v-card-actions">
                        <div></div>
                        <v-btn  width="50%" height="50px" class="text-none" text="Send" variant="flat" @click="dialog = false">Hủy thao
                            tác</v-btn>
                        <v-btn width="50%" height="50px" class="text-none" color="primary" variant="flat" @click="cancelOrder">Xác
                            nhận</v-btn>
                    </v-card-actions>
                </v-card>
            </template>
        </v-dialog>
    </div>
</template>

<script>
import { ref, reactive, computed, watch } from "vue";
import UtilsService from "../services/UtilsService";
import orderService from "../services/OrderService";
import { EditMode, OrderStatus } from "../common/constant";

export default {
    name: "CancelOrder",
    props: {
        modelValue: {
            type: Boolean,
            required: true,
        },
        order: {
            type: Object,
            required: true,
        },
    },
    setup(props, { emit }) {
        const dialog = ref(props.modelValue);
        const reasonCancel = ref("Thêm nhầm đơn");
        const reasonCancelText = ref("");
        const utilsService = new UtilsService();

        const cancelOrder = () => {
            const cancelText = reasonCancel.value == "other" ? reasonCancelText.value : reasonCancel.value;
            const order = {
                OrderID: props.order.OrderID,
                State: EditMode.EDIT,
                Status: OrderStatus.CANCEL,
                CancelReason: cancelText,
            };
            orderService.cancelOrder(order).then((res) => {
                if (res) {
                    emit("updateOrder");
                    dialog.value = false;
                }
            });
        };

        watch(
            () => props.modelValue,
            (newVal) => {
                dialog.value = newVal;
            }
        );

        watch(reasonCancel, (newValue) => {
            if(newValue == "other") {
                // auto focus to textarea
                setTimeout(() => {
                    document.querySelector(".v-textarea textarea").focus();
                }, 10);
            }
        });

        watch(dialog, (newValue) => {
            emit("update:modelValue", newValue);
        });



        const init = () => {
        };

        init();
        return {
            dialog,
            utilsService,
            reasonCancel,
            cancelOrder,
            reasonCancelText
        };
    },
};
</script>

<style scoped>
.main-payorder {
    display: flex;
}

.main-payorder>div {
    padding: 6px;
}

.payment-method {
    width: 40%;
}

.calculator-panel {
    width: 60%;
}

.calculator-suggest-panel {
    /* width: 30%; */
}

.pay-order-dialog :deep(.v-card-text) {
    background: #f2f2f2;
}

.box-mount {
    display: flex;
    justify-content: space-between;
    padding: 16px 8px;
    border-radius: 8px;
    margin-bottom: 8px;
}

.total-amount {
    background: #f4f6f8;
    border: 1px solid #c4cdd5;
}

.need-amount {
    background: #e5f3ff;
    border: 1px solid #99cfff;
}

.box-amount {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.box-amount .label {
    width: 100px;
}

.box-amount .input {
    flex: 1;
}

.box-amount :deep(.v-input__details) {
    display: none;
}

.excess-amount {
    display: flex;
    justify-content: space-between;
    align-items: center;
    height: 50px;
}

.money {
    font-size: 18px;
}

.warning-money {
    color: red;
    font-weight: bold;
}

.payment-method-child {
    height: 50px;
    display: flex;
    align-items: center;
    position: relative;
    width: 100%;
    border-radius: 6px;
    border: 1px solid #c4cdd5;
    padding: 0 8px;
    cursor: pointer;
}

.payment-method-child.active {
    border: 1px solid #4caf50;
}

.payment-method-child i {
    position: absolute;
    top: 3px;
    right: 4px;
}

.box-content {
    height: 100%;
}

.v-card-actions {
    padding: 0;
    min-height: unset;
}

.box-tip label {
    font-size: 15px !important;
}

.box-tip .none {
    visibility: hidden;
}

.cancel-order-dialog :deep(.v-card-actions .v-btn~.v-btn:not(.v-btn-toggle .v-btn)) {
    margin-inline-start: 0;
}
.cancel-order-dialog :deep(.v-radio-group>.v-input__control>.v-label) {
    margin-inline-start: 0;
}
</style>