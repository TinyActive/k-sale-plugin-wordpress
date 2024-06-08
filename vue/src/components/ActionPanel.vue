<template>
    <div class="action-panel">
        <div class="box">
            <v-btn class="button-100" prepend-icon="mdi-plus" size="x-large">
                <template v-slot:prepend>
                    <v-icon color="success"></v-icon>
                </template>
                Tạo đơn mới</v-btn>
        </div>
        <!-- <div class="box">
            <v-btn class="button-100" prepend-icon="mdi-plus" size="x-large">
                <template v-slot:prepend>
                    <v-icon color="success"></v-icon>
                </template>
                Tất cả đơn</v-btn>
        </div> -->
    </div>
</template>

<script>
import { ref, reactive, computed, watchEffect } from 'vue';

export default {
    name: 'ActionPanel',
    setup() {
        // Your setup code goes here
        const data = reactive({
            // Your data properties go here
        });

        const methods = {
            // Your methods go here
        };

        // Code to run when the component is mounted
        watchEffect(() => {
            // Code to run when data properties change
        });

        return {
            data,
            methods,
        };
    },
};
</script>

<style scoped>
.box{
    width: 100%;
    background: #01204E;
    padding: 10px;
}
</style>