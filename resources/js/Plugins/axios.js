import axios from 'axios'
import get from 'lodash/get'

axios.interceptors.response.use(response => response, async err => {
    const status = get(err, 'response.status');

    // reload if server and client versions don't match
    if (status === 409 && err.response.data.error === 'client version has expired') {
        window.location.reload();
    }
    else if (status === 419) {
        await axios.get('/csrf-token');

        if (get(err, 'response.status') !== 200) {
            window.location.href = route('login');
        }

        return axios(err.response.config);
    }
    else if (status === 401) {
        window.location.href = route('login');
    }

    return Promise.reject(err);
});
