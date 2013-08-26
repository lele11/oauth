For consumer(Authorization Code)
================================
##### 1. Client register
- Url: \<host\>/oauth/client/register

##### 2. Get authorization code(if grant type is authorization_code)
- Url: \<host\>/oauth/authorize/index
- method: GET
- parameters
<table>
  <tr>
    <td>client_id</td>
    <td>Client ID assigned after register the client</td>
  </tr>
  <tr>
    <td>response_type</td>
    <td>Response type, the value is "code" fixed</td>
  </tr>
  <tr>
    <td>redirect_uri</td>
    <td>Callback uri after authorization</td>
  </tr>
  <tr>
    <td>state</td>
    <td>State of client, it will be sent back after authorization</td>
  </tr>
  <tr>
    <td>scope(optional)</td>
    <td>Uncompleted(please use "base" for testing)</td>
  </tr>
</table>

##### 3. Get access token
- Url: \<host\>/oauth/grant/index
- method: POST
- parameters:
<table>
  <tr>
    <td>grant_type</td>
    <td>Grant type, the value is "authorization_code" fixed</td>
  </tr>
  <tr>
    <td>client_id</td>
    <td>Client ID assigned after register the client</td>
  </tr>
  <tr>
    <td>client_secret</td>
    <td>Client secret assigned after register the client</td>
  </tr>
  <tr>
    <td>code</td>
    <td>Authorization code from previous step</td>
  </tr>
  <tr>
    <td>redirect_uri</td>
    <td>Same as previous step.</td>
  </tr>
</table>
- return: json string, example {"token_type":"bearer","expires_in":"3600","access_token":"f6d***7e"}

##### Error
- If error occurs, a json string contained "error" and "error_description" will be returned.
