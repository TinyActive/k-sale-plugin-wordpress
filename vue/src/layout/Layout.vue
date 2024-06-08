<template>
    <div class="layout-page" :style="{maxWidth: maxWidth + 'px'}">
        <div class="header">
            <div class="title">
                <slot name="title"></slot>
            </div>
            <div class="action">
                <slot name="action"></slot>
            </div>
        </div>
        <v-divider></v-divider>
        <div class="layout-content">
            <slot name="content"></slot>
        </div>
        <div class="layout-footter">
            <slot name="footer"></slot>
        </div>
    </div>
</template>

<script>
import { ref, reactive, computed, watch } from 'vue';

export default {
    name: 'Layout',
    props: {
        maxWidth: {
            type: String,
            default: '900',
        },
    },
    components: {
    },
    setup() {
        const tab = ref('one');

        return {
            tab,
        };
    },
};
</script>

<style scoped>
/* Your CSS styles here */
.layout-page {
    height: 100%;
    margin: 0 auto;
}

.title {
    font-size: 24px;
    font-weight: bold;
}

.header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 20px 0;
}

.layout-content {
    padding: 20px 0;
}
</style>