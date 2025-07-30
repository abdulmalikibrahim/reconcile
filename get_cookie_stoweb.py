from flask import Flask, jsonify
import requests

app = Flask(__name__)

@app.route('/get-cookie')
def get_cookie():
    session = requests.Session()

    # Step 1: GET dulu halaman login buat dapetin PHPSESSID
    headers_get = {
        "User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36",
        "Accept": "text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8",
        "Accept-Encoding": "gzip, deflate",
        "Accept-Language": "id-ID,id;q=0.9,en-US;q=0.8",
        "Connection": "keep-alive",
        "Upgrade-Insecure-Requests": "1"
    }

    session.get("http://10.59.114.111:8080/stoweb/", headers=headers_get)

    # Step 2: POST login ke /index.php/login/verify
    headers_post = headers_get.copy()
    headers_post["Referer"] = "http://10.59.114.111:8080/stoweb/"
    headers_post["Content-Type"] = "application/x-www-form-urlencoded"

    payload = {
        "username": "awan",
        "password": "Astra123"
    }

    response = session.post(
        "http://10.59.114.111:8080/stoweb/index.php/login/verify",
        data=payload,
        headers=headers_post
    )

    # Step 3: Ambil semua cookies hasil login
    cookies = session.cookies.get_dict()
    return jsonify({"cookies": cookies})

if __name__ == '__main__':
    app.run(host="0.0.0.0", port=5000)
