import {Inertia} from '@inertiajs/inertia'

class UrlQueryService {
    constructor() {
        this.params = new URLSearchParams(window.location.search);

        // update params on page change
        Inertia.on('before', (event) => {
            this.params = new URLSearchParams(event.detail.visit.url.search);
        });
    }

    fresh() {
        this.params = new URLSearchParams(window.location.search);
    }

    set(param, value) {
        this.params.set(param, value);
    }

    remove(param) {
        this.params.delete(param);
    }

    get(param, defaultValue) {
        return this.params.get(param) || defaultValue;
    }

    has(param) {
        return this.params.has(param);
    }

    all() {
        this.clean();

        let object = {};
        this.params.forEach((v, k) => object[k] = v);

        return object;
    }

    encodedString() {
        this.clean();

        return this.params.toString();
    }

    clean() {
        let emptyKeys = [];

        this.params.forEach((value, key) => {
            if (value === '') {
                emptyKeys.push(key);
            }
        });

        emptyKeys.forEach(key => this.params.delete(key));
    }
}

export default new UrlQueryService;
