# Notifications module

Owns application email workflows.

`Services/Mailer.php` creates a fresh PHPMailer sender per message using the
shared SMTP factory. Plain-text messages are supported. No notification is sent
automatically by the file feature.

Place future notification controllers in `Controllers`, validation in `Requests`,
notification history access in `Repositories`, and escaped templates in `Views`.
Supabase Auth email is configured separately in the Supabase dashboard.
Run `composer mail:test -- recipient@example.com` only when you intend to send email.
