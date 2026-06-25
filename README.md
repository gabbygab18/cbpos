# JACB Time Tracker

Standalone Laravel app for Arlene's team: members log in, stamp the time they
spend on each task (Type of Report + Facility), and admins can monitor
handling time per person and edit time/date if needed.

This is a **separate, standalone Laravel project** — not a module inside
AyosNegosyoPH. It only borrows the same coding conventions.

## What it does

- **Members**
  - Log in / log out (session-based, plain email + password).
  - Start a task: pick *Type of Report* and *Facility*, timer starts.
  - Stop a task when done — duration is calculated automatically.
  - See today's tasks and total hours on their dashboard.
  - Cannot edit the time or date of a logged task (per Arlene's instruction)
    — they can only cancel a task that's still *running* if they started it
    by mistake.

- **Admins** (Jaybee, Arlene, Jo — seeded already)
  - See every member's tasks for any date ("Team Overview").
  - Open a member's full timesheet for a date range, with totals
    (tasks, hours, days worked) — printable via the browser's Print.
  - Edit a task's report type, facility, work date, and start/end time.
    Edited rows are marked "edited" with a tooltip showing who and when.
  - Manage the **Facilities** list and **Report/Task Types** list —
    these are exactly the dropdowns members see when starting a task.
  - Manage member/admin accounts (add, edit, activate/deactivate).

- A member's running task is auto-stopped at logout, so a forgotten timer
  doesn't keep counting hours in the background. If your team wants a
  different behavior here (e.g. block logout while a task is running
  instead), it's a small change in `AuthController@logout`.

## Setup (XAMPP / local)

1. Copy this folder into `htdocs` (or wherever your vhost points), e.g.
   `C:\xampp\htdocs\jacb-timetracker`.
2. Copy `.env.example` to `.env` and set your DB credentials if different
   from the defaults (`root` / no password, database name
   `jacb_timetracker`).
3. Create the database in phpMyAdmin: `jacb_timetracker`.
4. From the project folder:
   ```
   composer install
   php artisan key:generate
   php artisan migrate
   php artisan db:seed
   ```
5. Serve it:
   ```
   php artisan serve
   ```
   or point your Apache vhost at `/public` like your other Laravel projects.

## Seeded logins

Every seeded account uses the password **`password123`** — have everyone
change it after their first login (there's no "change password" screen yet;
an admin can reset anyone's password from **Members → Edit**).

**Admins:** Jaybee Beley, Arlene Fabay, Jo Beley — emails as given in chat.

**Members:** all 23 names from the list Arlene sent (Dharell Sales through
Madel Guadalupe) — emails as given in chat.

**Facilities:** all 24 (Great Neck through Bridge View) — editable under
**Facilities** once logged in as an admin.

**Report/Task Types:** all 39 unique types from Arlene's list (Daily Skilled
Notes, IPA, PDPM, the "- QA" variants, etc.) — editable under
**Report Types**.

## Notes / things to double check with Arlene before going live

- I mapped "Reports/Tasks" to a single **Type of Report** dropdown per task
  stamp, alongside **Facility** — matching the screenshot ("John Smith —
  Today, 4 tasks, 7.8h... Type of Report / Facility / Time / Dur"). If she
  actually wants a task to optionally cover *multiple* report types at once,
  that's a bigger change — confirm first.
- "24 hour Re-audit" appeared twice in her list — I only kept one (looked
  like a typo on her end). Easy to split back into two if she meant two
  different things.
- No SMS/email notifications, no payroll integration, no geofencing/photo
  capture — this is intentionally just the time-stamping + reporting piece
  she asked for. Let me know if she wants any of that layered on.
# cbpos
