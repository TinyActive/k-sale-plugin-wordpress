import BaseService from './BaseService';
class LocationService extends BaseService {
    constructor() {
        super('location');
    }

    async saveLocation({ LocationID, LocationName, NumberOfSeat }) {
        try {
            const url = '/save-location';
            const response = await this.api.post(url, { LocationID, LocationName, NumberOfSeat });
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }
}
const locationService = new LocationService;
export default locationService;