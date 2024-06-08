import BaseService from './BaseService';
class OrderService extends BaseService {
    constructor() {
        super('order');
    }

    async payOrder(Order) {
        return await this.saveOrder({ Order });
    }

    async cancelOrder(Order) {
        return await this.saveOrder({ Order });
    }

    async saveOrder({ Order, OrderDetails }) {
        try {
            const url = '/save-order';
            const response = await this.api.post(url, { Order, OrderDetails });
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    async getOrder({ OrderID }) {
        try {
            const url = '/get-order';
            const response = await this.api.get(url, { params: { OrderID } });
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    async unpaid(params, url = "/unpaid?per_page=1000") {
        try {
            const response = await this.api.get(url, { params });
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }
}
const orderService = new OrderService;
export default orderService;