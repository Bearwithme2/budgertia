const { buildOptions, isValidJson } = require('../utils/request');

test('buildOptions sets headers and body', () => {
    const opts = buildOptions('POST', 'abc', '{"a":1}');
    expect(opts.method).toBe('POST');
    expect(opts.headers.Authorization).toBe('Bearer abc');
    expect(opts.headers['Content-Type']).toBe('application/json');
    expect(opts.body).toBe('{"a":1}');
});

test('buildOptions omits body for GET', () => {
    const opts = buildOptions('GET', '', '');
    expect(opts.method).toBe('GET');
    expect(opts.body).toBeUndefined();
});

test('isValidJson returns false for invalid JSON', () => {
    expect(isValidJson('{foo:}')).toBe(false);
});

test('isValidJson returns true for valid JSON', () => {
    expect(isValidJson('{"a":1}')).toBe(true);
});
