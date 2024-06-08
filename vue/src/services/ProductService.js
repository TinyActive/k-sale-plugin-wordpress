import BaseService from './BaseService';
class ProductService extends BaseService {
    constructor() {
        super('product');
    }
}
const productService = new ProductService;
export default productService;