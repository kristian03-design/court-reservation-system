# CourtConnect Blueprint

## System Architecture

CourtConnect is organized as a modular Laravel 12 application:

- `app/Models`: users, courts, reservations, payments, schedules, notifications, testimonials, audit logs, and settings.
- `app/Http/Controllers`: public browsing, authentication, booking, user dashboard, payments, and admin operations.
- `app/Services`: reservation availability, reservation creation, payment verification, and reports.
- `app/Http/Requests`: focused validation for reservations, courts, and payment proof uploads.
- `app/Policies` and `app/Http/Middleware`: ownership checks and role-based admin access.
- `resources/views/layouts`, `components`, `partials`, `pages`, `auth`, `user`, and `admin`: modular Blade views instead of one large template.

## Database Schema

Core tables:

- `users`: name, email, phone, password, role, status, email verification fields.
- `courts`: court name, type, description, capacity, hourly rate, image, gallery, amenities, status.
- `reservations`: reservation number, user, court, date, start/end time, players, total, status, notes.
- `payments`: reservation, amount, method, status, proof image, reference number, verification date.
- `court_schedules`: court-level maintenance, closed, reserved, or available windows.
- `notifications`: user messages and read state.
- `testimonials`: landing page reviews.
- `facility_settings`: facility info, operating hours, and reservation rules.
- `audit_logs`: security and operational action tracking hook.

## API Structure

Current JSON endpoints:

- `GET /courts/{court}/availability?date=YYYY-MM-DD`
- `GET /admin/reservation-calendar`

Recommended production API expansion:

- `GET /api/courts`
- `GET /api/courts/{court}`
- `GET /api/courts/{court}/availability`
- `POST /api/reservations`
- `PATCH /api/reservations/{reservation}/cancel`
- `POST /api/payments/{payment}/proof`
- `PATCH /api/admin/reservations/{reservation}`
- `PATCH /api/admin/payments/{payment}`

Use Laravel Sanctum when exposing these endpoints to external clients or mobile apps.

## UI/UX Wireframes

Public flow:

1. Landing page with hero, features, court showcase, process steps, testimonials, and footer.
2. Court listing with search, category filter, sorting, court cards, rates, and booking CTA.
3. Court detail with image, amenities, pricing, status, and daily availability.
4. Booking form with court, date, time slot, players, payment method, notes, review, and confirmation.

Customer flow:

1. Dashboard with statistics, upcoming reservations, payment status, and quick actions.
2. Reservation confirmation page with schedule details, status, payment status, cancel action, and receipt upload.
3. Profile and payment history areas reserved for account-management expansion.

Admin flow:

1. Dashboard with reservation totals, revenue, active courts, pending approvals, calendar, and status breakdown.
2. Court management with add/edit/delete, pricing, status, and image URL.
3. Reservation management with search, filters, detail view, and status updates.
4. Payment verification, users, reports, and settings.

## Payment Strategy

Implemented:

- Pay-at-venue reservations.
- GCash/manual receipt upload.
- Admin payment verification.

Integration hooks:

- Add PayMongo checkout session creation in `PaymentService`.
- Store provider transaction IDs in `payments.reference_number`.
- Process PayMongo webhooks through a signed route.

## Email Notifications

Implemented foundation:

- Laravel email verification.
- In-app user notifications for reservation and payment status changes.

Recommended next email classes:

- ReservationCreatedMail
- ReservationApprovedMail
- ReservationRejectedMail
- ReservationCancelledMail
- ReservationReminderMail
- PaymentConfirmedMail

## Deployment Strategy

1. Configure `.env` for MySQL, SMTP/Gmail Mailer, queue, filesystem, and app URL.
2. Run `composer install --no-dev --optimize-autoloader`.
3. Run `npm install && npm run build`.
4. Run `php artisan key:generate` once per environment.
5. Run `php artisan migrate --force --seed` for first deployment.
6. Run `php artisan storage:link`.
7. Run `php artisan config:cache route:cache view:cache`.
8. Configure queue worker and scheduler:
   - `php artisan queue:work`
   - scheduler cron: `php artisan schedule:run`
9. Point the web server document root to `public`.

## Development Roadmap

Phase 1:

- Stabilize reservations, role access, court management, dashboard views, and seed data.

Phase 2:

- Add profile editing, admin schedule editor, real receipt image previews, audit log writes, and richer tests.

Phase 3:

- Add PayMongo checkout, webhooks, email notification classes, queued reminders, and export packages for PDF/Excel/CSV.

Phase 4:

- Add REST API with Sanctum, mobile-ready endpoints, analytics charts, recurring maintenance windows, and multi-facility support.
