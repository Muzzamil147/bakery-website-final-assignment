# Project Report: Golden Crust Bakery Website

*[Add your name, course, and submission date here]*

---

## 1. Business Problem

Golden Crust Bakery is a small, family-run neighborhood bakery that has relied
entirely on foot traffic and word-of-mouth since opening in 2015. Like many
local businesses of its size, it has no online presence: no way for customers
to browse the day's menu before visiting, no way to see what the bakery
actually looks like inside, and no way to reach the business outside of a
phone call during store hours. This creates several concrete problems:

- Customers researching bakeries nearby have no way to find Golden Crust or
  compare its offerings to competitors who do have websites.
- Custom orders (birthday cakes, event catering) currently require an
  in-person or phone conversation, with no easy way to send an inquiry outside
  business hours.
- The bakery's own staff have no simple way to update the menu, add seasonal
  items, or showcase new products without technical help.

## 2. Proposed Solution

A responsive, database-driven website was built to solve both the
customer-facing problem (no online presence) and the operational problem (no
easy way for the business to manage its own content).

The site is split into two parts:

- **A public-facing website** — Home, About, Products, Gallery, and Contact
  pages — giving prospective customers everything they'd want before walking
  in: what the bakery sells, who runs it, what it looks like, and a way to
  send an inquiry at any time via a contact form.
- **A secure admin panel** — allowing bakery staff, without any coding
  knowledge, to log in and add, edit, or remove products, team members, and
  gallery photos, including uploading their own photos. This means the
  bakery's own content doesn't go stale — a new hire can update the team page,
  or a new product can go live, without depending on a developer.

The system is backed by a relational MySQL database (6 tables, including a
foreign-key relationship between products and categories), so the site's
content is dynamic rather than hardcoded — every public page reflects live
data set by whoever is managing the admin panel.

**Key features implemented:**
- Full CRUD (Create, Read, Update, Delete) for products, categories, team
  members, and gallery photos
- Secure admin authentication (hashed passwords, session-based login, CSRF
  protection on every form)
- Image upload with server-side validation (real file-content checking, not
  just trusting the filename)
- A contact form that stores submissions in the database, viewable and
  manageable from the admin panel
- Fully responsive layout, tested at both mobile and desktop widths

## 3. AI Tools Used

This project was built with **Claude** (Anthropic's AI coding assistant,
via Claude Code), used throughout the development process for:

- **Scaffolding the project structure** — setting up the PHP file
  organization (public pages, shared includes, admin panel, config), and
  designing the MySQL schema with proper relationships and constraints.
- **Generating CRUD implementations** — the create/read/update/delete logic
  for products, categories, team members, and gallery photos was
  AI-generated, then reviewed and tested.
- **Security implementation** — Claude implemented password hashing
  (`password_hash`/`password_verify`), prepared statements to prevent SQL
  injection, CSRF token verification, and MIME-type validation for image
  uploads (rather than trusting file extensions).
- **CSS and responsive design** — the visual design (color palette,
  typography, layout, mobile breakpoints) was generated and iterated with AI
  assistance.
- **Testing and debugging** — Claude ran the site locally (starting the PHP
  and MySQL servers), tested every CRUD flow end-to-end (including a live
  image upload and a deliberately disguised malicious file to confirm the
  upload validation actually rejects it), and fixed issues found during that
  testing.

All AI-generated code was reviewed, run, and verified locally before being
considered complete — this report itself documents that verification process.

## 4. Challenges Faced

*[This section should reflect your own experience working on this project —
Claude can help you write it up, but it should describe what YOU actually
found difficult, not a generic list. Some prompts to get started:]*

- What part of the assignment brief was hardest to plan for before you
  started (e.g. deciding on the database schema, deciding what "full CRUD"
  should cover)?
- Did you hit any specific technical errors while setting this up on your own
  machine (database connection issues, PHP version differences, file upload
  permission problems)? How did you resolve them?
- What would you do differently if you started over?
- What did using an AI tool change about how you approached this project,
  compared to how you'd normally build something from scratch?

*[Write 2-4 paragraphs here based on your actual experience running and
testing the project yourself.]*
