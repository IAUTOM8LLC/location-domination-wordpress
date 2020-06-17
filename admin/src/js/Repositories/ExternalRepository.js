import axios from 'axios';

import qs from 'qs';

const BASE_URL = process.env.NODE_ENV === 'production' ? 'https://dashboard.locationdomination.net' : 'https://fathomless-storm-zv5pdajur2xj.vapor-farm-b1.com';

export class ExternalRepository {
    static getBaseUrl() {
        return BASE_URL;
    }

    static getStates( data ) {
        return axios.get( `${BASE_URL}/api/states`, data );
    }

    static getCountries( data ) {
        return axios.get( `${BASE_URL}/api/countries`, data );
    }

    static getRegions( data ) {
        return axios.get( `${BASE_URL}/api/regions`, data );
    }

    static getCounties( data ) {
        return axios.get( `${BASE_URL}/api/counties`, data );
    }

    static getCities( data ) {
        return axios.get( `${BASE_URL}/api/cities`, data );
    }

    static getWorldCities( data ) {
        return axios.get( `${BASE_URL}/api/worldcities`, data );
    }

    static setPostRequest( data ) {
        const options = {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            data: qs.stringify( data ),
            url: `${BASE_URL}/api/post-requests`
        };

        return axios( options );
    }

    static startLocalQueuePostRequest( data, url, templateId, nonce ) {
        const options = {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            data: qs.stringify( data ),
            url: `${url}?action=location_domination_start_queue&_nonce=${nonce}&templateId=${templateId}`
        };

        return axios( options );
    }

    static startIndexing( data, url, templateId, nonce ) {
        const options = {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            data: qs.stringify( data ),
            url: `${url}?action=location_domination_start_queue&_nonce=${nonce}&templateId=${templateId}`
        };

        return axios( options );
    }

    static cancelLocalQueuePostRequest( url, templateId, nonce ) {
        const options = {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            data: qs.stringify( {} ),
            url: `${url}?action=location_domination_cancel_queue&_nonce=${nonce}&templateId=${templateId}`
        };

        return axios( options );
    }

    static continueLocalQueuePostRequest( url, templateId, nonce ) {
        const options = {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            data: qs.stringify( {} ),
            url: `${url}?action=location_domination_continue_queue&_nonce=${nonce}&templateId=${templateId}`
        };

        return axios( options );
    }

    static previewPostRequest( data, url, templateId, nonce ) {
        const options = {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            data: qs.stringify( data ),
            url: `${url}?action=location_domination_preview_request&_nonce=${nonce}&templateId=${templateId}`
        };

        return axios( options );
    }

    static finishLocalQueuePostRequest( url, templateId ) {
        const options = {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            data: qs.stringify([]),
            url: `${url}?action=location_domination_end_queue&templateId=${templateId}`
        };

        return axios( options );
    }

    static pollPostRequest(url, templateId) {
        const options = {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            data: qs.stringify({
                template: templateId,
            }),
            url: `${url}?action=location_domination_process_queue`
        };

        return axios( options );
    }

    static pollIndex(url, templateId) {
        const options = {
            method: 'POST',
            headers: { 'content-type': 'application/x-www-form-urlencoded' },
            data: qs.stringify({
                template: templateId,
            }),
            url: `${url}?action=location_domination_process_index`
        };

        return axios( options );
    }

}