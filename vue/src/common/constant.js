// Enum file example

const EditMode = {
    ADD: 1,
    EDIT: 2,
    DELETE: 3,
    NONE: 0,
};

const OrderStatus = {
    DRAFT: 1, // nháp
    CONFIRM: 2, // xác nhận
    UNPAID: 3, // chưa thanh toán
    CANCEL: 4, // hủy
    PAID: 5, // đã thanh toán
};

export { EditMode, OrderStatus };
