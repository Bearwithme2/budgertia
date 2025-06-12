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

module.exports = { buildOptions, isValidJson };
