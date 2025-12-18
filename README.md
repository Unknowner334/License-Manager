# License Manager

A SaaS-ready project that allows you to grant access to your private apps via licenses/keys. Each license can have an **expiry date** and a **device limit**.  
While designed primarily for Android apps, it can also be used on **iOS, macOS, or Windows**, as long as you can obtain a device identifier to enforce the device limit reliably.

---

## 🚀 Project Status
**Finished:** December 6th, 2025

---

## Notes
**Important:** DOWNLOAD THE PROJECT FROM RELEASES SECTION THE BRANCH **_MAIN_** IS MEANT TO HANDLE THE LATEST COMMITS TO IT AND MOST OF THE TIME
THEY ARE NOT PRODUCTION READY

---

## 📄 License

Copyright © 2025 Karam (karamdev1)

All rights reserved.

This software and its source code are the property of Karam.

You may use this software publicly and display it with your own name as the app name of the website or project built using this software. 

You may NOT:
- Copy, modify, distribute, or republish this software itself.
- Claim authorship of the original source code without explicit written permission from the author.
- Remove or alter any copyright notices, credits, or links in the software without prior written consent from the author.

Any attempt to bypass, hide, or modify such notices or links is considered a violation of this license.

---

## ⚙️ Requirements

- PHP 8.5+
- Composer

---

## 🛠️ Setup

1. Copy `.env.example` to `.env`  
2. Configure your database credentials in `.env`  
3. Run the following commands:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --seed