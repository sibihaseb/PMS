# API Reference

Base URL: `/api`

Authentication: `Authorization: Bearer {token}` (Sanctum) for protected routes.

---

## Auth

### Register

`POST /api/register`

```json
{
  "name": "Jane Owner",
  "email": "jane@acme.test",
  "password": "password123",
  "password_confirmation": "password123",
  "organization_name": "Acme Corp"
}
```

**201 Response:**

```json
{
  "user": {
    "id": 1,
    "name": "Jane Owner",
    "email": "jane@acme.test",
    "role": "owner",
    "organization": {
      "id": 1,
      "name": "Acme Corp",
      "plan": "free"
    }
  },
  "token": "1|..."
}
```

### Login

`POST /api/login`

```json
{
  "email": "demo@workboard.test",
  "password": "password"
}
```

**200 Response:** same shape as register (`user` + `token`).

### Logout

`POST /api/logout` — requires auth.

**200 Response:**

```json
{
  "message": "Logged out successfully."
}
```

### Me

`GET /api/me` — requires auth.

**200 Response:**

```json
{
  "data": {
    "id": 1,
    "name": "Demo Owner",
    "email": "demo@workboard.test",
    "role": "owner",
    "organization": {
      "id": 1,
      "name": "Demo Company",
      "plan": "free"
    }
  }
}
```

---

## Projects

All project routes require auth + organization membership.

### List projects

`GET /api/projects`

**200 Response:**

```json
{
  "data": [
    {
      "id": 1,
      "organization_id": 1,
      "name": "Website Redesign",
      "description": "Q2 initiative",
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

### Create project

`POST /api/projects`

```json
{
  "name": "Website Redesign",
  "description": "Optional description"
}
```

**201** on success. **402** when Free plan org exceeds 3 projects:

```json
{
  "message": "Project limit reached for your current plan. Upgrade to Pro to create more projects."
}
```

### Show project

`GET /api/projects/{id}`

**200** with project object. **404** if project belongs to another organization.

### Update project

`PUT /api/projects/{id}` or `PATCH /api/projects/{id}`

```json
{
  "name": "Updated name"
}
```

### Delete project

`DELETE /api/projects/{id}`

Soft-deletes the project. Soft-deleted projects do not count toward plan usage limits.

**200 Response:**

```json
{
  "message": "Project deleted successfully."
}
```

### Restore project

`POST /api/projects/{id}/restore`

Restores a soft-deleted project within the current organization.

**200 Response:**

```json
{
  "data": {
    "id": 1,
    "organization_id": 1,
    "name": "Website Redesign",
    "description": null,
    "created_at": "...",
    "updated_at": "..."
  }
}
```

---

## Tasks

### List tasks

`GET /api/projects/{project}/tasks`

**200 Response:**

```json
{
  "data": [
    {
      "id": 1,
      "project_id": 1,
      "organization_id": 1,
      "title": "Write API docs",
      "created_at": "...",
      "updated_at": "..."
    }
  ]
}
```

### Create task

`POST /api/projects/{project}/tasks`

```json
{
  "title": "Write API docs"
}
```

**201** with task object.

---

## Billing

Billing is scoped to the **organization**. Any org member can read status; only the **owner** can start checkout.

### Billing status

`GET /api/billing/status`

**200 Response:**

```json
{
  "plan": "free",
  "projects_used": 2,
  "projects_limit": 3
}
```

For Pro: `"plan": "pro"`, `"projects_limit": null`.

For Team: `"plan": "team"`, `"projects_limit": 20`.

### Checkout (subscribe to Pro or Team)

`POST /api/billing/checkout` — owner only.

```json
{
  "plan": "pro"
}
```

Use `"plan": "team"` for the Team plan (20 projects).

**200 Response:**

```json
{
  "checkout_url": "https://checkout.stripe.com/..."
}
```

### Stripe webhook

`POST /api/stripe/webhook` — called by Stripe (not for manual use). Handled by Laravel Cashier.

---

## Validation errors

**422 Response:**

```json
{
  "message": "The name field is required.",
  "errors": {
    "name": ["The name field is required."]
  }
}
```

## Authorization errors

**403 Response** when user has no organization or lacks permission (e.g. member attempting checkout).

**429 Response** when the organization exceeds 60 API requests per minute on protected routes.

**401 Response** when token is missing or invalid.
