import BaseService from './BaseService';
class ImageService extends BaseService {
    constructor() {
        super('image');
    }

    async upload(file, metadata) {
        try {
            const formData = new FormData();
            formData.append('file', file);
            if (metadata) {
                for (const key in metadata) {
                    formData.append(key, metadata[key]);
                }
            }
            const url = '/upload';
            const response = await this.api.postForm(url, formData);
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }
}
const imageService = new ImageService;
export default imageService;