<template>
    <div class="product">
        <div class="product-image">
            <img :src="product.ImageUrl" v-if="product.ImageUrl" :alt="product.ProductName" />
            <img src="@/assets/image.png" v-if="!product.ImageUrl" />

        </div>
        <div class="product-name">{{ product.ProductName }}</div>
        <div class="product-price">{{ utilService.toCurrency(product.Price) }}</div>
    </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue';
import UtilService from '@/services/UtilsService';

export default {
    name: 'Product',
    props: {
        product: {
            type: Object,
            default: []
        }
    },
    setup(props, { emit }) {
        const utilService = new UtilService;
        // Your component's setup logic goes here
        return {
            utilService
        };
    },
};
</script>

<style scoped>
.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    object-position: 50% 50%;
}

.product {
    position: relative;
}

.product-name {
    position: absolute;
    left: 0;
    bottom: 0;
    height: 70px;
    background: #22233c94;
    width: 100%;
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
}

.product-price {
    display: flex;
    position: absolute;
    top: 8px;
    left: 8px;
    background: #0063da;
    padding: 8px;
    color: white;
    font-weight: bold;
}
</style>
