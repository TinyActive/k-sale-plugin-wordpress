import BaseService from './BaseService';
class TableService extends BaseService {
    constructor() {
        super('table');
    }

    async getTableByLocation({ LocationID }) {
        try {
            const url = '/get-table-by-location';
            const response = await this.api.get(url, {params: { LocationID: Number(LocationID) }});
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }
}
const tableService = new TableService;
export default tableService;