<template>
    <v-snackbar v-model="isShow" :color="color" :timeout="3000" :location="location">
        {{ message }}
    </v-snackbar>
</template>

<script>
export default {
    data: () => ({
        isShow: false,
        message: '',
        color: '',
        location: '',
    }),
    methods: {
        show(message, color = 'success', location="right") {
            this.message = message;
            this.color = color;
            this.isShow = true;
            this.location = location;
        },
    },
};
</script>