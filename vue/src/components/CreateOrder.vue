<template>
  <div>
    <v-dialog v-model="dialog" transition="dialog-bottom-transition" fullscreen>
      <v-card>
        <div class="main-create-oder">
          <div class="left-create-oder">
            <div class="header">
              <div class="search">
                <v-text-field v-model="textSearch" autofocus prepend-inner-icon="mdi-magnify" bg-color="#fff"
                  variant="outlined"></v-text-field>
              </div>
            </div>
            <div class="list-product">
              <div class="one-product" :key="index" v-for="(product, index) in listProductsFilter">
                <Product @click="addToOrder(product)" :product="product" />
              </div>
            </div>
          </div>
          <div class="right-create-oder">
            <div class="choose-location">
              <div class="selected-table" v-if="selectedLocation">
                <div class="table-number">
                  {{ selectedLocation.Location.LocationName }} - Bàn số:
                  {{ selectedLocation.Table.TableName }}
                </div>
                <v-btn variant="flat" height="100%" width="100%" @click="isShowTable = true">Chọn bàn</v-btn>
              </div>
              <v-btn v-else variant="flat" height="100%" width="100%" @click="isShowTable = true">Chọn bàn</v-btn>
            </div>
            <div class="list-order">
              <div class="wrap-one-row-selected" @click="activeProduct(detail)" :class="detail.isActive ? 'active' : ''"
                :key="index" v-for="(detail, index) in listProductsInOrder" v-show="detail && detail.State != 3">
                <div class="one-row-selected">
                  <div class="stt">{{ index + 1 }}</div>
                  <div class="box-name">
                    <div class="name">{{ detail.ProductName }}</div>
                    <div class="description">
                      Giá: {{ utilService.toCurrency(detail.Price) }}
                    </div>
                  </div>
                  <div class="quantity">{{ detail.Quantity }}</div>
                  <div class="price">
                    {{ utilService.toCurrency(detail.Price * detail.Quantity) }}
                  </div>
                </div>
                <div class="action" v-if="detail.isActive">
                  <v-btn variant="flat" @click="handleQuantity(detail, 1)">Thêm</v-btn>
                  <v-btn variant="flat" @click="handleQuantity(detail, -1)">Giảm</v-btn>
                  <v-btn variant="flat" color="red" @click="deleteProduct(detail, -1)">Xóa</v-btn>
                </div>
              </div>
            </div>
            <v-divider></v-divider>
            <div class="price-info">
              <div class="total-title">Tổng tiền</div>
              <div class="total-amount">
                {{ utilService.toCurrency(totalPrice) }}
              </div>
            </div>
            <div class="action-list">
              <v-btn @click="dialog = false" color="red" variant="flat">Thoát</v-btn>
              <v-btn @click="saveOrder" :disabled="!listProductsInOrder.length" color="success" variant="flat">Lưu
                đơn</v-btn>
              <v-btn @click="handlePayOrder" :disabled="!listProductsInOrder.length" color="primary"
                variant="flat">Thanh toán</v-btn>
            </div>
          </div>
        </div>
      </v-card>
    </v-dialog>
    <Location @chooseTable="chooseTable" v-model="isShowTable"></Location>
    <PayOrder @success="success" v-if="isShowPayOrder" :order="order.Order" v-model="isShowPayOrder"></PayOrder>
  </div>
</template>

<script>
import { ref, reactive, computed, watch, watchEffect } from "vue";
import Product from "@/components/Product.vue";
import productService from "@/services/productService";
import UtilService from "../services/UtilsService";
import Location from "@/components/Location.vue";
import orderService from "@/services/orderService";
import { EditMode } from "@/common/constant";
import { OrderStatus } from "../common/constant";
import PayOrder from "@/components/PayOrder.vue";

