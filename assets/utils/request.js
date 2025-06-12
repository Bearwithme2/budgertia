function buildOptions(method, token, body) {
    const options = { method, headers: {} };
    if (token) {
        options.headers.Authorization = `Bearer ${token}`;
    }
    if (body && method !== 'GET') {
        options.headers['Content-Type'] = 'application/json';
        options.body = body;
    }
    return options;
}

function isValidJson(str) {
    try {
        JSON.parse(str);
        return true;
    } catch (e) {
        return false;
    }
}

async function parseResponse(res) {
    const ct = res.headers.get('Content-Type');
    if (ct && ct.includes('application/json')) {
        try {
            return JSON.stringify(await res.json(), null, 2);
        } catch (e) {
            return res.statusText;
        }
    }
    return await res.text();
}

module.exports = { buildOptions, isValidJson, parseResponse };
