/**
 * Build fetch options with method, auth header, JSON content-type, and body (unless GET).
 * @param {string} method  HTTP method (e.g. 'GET', 'POST')
 * @param {string} token   Bearer token
 * @param {string} body    JSON string
 * @returns {object}       Options for fetch()
 */
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

/**
 * Check whether a string is valid JSON.
 * @param {string} str
 * @returns {boolean}
 */
function isValidJson(str) {
    try {
        JSON.parse(str);
        return true;
    } catch (e) {
        return false;
    }
}

/**
 * Parse a fetch Response: pretty-print JSON or return text/statusText.
 * @param {Response} res
 * @returns {Promise<string>}
 */
async function parseResponse(res) {
    const ct = res.headers.get('Content-Type') || '';
    if (ct.includes('application/json')) {
        try {
            const data = await res.json();
            return JSON.stringify(data, null, 2);
        } catch (e) {
            return res.statusText;
        }
    }
    return res.text();
}

module.exports = { buildOptions, isValidJson, parseResponse };
