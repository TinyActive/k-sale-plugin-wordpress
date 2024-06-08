<template>
    <Layout>
        <template v-slot:title>Danh sách mặc hàng</template>
        <template v-slot:action>
            <v-btn variant="flat" color="info" @click="goToAddPage">Thêm mặt hàng</v-btn>
        </template>
        <template v-slot:content>
            <div class="content-page">
                <v-data-table :hide-default-footer="false" :headers="headers" :items="items" item-value="name">
                    <template v-slot:item.ProductName="{ item }">
                        <div class="text-link" @click="handleToEditPage(item.ProductID)">{{ item.ProductName }}</div>
                    </template>
                    <template v-slot:item.ImageUrl="{ item }">
                        <div class="text-link" >
                            <img class="image-product" :src="item.ImageUrl" v-if="item.ImageUrl">
                        </div>
                    </template>
                    <template v-slot:item.Price="{ item }">
                        <div @click="">{{ utilsService.toCurrency(item.Price) }}</div>
                    </template>
                    <template v-slot:item.CostPrice="{ item }">
                        <div @click="">{{ utilsService.toCurrency(item.CostPrice) }}</div>
                    </template>
                    <template v-slot:item.Action="{ item }">
                        <div @click="deleteProduct(item.ProductID)">
                            <v-icon icon="mdi-delete" color="red"></v-icon>
                        </div>
                    </template>
                    <!-- <template v-slot:item.NumberOfTable="{ item }">
                    <div>{{ item.NumberOfTable }}</div>
                </template>
                <template v-slot:item.actions="{ item }">
                    <v-icon @click="isShowPopup = true">mdi-pencil</v-icon>
                    <v-icon @click="isShowPopup = true">mdi-delete</v-icon>
                </template> -->
                </v-data-table>
            </div>
            <v-dialog v-model="dialogDelete" max-width="400" persistent>
                <v-card title="Bạn có chắc chắn xóa mặt hàng này?">
                    <template v-slot:actions>
                        <v-spacer></v-spacer>

                        <v-btn @click="dialogDelete = false">
                            Đóng
                        </v-btn>

                        <v-btn @click="handleDeleteProdut" variant="flat" color="red">
                            Xóa
                        </v-btn>
                    </template>
                </v-card>
            </v-dialog>
        </template>
    </Layout>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue';
import Layout from '@/layout/Layout.vue';
import UtilsService from '@/services/utilsService';
import productService from '@/services/productService';

export default {
    name: 'ProductPage',
    components: {
        Layout,
    },
    setup() {
        const utilsService = new UtilsService();

        let headers = [
            {
                title: '',
                align: 'center',
                key: 'ImageUrl',
                width: '50px',
            },
            {
                title: 'Tên mặt hàng',
                align: 'start',
                key: 'ProductName',
            },
            {
                title: 'Giá thành',
                align: 'center',
                key: 'Price',
            },
            {
                title: 'Giá vốn',
                align: 'center',
                key: 'CostPrice',
            },
            {
                title: 'Hành động',
                align: 'center',
                key: 'Action',
            },
        ]

        let items = ref([]);
        let dialogDelete = ref(false);
        let prductIDDeleting = ref(null);

        const goToAddPage = () => {
            window.location.href = `?page=add-product`;
        }

        const handleToEditPage = (productID) => {
            window.location.href = `?page=add-product&id=${productID}`;
        }

        // Handle delete product
        const handleDeleteProdut = async (productID) => {
            // Call API to delete product
            productService.delete(`?id=${prductIDDeleting}`).then((data) => {
                dialogDelete.value = false;
                handleProductList();
            });
        }

        // Handle product list
        const handleProductList = async () => {
            // Call API to get product list
            productService.list().then((data) => {
                items.value = data.items;
            });
        }

        const deleteProduct = async (id) => {
            prductIDDeleting = id;
            dialogDelete.value = true;
        }

        const init = () => {
            handleProductList();
        }

        init();

        return {
            deleteProduct,
            utilsService,
            headers,
            items,
            goToAddPage,
            handleToEditPage,
            dialogDelete,
            handleDeleteProdut
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