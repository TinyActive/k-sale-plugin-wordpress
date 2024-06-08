<template>
  <div class="main-list-order d-flex">
    <div class="main-list-oder">
      <div
        class="one-order-panel"
        v-for="(order, index) in listOrderViewer"
        :key="index"
      >
        <OrderPanel @close="handleListOrder" @click="openUpdateOder(order)" :order="order" />
      </div>
    </div>
    <div class="main-right">
      <div class="action-panel">
        <div class="box">
          <v-btn
            @click="createNewOrder"
            class="button-100"
            prepend-icon="mdi-plus"
            size="x-large"
          >
            <template v-slot:prepend>
              <v-icon color="success"></v-icon>
            </template>
            Tạo đơn mới</v-btn
          >
        </div>
        <!-- <div class="box">
          <v-btn class="button-100" prepend-icon="mdi-plus" size="x-large">
            <template v-slot:prepend>
              <v-icon color="success"></v-icon>
            </template>
            Tất cả đơn</v-btn
          >
        </div> -->
      </div>
    </div>
  </div>
  <CreateOrder
    v-if="isShowCreateOrder"
    v-model="isShowCreateOrder"
    :orderID="updateOrderID"
    @close="closeCreateOrder"
  ></CreateOrder>
</template>

<script>
import { ref, reactive, computed, watchEffect, watch } from "vue";
import ActionPanel from "@/components/ActionPanel.vue";
import OrderPanel from "@/components/OrderPanel.vue";
import CreateOrder from "@/components/CreateOrder.vue";
import orderService from "../services/OrderService";

export default {
  name: "ListOrder",
  components: {
    ActionPanel,
    OrderPanel,
    CreateOrder,
  },
  setup() {
    const isShowCreateOrder = ref(false);
    const updateOrderID = ref(null);

    watch(isShowCreateOrder, (value) => {
      if (value === false) {
        handleListOrder();
        updateOrderID.value = null;
      }
    });

    const listOrder = ref([]);

    const listOrderViewer = computed(() => {
      return listOrder.value;
    });

    const createNewOrder = () => {
      isShowCreateOrder.value = true;
    };

    const openUpdateOder = (order) => {
      updateOrderID.value = order.OrderID;
      isShowCreateOrder.value = true;
    };

    const handleListOrder = () => {
      orderService.unpaid().then((res) => {
        listOrder.value = res.items;
      });
    };

    const closeCreateOrder = () => {
      handleListOrder();
      isShowCreateOrder.value = false;
    };

    const init = () => {
      handleListOrder();
    };

    init();

    return {
      isShowCreateOrder,
      createNewOrder,
      listOrderViewer,
      openUpdateOder,
      updateOrderID,
      handleListOrder,
      closeCreateOrder
    };
  },
};
</script>

<style scoped>
.main-list-oder {
  flex: 1;
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 10px;
  padding: 10px;
  background: #01204ec4;
  grid-auto-rows: minmax(100px, 200px);
}

.main-right {
  width: 250px;
  background-color: #01204e;
}

.box {
  width: 100%;
  background: #01204e;
  padding: 10px;
}

.one-order-panel {
}
</style>