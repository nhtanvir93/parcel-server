require('./bootstrap');

import Vue from 'vue';
import ParcelRequest from './vue/parcelRequest';

const app = new Vue({
    el : '#app',
    components : {
        ParcelRequest
    }
});