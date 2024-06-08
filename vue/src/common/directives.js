export const numericOnly = {
    mounted(el) {
        el.addEventListener('input', (input) => {
            input.target.value = input.target.value.replace(/[^0-9]/g, '');
        });
    }
};

export const currency = {
    mounted(el, binding, vnode) {
        el.addEventListener('input', (input) => {
            input.target.value = input.target.value.replace(/[^0-9]/g, '').replace(/^0+|-/g, '').replace(/\D/g, "").replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            input.target.dispatchEvent(new Event('input'));
        });
    }
};