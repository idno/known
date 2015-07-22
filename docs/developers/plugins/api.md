# Making and accepting API calls

## Making API calls

Every page in Known doubles as an API endpoint.

API calls to Known sites consist of POST requests to Known pages, with the following HTTP headers set:

* X-KNOWN-USERNAME: the user's username
* X-KNOWN-SIGNATURE: an HMAC signature, computed with sha256, using the user's API key

The user's API key can be found in Settings, under Tools and Apps.

Additionally, POST requests usually include:

* A POST payload consisting of JSON-encoded variables to be sent to the endpoint.
* A GET URL variable, `_t`, set to the template type to be used in the response (usually `json`).

### Creating an HMAC signature

The signature used in X-KNOWN-SIGNATURE uses the following components:

* The request URI
* The user's API key

This is then base64-encoded.

In PHP, these are calculated into an HMAC signature as follows:

    base64_encode(hash_hmac('sha256', $api_endpoint, $api_key, true));

### Example

For example, to submit a new status update, you would send a POST request to `/status/edit/?_t=json` containing the
following HTTP headers:

* X-KNOWN-USERNAME: the user's username
* X-KNOWN-SIGNATURE: a base64-encoded HMAC signature, using `/status/edit/?_t=json` and the user's API
 key, concatenated together

And the following data in the POST payload:

* `{"body": "The body text of your status update."}`

Known will then redirect to a JSON encapsulation of the newly created object on success.

## Accepting API calls

* See: [Accepting input from HTML forms, links or API calls](forms.md#accepting-input-from-html-forms-links-or-api-calls).