export default {
  name: "CreateOrder",
  components: {
    Product,
    Location,
    PayOrder
  },
  props: {
    modelValue: {
      type: Boolean,
      default: false,
    },
    orderID: {
      type: Number,
      default: null,
    },
  },
  setup(props, { emit }) {
    const dialog = ref(props.modelValue);
    const isShowTable = ref(false);
    const utilService = new UtilService();
    const isShowPayOrder = ref(false);
    const selectedLocation = ref(null);
    const listProducts = ref([]);
    const listProductsInOrder = ref([]);
    const textSearch = ref("");
    let order = reactive({
      Order: {
        TotalAmount: 0,
        PaymentMethod: 1,
        LocationID: null,
        TableID: null,
        Status: OrderStatus.CONFIRM,
        Note: null,
      },
      OrderDetails: [],
    });

    watch(
      () => props.modelValue,
      (newVal) => {
        dialog.value = newVal;
      }
    );

    watch(dialog, (newValue) => {
      emit("update:modelValue", newValue);
    });

    const success = () => {
      emit("close", true);
    };

    const listProductsFilter = computed(() => {
      return listProducts.value.filter((product) => {
        const productName = utilService.removeAccents(product.ProductName.toLowerCase());
        const textSearchValue = utilService.removeAccents(textSearch.value.trim().toLowerCase());
        return productName.includes(textSearchValue);
      });
    });

    const totalPrice = computed(() => {
      return listProductsInOrder.value.reduce((total, item) => {
        if (item.State === EditMode.DELETE) {
          return total;
        }
        return total + item.Price * item.Quantity;
      }, 0);
    });

    const handleListProduct = () => {
      productService.list().then((res) => {
        listProducts.value = res.items;
      });
    };

    const removeActiveInProduct = () => {
      listProductsInOrder.value.forEach((product) => {
        product.isActive = false;
      });
    };

    const addToOrder = (product) => {
      removeActiveInProduct();
      const productInOrder = listProductsInOrder.value.find(
        (item) => item.ProductID === product.ProductID
      );
      if (productInOrder) {
        productInOrder.Quantity++;
        productInOrder.isActive = true;

        if (productInOrder.State === EditMode.NONE) {
          productInOrder.State = EditMode.EDIT;
        }
      } else {
        listProductsInOrder.value.push({
          ...product,
          Quantity: 1,
          isActive: true,
          State: EditMode.ADD,
        });
      }
    };

    const handleQuantity = (product, quantity) => {
      product.Quantity += quantity;

      if (product.State === EditMode.NONE) {
        product.State = EditMode.EDIT;
      }

      if (product.Quantity <= 0) {
        deleteProduct(product, quantity);
      }
    };

    const deleteProduct = (product) => {
      const index = listProductsInOrder.value.findIndex(
        (item) => item.ProductID === product.ProductID
      );
      // update state
      if (product.State === EditMode.ADD) {
        listProductsInOrder.value.splice(index, 1);
      } else {
        listProductsInOrder.value[index].State = EditMode.DELETE;
      }

      // sure update state
      listProductsInOrder.value.push();
    };

    const activeProduct = (product) => {
      removeActiveInProduct();
      product.isActive = true;
    };

    const prepareOrder = () => {
      // if order has OrderID => update order
      // if order has not OrderID => create new order
      if (order.Order.OrderID) {
        order.Order.State = EditMode.EDIT;
        order.Order.TotalAmount = totalPrice.value;
        order.Order.LocationID =
          selectedLocation.value &&
            selectedLocation.value.Location &&
            selectedLocation.value.Location.LocationID
            ? selectedLocation.value.Location.LocationID
            : null;
        order.Order.TableID =
          selectedLocation.value &&
            selectedLocation.value.Table &&
            selectedLocation.value.Table.TableID
            ? selectedLocation.value.Table.TableID
            : null;
        order.Order.LocationName =
          selectedLocation.value &&
            selectedLocation.value.Location &&
            selectedLocation.value.Location.LocationName
            ? selectedLocation.value.Location.LocationName
            : null;
        order.Order.TableName =
          selectedLocation.value &&
            selectedLocation.value.Table &&
            selectedLocation.value.Table.TableName
            ? selectedLocation.value.Table.TableName
            : null;
        // order.Status = OrderStatus.CONFIRM;
        order.Order.Note = null;
        order.OrderDetails = listProductsInOrder.value.map((item) => {
          return {
            ProductID: item.ProductID,
            ProductName: item.ProductName,
            Quantity: item.Quantity,
            Price: item.Price,
            TotalAmount: item.Price * item.Quantity,
            Note: "",
            State: item.State,
            OrderDetailID: item.OrderDetailID,
          };
        });
      } else {
        order.Order.State = EditMode.ADD;
        order.Order.TotalAmount = totalPrice.value;
        order.Order.LocationID =
          selectedLocation.value &&
            selectedLocation.value.Location &&
            selectedLocation.value.Location.LocationID
            ? selectedLocation.value.Location.LocationID
            : null;
        order.Order.TableID =
          selectedLocation.value &&
            selectedLocation.value.Table &&
            selectedLocation.value.Table.TableID
            ? selectedLocation.value.Table.TableID
            : null;
        order.Order.LocationName =
          selectedLocation.value &&
            selectedLocation.value.Location &&
            selectedLocation.value.Location.LocationName
            ? selectedLocation.value.Location.LocationName
            : null;
        order.Order.TableName =
          selectedLocation.value &&
            selectedLocation.value.Table &&
            selectedLocation.value.Table.TableName
            ? selectedLocation.value.Table.TableName
            : null;
        order.Order.Status = OrderStatus.UNPAID;
        order.Order.Note = "";
        order.OrderDetails = listProductsInOrder.value.map((item) => {
          return {
            ProductID: item.ProductID,
            ProductName: item.ProductName,
            Quantity: item.Quantity,
            Price: item.Price,
            TotalAmount: item.Price * item.Quantity,
            Note: "",
            State: item.State,
            OrderDetailID: item.OrderDetailID,
          };
        });
      }
      return order;
    }

    const handlePayOrder = () => {
      saveOrder(false).then(() => {
        isShowPayOrder.value = true;
      });
    };

    const saveOrder = async (isShowLocation = true) => {
      if (listProductsInOrder.value.length === 0) {
        return;
      }
      const order = prepareOrder();
      const saveOrderParam = order;
      // Thực hiện việc lưu thông tin đơn hàng mà chưa cần ngay location
      const res = await orderService.saveOrder(saveOrderParam);
      // cập nhật lại trạng thái cho đơn hàng trên chương trình
      order.Order.OrderID = res.Order.OrderID;
      listProductsInOrder.value = res.OrderDetails;
      // update EDIT mode None for all detail
      listProductsInOrder.value.forEach((item) => {
        item.State = EditMode.NONE;
      });

      if (!selectedLocation.value && isShowLocation) {
        isShowTable.value = true;
      }
    };

    const chooseTable = (location) => {
      selectedLocation.value = location;

      // cập nhật đơn hàng
      saveOrder();
    };

    // handle exist order
    const handleExistOrder = () => {
      if (props.orderID) {
        orderService.getOrder({ OrderID: props.orderID }).then((res) => {
          order.Order = res.Order;
          order.OrderDetails = res.OrderDetails;
          listProductsInOrder.value = res.OrderDetails;
          selectedLocation.value = {
            Location: {
              LocationID: res.Order.LocationID,
              LocationName: res.Order.LocationName,
            },
            Table: {
              TableID: res.Order.TableID,
              TableName: res.Order.TableName,
            },
          };
        });
      }
    };

    const init = () => {
      handleExistOrder();
      handleListProduct();
    };

    init();

    return {
      saveOrder,
      dialog,
      listProducts,
      addToOrder,
      listProductsInOrder,
      utilService,
      handleQuantity,
      deleteProduct,
      activeProduct,
      totalPrice,
      isShowTable,
      chooseTable,
      selectedLocation,
      isShowPayOrder,
      handlePayOrder,
      order, success, textSearch,
      listProductsFilter
    };
  },
};
</script>

