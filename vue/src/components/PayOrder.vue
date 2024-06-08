<template>
  <div class="pay-order">
    <v-dialog max-width="600" v-model="dialog" transition="dialog-bottom-transition" class="pay-order-dialog"
      persistent>
      <template v-slot:default="{ isActive }">
        <v-card rounded="lg">
          <v-card-title class="d-flex justify-space-between align-center">
            <div class="text-h5 text-medium-emphasis ps-2">
              Thanh toán - {{ order.LocationName }} - Bàn {{ order.TableName }}
            </div>

            <v-btn icon="mdi-close" variant="text" @click="isActive.value = false"></v-btn>
          </v-card-title>

          <v-divider></v-divider>

          <v-card-text>
            <div class="main-payorder">
              <div class="payment-method">
                <div class="box-content">
                  <div class="payment-method-child mb-2" :class="isActiveMethod(method) ? 'active' : ''" :key="index"
                    v-for="(method, index) in listPaymentMethod" @click="choosePaymentMethod(method)">
                    <v-icon v-if="isActiveMethod(method)" icon="mdi-check-circle" color="success"></v-icon>
                    {{ method.name }}
                  </div>
                </div>
              </div>
              <div class="calculator-panel">
                <div class="box-content">
                  <div class="total-amount box-mount">
                    <div class="label">Tổng tiền</div>
                    <div class="value money">
                      {{ utilsService.toCurrency(order.TotalAmount) }}
                    </div>
                  </div>
                  <div class="need-amount box-mount">
                    <div class="label">Cần thanh toán</div>
                    <div class="value money">
                      {{ utilsService.toCurrency(order.TotalAmount) }}
                    </div>
                  </div>
                  <div class="box-amount">
                    <div class="label">Khách trả</div>
                    <div class="input">
                      <v-text-field v-model="payAmount" variant="outlined" class="text-right money"
                        @focus="$event.target.select()" v-currency autofocus>
                      </v-text-field>
                    </div>
                  </div>
                  <div class="excess-amount">
                    <div class="label">Tiền thừa</div>
                    <div class="excess-amount-value money" :class="excessAmount < 0 ? 'warning-money' : ''">
                      {{ utilsService.toCurrency(excessAmount) }}
                    </div>
                  </div>
                  <div class="box-tip">
                    <v-checkbox v-model="isTakeMoney" :class="excessAmount > 0 || isTakeMoney ? 'active' : 'none'"
                      color="primary" label="Khách không lấy tiền thừa"></v-checkbox>
                  </div>
                </div>
              </div>
              <!-- <div class="calculator-suggest-panel"></div> -->
            </div>
          </v-card-text>

          <v-divider></v-divider>

          <v-card-actions class="v-card-actions">
            <v-btn width="100%" height="50px" class="text-none" color="primary" text="Send" variant="flat"
              @click="handleConfirmOrder">Xác nhận thanh toán</v-btn>
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
  name: "PayOrder",
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
    const isTakeMoney = ref(false);
    const utilsService = new UtilsService();
    const payAmount = ref(0);
    // tiền thừa
    const excessAmount = computed(() => {
      if (isTakeMoney.value) {
        return 0;
      }
      return (
        utilsService.toNumber(payAmount.value) -
        utilsService.toNumber(props.order.TotalAmount)
      );
    });

    const listPaymentMethod = [
      { id: 1, name: "Tiền mặt" },
      { id: 2, name: "Chuyển khoản" },
    ];

    const selectedMethod = ref(1);
    const isActiveMethod = (method) => {
      return method.id === selectedMethod.value;
    };

    const choosePaymentMethod = (method) => {
      selectedMethod.value = method.id;
    };

    watch(
      () => props.modelValue,
      (newVal) => {
        dialog.value = newVal;
      }
    );

    watch(dialog, (newValue) => {
      emit("update:modelValue", newValue);
    });

    const handleConfirmOrder = () => {
      const order = {
        OrderID: props.order.OrderID,
        PaymentMethod: selectedMethod.value,
        PaidAmount: utilsService.toNumber(props.order.TotalAmount),
        TipAmount: isTakeMoney.value ? utilsService.toNumber(payAmount.value) - utilsService.toNumber(props.order.TotalAmount) : 0,
        Status: OrderStatus.PAID,
        State: EditMode.EDIT
      };
      orderService.payOrder(order).then((res) => {
        if (res) {
          emit("success", true);
        }
      });
    };

    const init = () => {
      payAmount.value = utilsService.toCurrency2(props.order.TotalAmount);
    };

    init();
    return {
      dialog,
      utilsService,
      payAmount,
      excessAmount,
      listPaymentMethod,
      isActiveMethod,
      choosePaymentMethod,
      isTakeMoney,
      handleConfirmOrder
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
</style>