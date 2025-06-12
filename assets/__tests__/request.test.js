/**
 * Build fetch options with method, auth header, JSON content-type, and body (unless GET).
 * @param {string} method  HTTP method (e.g. 'GET', 'POST')
 * @param {string} token   Bearer token
 * @param {string} body    JSON string
 * @returns {object}       Options for fetch()
 */
function buildOptions(method, token, body) {
  const options = {
    method,
    headers: {},
  };

  // Add Authorization header if token provided
  if (token) {
    options.headers.Authorization = `Bearer ${token}`;
  }

  // For non-GET methods, set JSON content-type and include the body
  if (method !== 'GET' && body) {
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
  } catch {
    return false;
  }
}

/**
 * Parse a fetch Response: pretty-print JSON or return text.
 * @param {Response} res
 * @returns {Promise<string>}
 */
async function parseResponse(res) {
  // Look up content-type header (case-insensitive)
  const contentType = (res.headers.get('Content-Type') || res.headers.get('content-type') || '').toLowerCase();

  if (contentType.includes('application/json')) {
    const data = await res.json();
    return JSON.stringify(data, null, 2);
  } else {
    return res.text();
  }
}

module.exports = { buildOptions, isValidJson, parseResponse };
