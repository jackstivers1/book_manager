# Book Collection Manager

A full-stack CRUD web application for managing a book collection.
The application is deployed to a public hosting platform and accessible via a custom domain with HTTPS enabled.

------------------------------------------------------------
LIVE DEPLOYMENT
------------------------------------------------------------

Domain Name: bookmanager.shop
Registrar: Namecheap
Hosting Provider: Render (Web Service)
HTTPS: Enabled automatically via Render (SSL certificate provisioned by Render)

------------------------------------------------------------
TECH STACK
------------------------------------------------------------

Frontend:
- HTML5
- CSS3
- Vanilla JavaScript (Fetch API)

Backend:
- PHP 8 (running on Apache via Render)

Database:
- PostgreSQL
- Hosted on Render Managed PostgreSQL Service

------------------------------------------------------------
DATABASE DETAILS
------------------------------------------------------------

Type:
PostgreSQL

Hosting Location:
Render Managed PostgreSQL

Connection Method:
Configured using the DATABASE_URL environment variable.

Schema:

CREATE TABLE books (
  id SERIAL PRIMARY KEY,
  name TEXT NOT NULL,
  pub_date DATE,
  genre TEXT NOT NULL,
  author TEXT NOT NULL,
  image_url TEXT NOT NULL,
  created_at TIMESTAMPTZ DEFAULT NOW()
);

The database is seeded with at least 30 records.

------------------------------------------------------------
APPLICATION FEATURES
------------------------------------------------------------

- Full CRUD functionality (Create, Read, Update, Delete)
- Data persisted in PostgreSQL
- Delete confirmation implemented
- Each record displays an associated image
- Broken/missing images handled with placeholder fallback
- Server-side paging
- User-configurable page size
- Page size preference persisted via browser cookie
- Sorting (multiple fields, ascending/descending)
- Filtering by genre
- Stats view displaying:
    - Total record count
    - Most popular genre
    - Current page size
- Custom domain enabled
- HTTPS secure connection

------------------------------------------------------------
DEPLOYMENT PROCESS
------------------------------------------------------------

Initial Deployment:

1. Push project to GitHub repository.
2. Create a new Web Service in Render (Docker-based PHP service).
3. Create a Render PostgreSQL database.
4. Add environment variable in Render:

   Key: DATABASE_URL
   Value: <Render PostgreSQL connection string>

5. Deploy service via Render dashboard.
6. Run SQL schema and seed data in Render PostgreSQL instance.
7. Add custom domain in Render and configure DNS in Namecheap.
8. Wait for domain verification and automatic HTTPS provisioning.

Updating the Application:

1. Make changes locally.
2. Commit and push to GitHub.
3. Render automatically rebuilds and redeploys the application.
   (Alternatively, trigger Manual Deploy in the Render dashboard.)

------------------------------------------------------------
CONFIGURATION & SECRETS MANAGEMENT
------------------------------------------------------------

Secrets are managed through environment variables in Render.

- DATABASE_URL contains the PostgreSQL connection string.
- No database credentials are stored in the repository.
- SSL certificates are automatically managed by Render.
- Sensitive configuration is never hard-coded in source files.

------------------------------------------------------------
GITHUB REPOSITORY
------------------------------------------------------------

Repository Link:
https://github.com/jackstivers1/book_manager.git

------------------------------------------------------------
REQUIREMENTS SATISFIED
------------------------------------------------------------

- Live custom domain loads application
- HTTPS secure connection enabled
- SQL database (PostgreSQL)
- Minimum 30 seeded records
- Fully functional CRUD operations
- Delete confirmation implemented
- Images displayed per record with placeholder fallback
- Filtering functionality
- Sorting functionality
- Paging with configurable page size
- Page size persisted via cookie
- Stats view accurate and functional
- Deployment and configuration documented
