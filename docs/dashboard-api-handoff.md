# Dashboard API — Frontend Handoff

This is the reference for building the admin dashboard on top of the API. It covers how to sign in, how permissions decide what to show, and every endpoint the dashboard uses today: **Users**, **Roles** and **Permissions**, plus the shared auth, profile and notification endpoints.

> Base URL: `{{BASE_URL}}/api/v1` (local: `http://localhost/api/v1`)
> A Postman collection with all of these requests is in the repo: `graduation-project.postman_collection.json`.

---

## Contents

1. [The big picture](#1-the-big-picture)
2. [Headers on every request](#2-headers-on-every-request)
3. [Response format](#3-response-format)
4. [Errors and how to handle them](#4-errors-and-how-to-handle-them)
5. [Authentication flow](#5-authentication-flow)
6. [Permissions: what to show and hide](#6-permissions-what-to-show-and-hide)
7. [Lists: pagination, filters, search](#7-lists-pagination-filters-search)
8. [Users module](#8-users-module)
9. [Roles module](#9-roles-module)
10. [Permissions list (for the role form)](#10-permissions-list-for-the-role-form)
11. [My profile and notifications](#11-my-profile-and-notifications)
12. [Screen-by-screen checklist](#12-screen-by-screen-checklist)

---

## 1. The big picture

The API has two areas:

| Area | Prefix | Who uses it | Protected by |
|---|---|---|---|
| **Public** | `/api/v1/public/...` | everyone (sign in, own profile, notifications, settings) | token only, no permissions |
| **Admin** | `/api/v1/admin/...` | dashboard management screens | token **+ admin user type + a permission per endpoint** |

**User types** (the `type` field on a user), from highest to lowest:

| `type` | Label | Can open the dashboard? | Notes |
|---|---|---|---|
| `super_admin` | Super Admin | Yes | Has **every** permission automatically. No roles needed. |
| `admin` | Admin | Yes | Gets permissions **only through roles**. |
| `volunteer` | Volunteer | No | App user. |
| `donor` | Donor | No | App user. |

**Hierarchy rule:** a user can only see and manage users of **their own type or lower**. An `admin` never sees `super_admin` accounts in the list and cannot create one. The backend enforces this; the frontend just needs to use the options the API returns (see `form-options`).

**Owner account:** user `id = 1` is the seeded owner. Only the owner can see or edit it. Others won't get it in the list, and opening it directly returns `403`.

---

## 2. Headers on every request

```http
Accept: application/json
Content-Type: application/json        # or multipart/form-data when uploading an image
Authorization: Bearer {access_token}  # every request except login / settings
lang: ar                              # optional: "ar" or "en"
```

**Language:** before sign-in, the `lang` header picks the language of the messages. **Once signed in, the user's own `locale` wins** and the header is ignored. To switch the dashboard language for a signed-in user, update their `locale` (see [§11](#11-my-profile-and-notifications)).

---

## 3. Response format

Every response uses the same envelope.

**Success:**

```json
{
  "status": "success",
  "message": "Success",
  "data": { }
}
```

**Error:**

```json
{
  "status": "error",
  "message": "Human readable message, already translated"
}
```

`status` can be `success`, `error`, `unauthenticated` or `unauthorized`. **Use the HTTP status code for your logic** and show `message` to the user.

---

## 4. Errors and how to handle them

| HTTP | `status` | When | What the frontend should do |
|---|---|---|---|
| `401` | `unauthenticated` | Missing, wrong or expired access token | Try **refresh** once (see §5). If that fails, log out and go to the login page. |
| `403` | `unauthorized` | Not an admin, or missing the permission for this endpoint | Show a "no permission" message/page. Don't retry. |
| `403` | `error` | Business rule, e.g. "You cannot delete your own account", "Your account is not active" | Show `message` as a toast. "Your account is not active" → log the user out. |
| `404` | `error` | Record doesn't exist (or is deleted) | Show "not found" and go back to the list. |
| `409` | `error` | Conflict, e.g. deleting a role that users still hold | Show `message`. |
| `422` | `error` | Validation failed | Show `errors` under each field (format below). |
| `429` | — | Too many requests (login is rate limited) | Show "try again later". |
| `500` | `error` | Server bug | Show a generic error. Send the `reference` value to the backend team. |
| `503` | `error` | Site closed (public endpoints only; admins still get through) | Show a maintenance message. |

**Validation error (`422`) format:**

```json
{
  "status": "error",
  "message": "validation_error",
  "errors": {
    "email": ["The email has already been taken."],
    "roles.0": ["The selected roles.0 is invalid."]
  }
}
```

Each key is the field name and each value is a list of messages, so show the first one under the input. Array fields come back as `field.index`, like `roles.0`.

---

## 5. Authentication flow

The API uses **two tokens**:

| Token | Lifetime (default) | Used for |
|---|---|---|
| `access_token` | 60 minutes | Every API call, as `Authorization: Bearer ...` |
| `refresh_token` | 30 days | **Only** for `POST /public/refresh` |

### 5.1 Login — `POST /public/login`

```json
{
  "email": "admin@example.com",
  "password": "12345678",
  "imei": "dashboard-<a unique id you generate once and keep in localStorage>",
  "token": "<FCM web push token, or any non-empty string if you don't use push>",
  "device_type": "website"
}
```

| Field | Rules |
|---|---|
| `email` | required. The user's **email or phone**; the backend detects which. |
| `password` | required, min 8 |
| `imei` | required. A device id. Generate a UUID once per browser and reuse it, because you also send it to logout. |
| `token` | required. Push notification token. |
| `device_type` | required. Always `website` for the dashboard. |

**Response `200`:**

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "data": { "id": 1, "full_name": "Super Admin", "type": "super_admin", "...": "UserResource, see §8.3" },
    "access_token": "12|abc...",
    "refresh_token": "13|def...",
    "token_type": "Bearer",
    "expires_in": 3600,
    "access_token_expires_at": "2026-11-18T13:00:00+00:00",
    "refresh_token_expires_at": "2026-12-18T12:00:00+00:00"
  }
}
```

Wrong credentials or an inactive account → `401`.

> ⚠️ **Login works for any user type.** After login, check `data.data.type`. If it is **not** `admin` or `super_admin`, show "You don't have access to the dashboard" and don't keep the tokens. Every `/admin/*` call would return `403` for them anyway.

**After a successful login:**
1. Save `access_token`, `refresh_token` and the user.
2. Call `GET /public/profile/permissions` to build the sidebar (§6.1).
3. Go to the first page the user is allowed to open.

### 5.2 Refresh — `POST /public/refresh`

Send the **refresh token** (not the access token) as the bearer:

```http
Authorization: Bearer {refresh_token}
```

No body. The response has the same shape as login, with a **new access token and a new refresh token**. The old pair stops working, so replace both.

**Recommended:** in your HTTP interceptor, on a `401`:
1. If a refresh is already running, wait for it.
2. Otherwise call `/public/refresh` once.
3. On success, save the new tokens and retry the original request.
4. On failure, clear storage and redirect to login.

You can also refresh ahead of time using `access_token_expires_at`.

### 5.3 Logout — `POST /public/logout`

```json
{ "imei": "the same imei you sent at login" }
```

This revokes the token pair and removes the device. Clear local storage afterwards, whatever the response.

### 5.4 App settings — `GET /public/settings` (no token)

Returns public site settings you can use on the login screen (logo, contacts):

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "site_email": "...", "site_phone": "...", "address": "...",
    "social_login": "...", "site_open": "1", "logo_image": "...",
    "facebook": "...", "twitter": "...", "youtube": "...", "instagram": "...",
    "whatsapp": "...", "snapchat": "...", "tiktok": "..."
  }
}
```

---

## 6. Permissions: what to show and hide

Permissions are named `{module}.{action}`, for example `users.index` or `roles.store`. Every admin endpoint needs one (listed with each endpoint below). A `super_admin` has all of them.

Permissions control **three levels** of the UI, and the API gives you a ready-made answer for each one:

| Level | Where the answer comes from |
|---|---|
| Sidebar items (which pages exist) | `GET /public/profile/permissions` |
| Buttons on a list page (Add / Edit / Delete / Status / Restore) | the `permissions` object returned **with every list response** |
| Role form checkboxes | `GET /admin/permissions` |

You never need to compare permission names yourself. Just read the flags.

### 6.1 Sidebar — `GET /public/profile/permissions`

Call it after login and on every app reload.

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "is_users": true,
    "is_roles": false
  }
}
```

| Flag | Sidebar item | Route guard |
|---|---|---|
| `is_users` | Users | `users.index` |
| `is_roles` | Roles | `roles.index` |

- Show an item only when its flag is `true`.
- **Also protect the routes** in your router. If a user types `/roles` in the URL and `is_roles` is `false`, redirect them.
- More flags will appear here as new modules are added (cities, settings, logs, ...). Write your sidebar so it reads flags from a config map and doesn't hard-code only these two.

### 6.2 List page buttons

Every list endpoint returns a `permissions` object next to the data. Example from `GET /admin/users`:

```json
"permissions": {
  "is_store": true,
  "is_update": true,
  "is_delete": false,
  "is_status": true,
  "is_restore": false
}
```

| Flag | Show |
|---|---|
| `is_store` | "Add new" button |
| `is_update` | "Edit" action on each row |
| `is_delete` | "Delete" action |
| `is_status` | Active / inactive toggle |
| `is_restore` | "Restore" action on deleted rows (users only) |

Roles have no status toggle, so the roles list returns only `is_store`, `is_update` and `is_delete`.

> Hiding a button is for UX only. The backend checks the permission again and returns `403` if someone calls it anyway.

---

## 7. Lists: pagination, filters, search

All list endpoints share these query parameters:

| Param | Default | Description |
|---|---|---|
| `page` | `1` | Page number |
| `limit` | `10` | Items per page (1–100) |
| `all_data` | `1` | Send `0` to get **all rows without pagination** (useful for dropdowns). `meta` and `links` are then omitted. |
| `search` | — | Free text search |

Invalid filter values return `422`. They are not silently ignored.

**List response shape:**

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "data": [ { "...": "item" } ],
    "meta": {
      "total": 57,
      "count": 10,
      "per_page": 10,
      "current_page": 1,
      "total_pages": 6,
      "last_page": 6,
      "from": 1,
      "to": 10,
      "path": "http://localhost/api/v1/admin/users"
    },
    "links": {
      "first": "...?page=1",
      "last": "...?page=6",
      "prev": null,
      "next": "...?page=2"
    },
    "permissions": { "is_store": true, "...": "see §6.2" }
  }
}
```

Note the nesting: rows are in **`data.data`**, pagination in **`data.meta`**.

**Tip:** keep filters in the URL query string (`/users?type=admin&page=2`) so refresh and the back button keep the state. Debounce the search input (~400 ms) and reset to `page=1` when any filter changes.

---

## 8. Users module

| Method | Endpoint | Permission | Purpose |
|---|---|---|---|
| GET | `/admin/users` | `users.index` | List |
| GET | `/admin/users/form-options` | `users.store` **or** `users.update` | Dropdown data for the create/edit form |
| POST | `/admin/users` | `users.store` | Create |
| GET | `/admin/users/{id}` | `users.show` | Show one |
| PUT | `/admin/users/{id}` | `users.update` | Edit |
| PATCH | `/admin/users/{id}/status` | `users.status` | Toggle active / inactive |
| DELETE | `/admin/users/{id}` | `users.destroy` | Soft delete |
| PATCH | `/admin/users/{id}/restore` | `users.restore` | Restore a deleted user |

### 8.1 List — `GET /admin/users`

| Filter | Values | Description |
|---|---|---|
| `search` | text | Searches full name, email and phone |
| `type` | `all` (default), `super_admin`, `admin`, `volunteer`, `donor` | Filter by type |
| `active` | `1` / `0` | Active or inactive only. Leave it out for both. |
| `trashed` | `only` / `with` | `only` = deleted users only (for a "Trash" tab), `with` = deleted + not deleted. Leave it out for normal users only. |
| `date_start` | `YYYY-MM-DD` | Created on or after |
| `date_end` | `YYYY-MM-DD` | Created on or before (must be ≥ `date_start`) |
| + `page`, `limit`, `all_data` | | See §7 |

Results are newest first and include each user's `roles`. Users the current admin outranks are excluded automatically.

**Suggested UI:** tabs or a select for `type`, an active/inactive select, a date range picker, a search box, and a "Deleted users" tab using `trashed=only`. In that tab show **Restore** (if `is_restore`) instead of Edit/Delete.

### 8.2 Form options — `GET /admin/users/form-options`

Call this when the create or edit form opens. **Don't hard-code these lists.**

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "types": [
      { "value": "donor", "label": "Donor" },
      { "value": "volunteer", "label": "Volunteer" },
      { "value": "admin", "label": "Admin" }
    ],
    "roles": [
      { "id": 1, "name": "admin", "display_name": "Admin" }
    ],
    "genders": [
      { "value": "male", "label": "Male" },
      { "value": "female", "label": "Female" }
    ],
    "locales": [
      { "value": "ar", "label": "العربية" },
      { "value": "en", "label": "English" }
    ],
    "roles_for_types": ["admin"],
    "email_required_for": ["admin", "super_admin"]
  }
}
```

| Key | Use it for |
|---|---|
| `types` | The **Type** select. It already contains only the types this admin may assign. |
| `roles` | The **Roles** multi-select |
| `genders`, `locales` | Their selects |
| `roles_for_types` | Show the Roles field **only** when the selected type is in this list (today: `admin`) |
| `email_required_for` | Mark Email as **required** when the selected type is in this list; otherwise optional |

Labels come translated into the user's language.

### 8.3 User object (`UserResource`)

```json
{
  "id": 5,
  "full_name": "Ahmed Ali",
  "phone": "01012345678",
  "email": "ahmed@example.com",
  "type": "admin",
  "type_name": "Admin",
  "roles": [ { "id": 1, "name": "admin", "display_name": "Admin" } ],
  "image": "http://localhost/uploads/users/abc.jpg",
  "gender": "male",
  "gender_name": "Male",
  "birth_date": "1998-04-12",
  "locale": "ar",
  "theme": "light",
  "is_notify": 1,
  "active": 1,
  "last_active": "2026-11-18 11:20:00",
  "created_at": "2026-11-01 09:00:00",
  "updated_at": "2026-11-18 10:00:00",
  "deleted_at": null
}
```

- `active` and `is_notify` are `1` / `0`, not booleans.
- `deleted_at` is set only for deleted users (in the `trashed` lists).
- Use `type_name` and `gender_name` for display; they are translated.

### 8.4 Create — `POST /admin/users`

| Field | Rules |
|---|---|
| `full_name` | **required**, 2–255 chars |
| `type` | **required**, one of `form-options.types[].value` |
| `phone` | **required**, unique, must match the site's phone format |
| `email` | **required if** type is in `email_required_for`, otherwise optional. Valid email, unique. |
| `password` | **required**, min 8 |
| `confirm_password` | must equal `password` |
| `roles` | array of role ids. **Only used when type = `admin`**; ignored for other types. |
| `image` | optional image: jpeg, png, jpg, gif, webp, max 2 MB |
| `active` | optional, `1` / `0` (default active) |
| `is_notify` | optional, `1` / `0` |
| `locale` | optional, `ar` / `en` |
| `gender` | optional, `male` / `female` |
| `birth_date` | optional, `YYYY-MM-DD`, user must be at least 15 |

**Sending it:**
- **With an image:** use `multipart/form-data`. Send arrays as `roles[]=1`, `roles[]=3` and booleans as `1` / `0`.
- **Without an image:** plain JSON is fine: `"roles": [1, 3]`.

**Response `201`:** `{ "data": { "user": { ...UserResource } } }`

Trying to create a type above your own → `403`.

### 8.5 Show — `GET /admin/users/{id}`

Response: `{ "data": { "user": { ...UserResource } } }`. Use it to prefill the edit form.
A deleted user returns `404` (restore it first). A user you may not manage returns `403`.

### 8.6 Update — `PUT /admin/users/{id}`

Same fields and rules as create, with two differences:

- **Send the full form** (`full_name`, `type`, `phone`, and `email` when required are all required again).
- `password` is **optional**. Leave it empty to keep the current password. If you send it, also send `confirm_password`.

**Roles behaviour on update:**
- Field **not sent** → roles stay as they are.
- Sent as `[]` → all roles removed.
- Type changed away from `admin` → roles are removed automatically.

> ⚠️ **Uploading an image on edit:** PHP can't read `multipart/form-data` on a real `PUT`. Send **`POST /admin/users/{id}`** with an extra form field **`_method=PUT`**. Without an image, a normal JSON `PUT` works.

Business rules that return `403`:
- Editing a user above your type, or assigning a type above yours.
- Changing **your own** type → "You cannot change your own type". Disable the Type select when editing yourself.

Response: `{ "data": { "user": { ...UserResource } } }`

### 8.7 Toggle status — `PATCH /admin/users/{id}/status`

No body. Flips `active` between `1` and `0` and returns the updated user. Deactivating a user **signs them out of every device** immediately.

You can't deactivate yourself (`403`), so hide the toggle on your own row.

### 8.8 Delete — `DELETE /admin/users/{id}`

Soft delete: the user moves to the "deleted" list (`trashed=only`) and is signed out everywhere. Always ask for confirmation first.

```json
{ "status": "success", "message": "Deleted successfully", "data": null }
```

You can't delete yourself (`403`), so hide Delete on your own row.

### 8.9 Restore — `PATCH /admin/users/{id}/restore`

No body. Brings a soft-deleted user back and returns the user. Show this only in the deleted users view, when `is_restore` is `true`.

---

## 9. Roles module

Roles group permissions. They only matter for users of type `admin`.

| Method | Endpoint | Permission | Purpose |
|---|---|---|---|
| GET | `/admin/roles` | `roles.index` | List |
| POST | `/admin/roles` | `roles.store` | Create |
| GET | `/admin/roles/{id}` | `roles.show` | Show one (with its permissions) |
| PUT | `/admin/roles/{id}` | `roles.update` | Edit |
| DELETE | `/admin/roles/{id}` | `roles.destroy` | Delete |

### 9.1 List — `GET /admin/roles`

Filters: `search` (name or display name), plus `page`, `limit`, `all_data`. Newest first.

Each row:

```json
{
  "id": 2,
  "name": "content_manager",
  "display_name": "Content Manager",
  "description": "Manages cities and users",
  "users_count": 4,
  "permissions_count": 12,
  "created_at": "2026-11-10 10:00:00",
  "updated_at": "2026-11-10 10:00:00"
}
```

`permissions` on the list: `is_store`, `is_update`, `is_delete`.

**Suggested columns:** Display name, Name, Users count, Permissions count, Created at, Actions.

### 9.2 Show — `GET /admin/roles/{id}`

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "role": {
      "id": 2,
      "name": "content_manager",
      "display_name": "Content Manager",
      "description": "...",
      "users_count": 4,
      "permissions": [
        { "id": 10, "name": "users.index", "display_name": "view", "module": "users" },
        { "id": 11, "name": "users.show",  "display_name": "show", "module": "users" }
      ],
      "created_at": "...",
      "updated_at": "..."
    }
  }
}
```

To prefill the edit form, **tick the checkboxes whose ids are in `role.permissions[].id`**.

### 9.3 Create — `POST /admin/roles` / Update — `PUT /admin/roles/{id}`

```json
{
  "name": "content_manager",
  "display_name": "Content Manager",
  "description": "Optional text",
  "permissions": [10, 11, 15, 22]
}
```

| Field | Rules |
|---|---|
| `name` | **required**, unique, only lowercase letters, numbers and `_` (e.g. `content_manager`). Tip: auto-generate it from the display name. |
| `display_name` | **required**, max 255 |
| `description` | optional, max 255 |
| `permissions` | array of permission **ids** from `GET /admin/permissions` |

On **update**:
- `permissions` **not sent** → permissions stay as they are.
- `permissions: []` → all permissions removed.

Create returns `201` with `{ "data": { "role": {...} } }`; update returns `200` with the same shape.

### 9.4 Delete — `DELETE /admin/roles/{id}`

| Result | Meaning | UI |
|---|---|---|
| `200` "Deleted successfully" | Done | Remove the row |
| `403` "This role cannot be deleted" | System role (e.g. the seeded `admin` role) | Show message. You can hide Delete for `name === "admin"`. |
| `409` "This role is assigned to users" | Users still hold it | Show message. You can disable Delete when `users_count > 0`. |

---

## 10. Permissions list (for the role form)

### `GET /admin/permissions` — needs `roles.index`

Returns every permission, grouped by module, to build the checkbox grid on the role create/edit form.

> A normal admin only receives permissions **they hold themselves**, so nobody can give a role more than they have. A super admin receives all of them.

```json
{
  "status": "success",
  "message": "Success",
  "data": {
    "resources": [
      {
        "module": "users",
        "module_name": "Users",
        "permissions": [
          { "id": 10, "name": "users.index",   "display_name": "view",    "module": "users" },
          { "id": 11, "name": "users.show",    "display_name": "show",    "module": "users" },
          { "id": 12, "name": "users.store",   "display_name": "create",  "module": "users" },
          { "id": 13, "name": "users.update",  "display_name": "edit",    "module": "users" },
          { "id": 14, "name": "users.status",  "display_name": "status",  "module": "users" },
          { "id": 15, "name": "users.destroy", "display_name": "delete",  "module": "users" },
          { "id": 16, "name": "users.restore", "display_name": "restore", "module": "users" }
        ]
      },
      {
        "module": "roles",
        "module_name": "Roles",
        "permissions": [ "..." ]
      }
    ],
    "custom": [
      { "id": 40, "name": "dashboard",  "label": "Dashboard" },
      { "id": 41, "name": "statistics", "label": "Statistics" }
    ]
  }
}
```

**How to render the form:**

```
┌ Users ─────────────────────────────── [ ] Select all ┐
│ [x] view  [x] show  [ ] create  [ ] edit             │
│ [ ] status  [ ] delete  [ ] restore                  │
└──────────────────────────────────────────────────────┘
┌ Roles ─────────────────────────────── [ ] Select all ┐
│ ...                                                  │
└──────────────────────────────────────────────────────┘
┌ Other ───────────────────────────────────────────────┐
│ [ ] Dashboard   [ ] Statistics                       │
└──────────────────────────────────────────────────────┘
                                   [ ] Select everything
```

- One card per item in `resources`, titled `module_name`.
- One checkbox per permission. `display_name` is the action key (`view`, `show`, `create`, `edit`, `status`, `delete`, `restore`); translate it on the frontend. The order is already correct.
- A **"Select all"** checkbox per card is a nice touch.
- `custom` items are standalone screens. Show them in an "Other" card using `label`.
- **Submit the selected `id`s** as `permissions: [...]`.

**UX rule worth adding:** when a user ticks `create`, `edit`, `delete`, `status` or `restore` in a module, also tick `view` automatically. Without `view` they can't open the list page to reach those buttons.

---

## 11. My profile and notifications

These are under `/public` and need only the access token, no permission.

| Method | Endpoint | Body | Purpose |
|---|---|---|---|
| GET | `/public/profile` | — | Current user: `{ "data": { "user": {...}, ...settings } }` |
| PUT | `/public/profile` | `full_name` (required), `gender`, `date_of_birth` (`YYYY-MM-DD`), `email` | Edit my info |
| POST | `/public/profile/change/password` | `password_old`, `password`, `confirm_password` (min 8, new ≠ old) | Change my password |
| POST | `/public/profile/change/image` | `image` (multipart; jpeg/jpg/png/gif/svg, max 4 MB) | Change my avatar |
| POST | `/public/profile/change/lang` | `locale`: `ar` / `en` | Change my language |
| GET | `/public/profile/permissions` | — | Sidebar flags (§6.1) |
| POST | `/public/logout` | `imei` | Logout (§5.3) |
| GET | `/public/notifications` | query: `read=1/0`, `read_all=1`, `read_only=1` | My notifications, paginated |
| GET | `/public/notifications/{id}` | — | Open one (also marks it read) |
| DELETE | `/public/notifications/{id}` | — | Delete one |

**Notifications tips:**
- `GET /public/notifications?read=0` → unread only (for the bell badge).
- `GET /public/notifications?read_all=1&read_only=1` → mark all as read without fetching ("Mark all as read").
- After changing language, reload the user (`GET /public/profile`) and switch the UI direction (RTL for `ar`, LTR for `en`).

---

## 12. Screen-by-screen checklist

### App bootstrap
- [ ] On load: if there's a token, call `GET /public/profile` and `GET /public/profile/permissions`.
- [ ] Interceptor: add `Authorization`, `Accept`, `lang`; on `401` refresh once, then log out.
- [ ] Set the UI language and direction from `user.locale`.
- [ ] Router guards based on sidebar flags.

### Login page
- [ ] Email/phone + password form → `POST /public/login` with `device_type: "website"` and a persistent `imei`.
- [ ] Reject non-admin types (`donor`, `volunteer`) on the frontend.
- [ ] Show `429` as "Too many attempts, try again later".

### Users list
- [ ] Table: image, full name, email, phone, type (`type_name`), roles, active toggle, created at, actions.
- [ ] Filters: search, type, active, date range, "Deleted" tab (`trashed=only`).
- [ ] Buttons driven by `data.permissions`.
- [ ] Hide status toggle and delete on **your own** row.
- [ ] Confirm before delete; on success refresh the list.

### User create/edit form
- [ ] Load `GET /admin/users/form-options` first.
- [ ] Show Roles only when type ∈ `roles_for_types`.
- [ ] Email required only when type ∈ `email_required_for`.
- [ ] Password required on create, optional on edit (+ `confirm_password`).
- [ ] Image upload → multipart; on edit use `POST` + `_method=PUT`.
- [ ] Disable the type select when editing yourself.
- [ ] Map `422` errors to fields.

### Roles list
- [ ] Table: display name, name, users count, permissions count, created at, actions.
- [ ] Search box; buttons from `data.permissions`.
- [ ] Handle `403` (system role) and `409` (role in use) on delete.

### Role create/edit form
- [ ] Load `GET /admin/permissions` and render cards per module + "Other".
- [ ] On edit, load `GET /admin/roles/{id}` and tick `role.permissions[].id`.
- [ ] `name`: lowercase/numbers/underscore only (auto-generate from display name).
- [ ] Submit `permissions` as an array of ids.

---

### Questions?

If an endpoint behaves differently from this document, or you need a field or filter that isn't here, contact the backend team **with the request, the response and the `reference` value (for 500 errors)**.