<style scoped>
.main-create-oder {
  display: flex;
  height: 100%;
}

.left-create-oder {}

.right-create-oder {
  width: 400px;
  display: flex;
  flex-direction: column;
}

.left-create-oder {
  flex: 1;
  background: #3c5477;
  padding: 10px;
}

.list-product {
  display: grid;
  grid-template-columns: repeat(6, 1fr);
  gap: 10px;
  max-height: calc(100% - 76.39px);
  overflow: auto;

  /* Add these lines to customize the scrollbar */
  scrollbar-width: thin;
  scrollbar-color: rgba(155, 155, 155, 0.7) transparent;
}

/* For Webkit browsers like Chrome, Safari */
.list-product::-webkit-scrollbar {
  width: 8px;
}

.list-product::-webkit-scrollbar-track {
  background: transparent;
}

.list-product::-webkit-scrollbar-thumb {
  background-color: rgba(155, 155, 155, 0.7);
  border-radius: 20px;
  border: transparent;
}


.one-product {
  position: relative;
  padding-bottom: 100%;
}

.one-product>* {
  position: absolute;
  width: 100%;
}

.one-row-selected {
  display: flex;
  font-weight: bold;
  padding-top: 16px;
  padding-bottom: 16px;
}

.one-row-selected .stt {
  width: 30px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.one-row-selected .quantity {
  width: 20px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.one-row-selected .price {
  width: 80px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.one-row-selected .box-name {
  flex: 1;
}

.one-row-selected .box-name .description {
  font-weight: normal;
  font-size: 12px;
}

.active {
  background: #0a53c1;
  color: white;
  padding-bottom: 16px;
}

.action {
  display: flex;
  justify-content: space-around;
}

.list-order {
  height: calc(100% - 110px);
  overflow: auto;
}

.action-list {
  display: flex;
  height: 70px;
}

.price-info {
  display: flex;
  justify-content: space-between;
  padding: 0 16px;
  height: 40px;
  align-items: center;
  background: #efefef;
}

.action-list>button {
  flex: 1;
  height: 100% !important;
}

.choose-location {
  display: flex;
  height: 50px;
  align-items: center;
  padding-left: 8px;
  font-size: 18px;
  font-weight: bold;
  background: #f4f6f8;
}

.price-info .total-amount {
  font-weight: bold;
  font-size: 20px;
}

.selected-table {
  display: flex;
  width: 100%;
  height: 100%;
}

.selected-table>* {
  flex: 1;
}

.table-number {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
