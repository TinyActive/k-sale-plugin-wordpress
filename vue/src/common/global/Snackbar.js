import { createApp, h } from 'vue';
import Snackbar from './Snackbar.vue';

const snackbarApp = createApp({
    render: () => h(Snackbar),
});
const wrapper = document.createElement('div');
const snackbarInstance = snackbarApp.mount(wrapper);
document.body.appendChild(wrapper);
export default snackbarInstance;