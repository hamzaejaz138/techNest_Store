<?php
// Database settings. Change these to match your server (XAMPP defaults shown).
const DB_HOST = 'localhost';
const DB_NAME = 'technest';
const DB_USER = 'root';
const DB_PASS = '';
const ADMIN_EMAIL = 'admin@example.com';      // used by setup.php for the first admin
const SITE_URL    = 'http://localhost/store'; // no trailing slash; used in reset links
const DEV_MODE    = true;                     // true: show reset link on screen. Set false on a live server.

// Stripe (test mode). Leave the key empty to use "Pay on delivery" instead.
// Get keys at dashboard.stripe.com > Developers > API keys. NEVER share or publish your secret key.
const STRIPE_SECRET_KEY     = '';   // sk_test_...
const STRIPE_WEBHOOK_SECRET = '';   // whsec_... (optional, for webhook.php)
const CURRENCY              = 'usd';
