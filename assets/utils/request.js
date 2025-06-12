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

module.exports = { buildOptions };
