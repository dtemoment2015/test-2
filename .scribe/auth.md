# Authenticating requests

To authenticate requests, include an **`Authorization`** header with the value **`"Bearer {YOUR_AUTH_TOKEN}"`**.

All authenticated endpoints are marked with a `requires authentication` badge in the documentation below.

Получите токен авторизации через эндпоинты <b>POST /api/v1/register</b> или <b>POST /api/v1/login</b>. Токен будет возвращен в ответе в поле <code>data.token</code>. Используйте его в заголовке <code>Authorization: Bearer {YOUR_AUTH_TOKEN}</code>.
