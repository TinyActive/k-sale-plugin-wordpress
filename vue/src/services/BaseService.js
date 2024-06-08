import axios from 'axios';

class BaseService {
    constructor(controller) {
        this.api = axios.create({
            baseURL: this.getBaseUrl(controller), // Replace with your API base URL
            headers: {
                'Content-Type': 'application/json',
            },
        });
    }

    getBaseUrl(controller) {
        return window.__app_config.base_url + 'wp-json/k-sale/v1/' + controller + 's';
    }

    async list(params, url = "/list?per_page=1000") {
        try {
            const response = await this.api.get(url, { params });
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }


    async get(params, url = "") {
        try {
            const response = await this.api.get(url, { params });
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            if(error.response.status === 404) {
                return null;
            }
            throw error;
        }
    }

    async post(data, url = "") {
        try {
            const response = await this.api.post(url, data);
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }
    
    async postForm(form, url = "") {
        try {
            const response = await this.api.postForm(url, form);
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    async put(data, url = "") {
        try {
            const response = await this.api.put(url, data);
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }

    async delete(url) {
        try {
            const response = await this.api.delete(url);
            return response.data;
        } catch (error) {
            console.error('Error:', error);
            throw error;
        }
    }


}

export default BaseService;