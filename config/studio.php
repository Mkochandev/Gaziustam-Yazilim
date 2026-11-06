<?php
return [
    'notify'=>env('ENQUIRY_NOTIFY_TO'),
    'retention_days'=>(int)env('ENQUIRY_RETENTION_DAYS',180),
    'turnstile_key'=>env('TURNSTILE_SITE_KEY'),
    'turnstile_secret'=>env('TURNSTILE_SECRET'),
];
