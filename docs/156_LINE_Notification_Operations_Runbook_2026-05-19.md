# LINE Notification Operations Runbook

Date: 2026-05-19

## Purpose

This runbook documents the exact operational path for validating LINE Messaging API delivery in Daily Ops Command Center. It exists so the project can be demonstrated without relying on manual curl commands or creating fake incidents only to test connectivity.

## Required Environment

```env
LINE_NOTIFICATIONS_ENABLED=true
LINE_CHANNEL_ACCESS_TOKEN=<Messaging API channel access token>
LINE_NOTIFICATION_TO=<LINE userId, groupId, or roomId>
LINE_NOTIFICATION_TIMEOUT=5
QUEUE_CONNECTION=database
APP_URL=http://localhost:8000
```

For local demonstration, `APP_URL=http://localhost:8000` is acceptable. For a phone or deployed environment, `APP_URL` must be a reachable HTTPS URL; otherwise the LINE message will send successfully but the detail link will only work on the server machine.

## Validation Steps

1. Run migrations:

```bash
php artisan migrate
```

2. Send a direct LINE probe:

```bash
php artisan notifications:line:test
```

Expected successful result:

- command exits with code `0`
- LINE receives a `[Daily Ops] LINE connection test...` message
- management page `ประวัติแจ้งเตือน` shows a `ทดสอบ LINE notification` row with status `ส่งสำเร็จ`

3. Validate the queued incident flow:

```bash
php artisan queue:work --once --tries=1 --timeout=30
```

Then create a new incident from the staff workflow. The queue worker should process `SendExternalNotificationOnIncidentEvent`, and the management audit page should show a `รายงานปัญหาใหม่` delivery row.

## Failure Interpretation

- `skipped_disabled`: `LINE_NOTIFICATIONS_ENABLED` is false.
- `skipped_incomplete_config`: token or recipient id is missing.
- `failed`: LINE API responded with a non-2xx HTTP status. Check token validity, recipient relationship, and channel settings.
- `failed_exception`: the app could not reach LINE or the HTTP client threw an exception.

The system intentionally records failures but does not block incident creation, status updates, or assignment changes.

## Manual Redelivery

From `ประวัติแจ้งเตือน`, management users can manually redeliver failed incident-linked LINE rows. The system does not pretend to recreate the original payload; it sends a clear `ส่งซ้ำการแจ้งเตือน` message with the incident, original event type, current status, and detail link.

Manual redelivery creates a new `manual_redelivery` row in `notification_deliveries` so the audit trail remains append-only.

## Brutal Truth

This implementation is now demo-safe and auditable. It is not a full notification operations platform yet because it does not include role-based recipient routing or scheduled escalation.
