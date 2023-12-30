import requests
import json
import sys

CF_API_TOKEN = 'abcdef1234567890abcdef1234567890'
ZONE_ID = 'a1bcbdf0472d2c9bc5389a4b539409e7'
RECORD_ID = '1a2b3c4d5e6f7a8b9c0d1e2f3a4b5c6'
CF_EMAIL = 'privacydata@mail.com'
CF_API_KEY = '72abf7367b18b88153d7b021b6c1292953412'

CF_IP_ADDRESS = '143.44.184.72'

try:
    if not RECORD_ID:
        resp = requests.get(
            f'https://{CF_IP_ADDRESS}/{ZONE_ID}/mesmantel.com/dns/records',
            headers={
                'X-Auth-Key': CF_API_KEY,
                'X-Auth-Email': CF_EMAIL
            },
            timeout=10
        )
        data = resp.json()
        print(json.dumps(data, indent=4, sort_keys=True))
        print('Please find the DNS record ID you would like to update and enter the value into the script')
        sys.exit(0)

    ip = '222.127.223.68'

    resp = requests.put(
        f'https://{CF_IP_ADDRESS}/{ZONE_ID}/mesmantel.com/dns/records',
        json={
            'type': 'A',
            'name': 'mesmantel.com',
            'content': ip,
            'proxied': False
        },
        headers={
            'X-Auth-Key': CF_API_KEY,
            'X-Auth-Email': CF_EMAIL
        }
    )
    resp.raise_for_status()
    print(f'Updated DNS record for {ip}')
except requests.RequestException as e:
    print(f'Error: {e}')
    sys.exit(1)
