<template>
    <Layout maxWidth="1322">

        <template v-slot:title>{{ title }}</template>
        <template v-slot:content>
            <div class="content-page">

                <div class="left-content-page">
                    <form>
                        <div class="box-content">
                            <div class="box-content-form">
                                <div class="label">Tên mặt hàng</div>
                                <v-text-field :error-messages="productName.errorMessage.value" autofocus
                                    v-model="productName.value.value" variant="outlined"></v-text-field>
                                <div class="label">Giá thành</div>
                                <v-text-field @focus="$event.target.select()" v-model="price" variant="outlined"
                                    v-currency></v-text-field>
                                <div class="label">Đơn vị tính</div>
                                <v-combobox v-model="unit" :items="['Bát', 'Đĩa', 'Cốc']"
                                    variant="outlined"></v-combobox>
                                <div class="label">Ghi chú</div>
                                <v-textarea v-model="note" variant="outlined"></v-textarea>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="right-content-page">
                    <div class="box-content">
                        <div class="label">Hình đại diện</div>
                        <v-file-input class="d-none" ref="fileInput" accept="image/*" placeholder="Upload an image"
                            prepend-icon="mdi-camera" @change="onFileSelected"></v-file-input>
                        <img class="image-uploaded" v-if="imageUrl" :src="imageUrl" alt="Uploaded image" />
                        <img class="image-uploaded" v-if="!imageUrl" src="@/assets/image.png" alt="Uploaded image" />
                        <v-btn @click="onButtonClick" width="100%" variant="outlined">Tải ảnh lên</v-btn>
                    </div>
                    <v-divider></v-divider>
                    <div class="box-content">
                        <v-btn width="100%" color="info" @click="saveProduct" variant="flat">Lưu hàng hóa</v-btn>
                    </div>

                </div>
            </div>
        </template>
        <template v-slot:footer>

        </template>
    </Layout>
</template>

<script>
import { ref, reactive, computed, watch, inject } from 'vue';
import Layout from '@/layout/Layout.vue';
import { useField, useForm } from 'vee-validate';
import productService from '@/services/productService';
import UtilsService from '@/services/utilsService';
import imageService from '@/services/imageService';

export default {
    name: 'ProductPage',
    components: {
        Layout
    },
    setup() {
        const snackbar = inject('snackbar')
        const overlay = inject('overlay')
        const price = ref(0);
        const unit = ref("Cốc");
        const note = ref("");
        const imageUrl = ref("");
        const fileInput = ref(null);
        const productID = ref(null);

        const utilsService = new UtilsService();

        // create computed for title, if productID is null then title is 'Thêm mặt hàng' else 'Sửa mặt hàng'
        const title = computed(() => {
            return productID.value ? productName.value.value : 'Thêm mặt hàng';
        });

        const { handleSubmit, handleReset } = useForm({
            validationSchema: {
                productName(value) {
                    if (value && value.length > 0) {
                        return true
                    }
                    return 'Tên mặt hàng không được để trống!'
                },
            },
        });

        // Tên sản phẩm
        const productName = useField("productName");

        // Watch the price ref
        watch(price, (newVal) => {
            if (newVal === '') {
                price.value = 0;
            }
        });

        // Thực hiện upload ảnh
        const onFileSelected = (event) => {
            let selectedFile = event.target.files[0];
            imageUrl.value = URL.createObjectURL(selectedFile);

            imageService.upload(selectedFile).then((data) => {
                if (data && data.url) {
                    imageUrl.value = data.url;
                }
            });
        }

        const goToProductPage = () => {
            // go to product page
            window.location.href = '?page=product-page';
        }

        // Lưu sản phẩm
        const saveProduct = handleSubmit(async ({ productName }) => {
            // Xử lý lưu dữ liệu
            const product = {
                ProductName: productName,
                Price: utilsService.toNumber(price.value),
                Unit: unit.value,
                Note: note.value,
                ImageUrl: imageUrl.value,
            }

            let data = null;

            if (productID.value) {
                product.ProductID = productID.value;
                data = await productService.put(product);
            } else {
                data = await productService.post(product);
            }

            if (data && data.ProductID) {
                snackbar('Lưu mặt hàng thành công!');
                // go to product page
                goToProductPage();
            } else {
                snackbar('Đã có lỗi xảy ra!', 'error');
            }

        });

        // Click button to open file input
        const onButtonClick = () => {
            fileInput.value.click();
        }

        const init = async () => {
            overlay.value = true;
            const mode = utilsService.getMode();
            if (mode === 'edit') {
                // Call API to get product detail
                try {
                    const urlParams = new URLSearchParams(window.location.search);
                    const ProductID = urlParams.get('id');
                    const data = await productService.get({ id: ProductID })
                    if (data) {
                        productName.value.value = data.ProductName;
                        price.value = utilsService.toCurrency2(data.Price);
                        unit.value = data.Unit;
                        note.value = data.Note;
                        productID.value = data.ProductID;
                        imageUrl.value = data.ImageUrl;
                    } else {
                        snackbar('Không tìm thấy sản phẩm ', 'error');
                        // go to product page
                        goToProductPage();
                    }
                } catch (error) {
                    snackbar('Đã có lỗi xảy ra!', 'error');
                }
            }
            overlay.value = false;
        }

        init();

        return {
            unit,
            price,
            productName,
            saveProduct,
            note,
            onFileSelected,
            imageUrl,
            fileInput,
            onButtonClick,
            goToProductPage,
            title
        }
    },
};
</script>

<style scoped>
.box-content {
    background: white;
    padding: 12px;
}

.content-page {
    display: flex;
}

.image-uploaded {
    width: 100%;
    margin-top: 12px;
    min-height: 100px;
    background-color: gray;
}

.left-content-page {
    width: 890px;
}

.right-content-page {
    padding-left: 12px;
    flex: 1;
}

.label {
    font-size: 16px;
    margin-bottom: 6px;
}

.box-content {
    margin-bottom: 8px;
}
</style>