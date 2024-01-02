import requests
from flask import Flask, request, jsonify

app = Flask(__name__)

@app.route('/save-python-endpoint', methods=['POST'])
def process_request():
    data = request.json

    api_token = data.get('api_token')
    zone_id = data.get('zone_id')
    email = data.get('email')
    type = data.get('type')
    name = data.get('name')
    content = data.get('content')

    cloudflare_api_url = f"https://api.cloudflare.com/client/v4/zones/{zone_id}/dns_records"

    headers = {
        'Content-Type': 'application/json',
        'X-Auth-Email': email,
        'X-Auth-Key': api_token,
        'Authorization': f'Bearer {api_token}',
    }

    dns_data = {
        'type': type,
        'name': name,
        'content': content,
    }

    try:
        response = requests.post(cloudflare_api_url, json=dns_data, headers=headers)
        response.raise_for_status()
        return jsonify({'success': True, 'output': response.json()}), response.status_code
    except requests.exceptions.RequestException as e:
        return jsonify({'error': str(e)}), 500

if __name__ == '__main__':
    app.run(port=5000)
