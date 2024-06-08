<template>
  <div class="order-panel">
    <div class="header">
      <div class="location">{{ order.LocationName }}</div>
      <div class="number-of-people"><v-icon icon="mdi-account"></v-icon></div>
    </div>
    <div class="number-of-location">{{ order.TableName }}</div>
    <div class="timing-and-money">
      <div class="timing">
        <div class="icon">
          <v-icon icon="mdi-clock-fast"></v-icon>
        </div>
        <div class="value">{{ formattedOrderDate }}</div>
      </div>
      <div class="money">
        <div class="icon">
          <v-icon icon="mdi-currency-usd"></v-icon>
        </div>
        <div class="value">
          {{ utilsService.toCurrency(order.TotalAmount) }}
        </div>
      </div>
    </div>
    <div class="action-button">
      <v-btn class="text-none" @click.stop="cancelPopup" variant="flat" color="danger"> Hủy đơn </v-btn>
      <v-btn class="text-none" @click.stop="payOrderPopup" variant="flat" color="info"> Thanh toán </v-btn>
    </div>
    <PayOrder @success="success" v-if="isShowPayOrder" :order="order" v-model="isShowPayOrder"></PayOrder>
    <CancelOrder @updateOrder="updateCancelOrder" v-if="isShowCancelOrder" :order="order" v-model="isShowCancelOrder"></CancelOrder>
  </div>
</template>

<script>
import { ref, reactive, computed, watchEffect } from "vue";
import UtilsService from "@/services/UtilsService";
import PayOrder from "@/components/PayOrder.vue";
import CancelOrder from "@/components/CancelOrder.vue";

export default {
  name: "OrderPanel",
  components: { PayOrder, CancelOrder },
  props: {
    order: {
      type: Object,
      required: true,
    },
  },
  setup(props, { emit }) {
    const utilsService = new UtilsService();
    const isShowPayOrder = ref(false);
    const isShowCancelOrder = ref(false);
    const tick = ref(0);

    setInterval(() => {
      tick.value++;
    }, 1000); // update every 1 second

    const success = () => {
      emit("close", true);
    };

    const updateCancelOrder = () => {
      emit("close", true);
    };

    const formattedOrderDate = computed(() => {
      tick.value; // force update computed property
      const orderDate = new Date(props.order.OrderDate);
      var date = new Date();
      var utcDate = new Date(date.getTime() + date.getTimezoneOffset() * 60000);
      // calcilating time from now to order date
      const diff = utcDate.getTime() - orderDate.getTime();
      // convert diff to hours, minutes, seconds
      let hours = Math.floor(diff / (1000 * 60 * 60));
      let minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
      let seconds = Math.floor((diff % (1000 * 60)) / 1000);
      // i want if number < 10 then add 0 before number
      hours = hours < 10 ? `0${hours}` : hours;
      minutes = minutes < 10 ? `0${minutes}` : minutes;
      seconds = seconds < 10 ? `0${seconds}` : seconds;
      return `${hours}:${minutes}:${seconds}`;
    });

    const payOrderPopup = () => {
      isShowPayOrder.value = true;
    };

    const cancelPopup = () => {
      isShowCancelOrder.value = true;
    };

    return {
      utilsService,
      formattedOrderDate,
      payOrderPopup,
      isShowPayOrder,
      cancelPopup,
      isShowCancelOrder,
      updateCancelOrder,
      success
    };
  },
};
</script>

<style scoped>
.one-order-panel {
  height: 200px;
}

.order-panel {
  width: 100%;
  background: #fff;
  height: 100%;
  border-radius: 6px;
}

.order-panel>div {
  padding: 10px;
  box-sizing: border-box;
}

.header {
  display: flex;
  justify-content: space-between;
  background: #01204e47;
}

.number-of-location {
  display: flex;
  justify-content: center;
  align-items: center;
  border-bottom: 1px solid #01204e47;
  height: 50px;
  font-size: 24px;
  font-weight: bold;
}

.timing-and-money {
  display: flex;
  justify-content: space-between;
  border-bottom: 1px solid #01204e47;
  height: 50px;
}

.timing-and-money>div {
  display: flex;
  align-items: center;
}

.action-button {
  display: flex;
  justify-content: space-between;
  align-items: center;
}
</style>