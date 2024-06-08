import BaseService from './BaseService';
class InvoiceDetailService extends BaseService {
    constructor() {
        super('invoice-detail');
    }
}
const invoiceDetailService = new InvoiceDetailService;
export default invoiceDetailService;