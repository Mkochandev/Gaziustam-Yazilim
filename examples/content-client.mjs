const base = process.env.GAZIUSTAM_API_URL;
const token = process.env.GAZIUSTAM_API_TOKEN;
if (!base || !token) throw new Error('GAZIUSTAM_API_URL and GAZIUSTAM_API_TOKEN are required.');

async function request(path, method, payload) {
    const response = await fetch(new URL(path, base), {
        method,
        headers: {
            Authorization: 'Bearer ' + token,
            Accept: 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload),
    });
    const result = await response.json();
    if (!response.ok) throw new Error('HTTP ' + response.status + ': ' + JSON.stringify(result));
    return result;
}

const post = await request('/api/v1/posts', 'POST', {
    type: 'article',
    source_locale: 'de',
    provider: 'example-client',
    external_id: 'sample-001',
    category: 'software',
});
const translation = await request('/api/v1/posts/' + post.id + '/translations/de', 'PUT', {
    title: 'Beispielentwurf',
    slug: 'beispielentwurf',
    excerpt: 'Ein Beispiel für die Inhaltsintegration.',
    body: '<p>Dieser Inhalt ist ein redaktioneller Entwurf.</p>',
});
console.log(JSON.stringify({ id: post.id, locale: translation.locale, status: translation.status }));

