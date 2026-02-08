# Portfolio Website Template (PHP + JSON Admin)

Lightweight portfolio website template with a simple PHP admin panel.
Designed for creatives: makeup artists, photographers, stylists.

No database required.  
All content is stored in JSON files.

---

## Project Structure

/
├── admin/                     Admin panel (protected)
│   ├── login.php
│   ├── logout.php
│   ├── auth.php
│   ├── config.php
│   ├── index.php              Gallery admin
│   ├── upload.php
│   ├── delete.php
│   ├── edit_home.php
│   ├── edit_services.php
│   ├── backfill.php           Generate image variants for existing images
│   └── functions.php
│
├── content/                   Editable text content
│   ├── home.json
│   ├── services.json
│   └── config.json            Admin login + optional GA4
│
├── img/
│   ├── portfolio/             Portfolio images (original uploads)
│   ├── cache/                 Auto-generated thumbnails
│   └── site/                  Background images (hero, services, inquiry)
│
├── css/
│   └── style.css
│
├── js/
│   └── script.js
│
├── send.php                   Inquiry form handler (email)
├── thanks.html                Thank-you page
├── thumb.php                  Secure image resize & cache
├── index.php                  Home page
├── portfolio.php
├── services.php
├── inquiry.html
└── README.md

---

## Admin Panel

Admin panel URL:

/admin/login.php

Login credentials are stored in:

/content/config.json

Example:

{
  "admin_user": "admin",
  "admin_pass": "ChangeMe123!",
  "ga4_id": "G-XXXXXXXXXX"
}

Change login and password before production.

---

## Content Editing

All public text content is stored in JSON files.

Home page text:
/content/home.json

Services page text:
/content/services.json

Content can be edited either manually or via admin panel.

---

## Portfolio Images

Images are uploaded via admin panel.

Upload folder:
/img/portfolio/

Generated image sizes:
- 640px
- 1024px
- 1600px

Cached images:
/img/cache/

Image processing is handled by thumb.php:
- EXIF auto-rotation
- Path security check
- Long-term browser caching

---

## Background Images

Background images for pages are stored in:

/img/site/

These images are used via CSS variables:
- Home hero background
- Services background
- Inquiry background

---

## Inquiry Form

Form handler:
/send.php

After successful submission user is redirected to:
/thanks.html

Email settings are configured inside send.php.

---

## backfill.php

File:
/admin/backfill.php

Purpose:
Generate image variants (640 / 1024 / 1600) for images that already exist in
/img/portfolio/

Used once if images were uploaded manually (FTP).

---

## functions.php

File:
/admin/functions.php

Contains helper functions:
- Project root detection
- Image listing
- Public URL generation
- Variant filtering

Required for admin panel operation.

---

## Local Development

You can run the project locally using PHP built-in server:

php -S localhost:8000

Then open in browser:

http://localhost:8000

Admin panel:

http://localhost:8000/admin/login.php

---

## Security Notes

- Admin panel protected by PHP sessions
- No database used
- thumb.php strictly limits accessible paths
- Debug files must be removed before production
- Cache directory contains only generated images

---

## Production Checklist

- Change admin login and password
- Remove debug files
- Upload images via admin panel
- Test gallery on mobile
- Optional: add Google Analytics ID

---

## License

This template is provided as-is.
Can be customized and sold as part of a website project.
