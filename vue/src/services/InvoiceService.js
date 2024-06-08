import BaseService from './BaseService';
class InvoiceService extends BaseService {
    constructor() {
        super('invoice');
    }


    async getInvoice({ InvoiceID }) {
        try {
            const url = '/get-invoice';
            const response = await this.api.get(url, { params: { InvoiceID } });
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }
}
const invoiceService = new InvoiceService;
export default invoiceService;