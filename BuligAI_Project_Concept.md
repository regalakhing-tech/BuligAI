# BuligAI: An Open-Source, AI-Powered Community Information-Sharing and Government Service Navigation Platform for Antique
**A Community-Powered Government Service Guide and Interactive Map for Antique**

*Tagline: “Bulig sang komunidad, gabay sang teknolohiya.”*

**Meaning of the Name:** *BuligAI* combines **Bulig**, meaning help or assistance in Kinaray-a, with **AI**, meaning Artificial Intelligence. The name represents Antiqueños helping one another through community knowledge, supported by accessible AI technology.

## 1. The Problem

Many residents of Antique experience difficulties accessing government services because they do not know where to go, what requirements to prepare, or how the application process works.

For example, someone applying for Assistance to Individuals in Crisis Situation (AICS) may not know which office to visit, where it is located, or which documents to bring.

Although some information is available online, it may be scattered across Facebook posts, government announcements, and different websites.

Sometimes, residents rely on asking friends or other people who have already completed the process.

Unfortunately, not everyone has someone who can guide them.

**The main problem is the lack of a centralized, community-accessible platform where Antiqueños can share and discover practical information about government services, locations, requirements, and application experiences.**

## 2. The Proposed Solution

BuligAI is an open-source, AI-assisted community information platform designed to help residents of Antique discover and understand government services.

Unlike traditional government information systems, BuligAI does not require government partnerships, government personnel accounts, or government database integrations.

Instead, the platform uses a **community-driven approach**.

Residents can voluntarily contribute useful information about government services, including application experiences, required documents, service locations, and practical tips.

For example, a resident who successfully completed an AICS application can share:

- The name of the government service
- Where they submitted their application
- The requirements they were asked to provide
- The steps they personally followed
- Their experience and helpful tips
- A map pin showing the public office location
- A public reference link, when available

Other residents can view the information, ask questions, suggest corrections, or contribute updated details.

An AI assistant helps users find and understand community-contributed information in simple language.

**The goal is to create a digital community where Antiqueños help fellow Antiqueños navigate government services.**

## 3. Main Features

### A. Community Government Service Posts

Residents can create informative posts about their government service experiences.

Each post can include a title, municipality, government service category, step-by-step guide, requirements, images, and helpful tips.

Other users can comment, ask questions, suggest corrections, and share their experiences.

This makes BuligAI function like a focused community forum for government service information.

### B. Community-Powered Interactive Map

Residents can voluntarily add public government service locations to an interactive map.

For example, a contributor can create a map entry named:

**AICS Assistance – Community-Reported Service Location**

The contributor can provide the location, office name, address, service description, and directions.

When someone searches for AICS, the platform can display community-submitted locations on a map.

Users can open directions through a supported mapping service.

Map entries are labeled according to their verification status, ensuring users can distinguish community-submitted information from confirmed locations.

### C. BuligAI Assistant

An AI assistant helps residents understand information posted by the community.

Residents can ask questions such as:

- "Saan pwede mag-apply ng AICS sa Antique?"
- "Ano ang requirements sa barangay clearance?"
- "Paano kumuha ng business permit?"
- "Saan located ang opisina?"
- "May nakapag-apply na ba dito?"
- "Ano ang dapat kong dalhin?"

The AI searches available community contributions and source references, then explains the relevant information.

It also provides links to the original contributions so users can check the details.

When reliable information is unavailable, the AI clearly communicates that the information has not been confirmed.

### D. Community Questions and Answers

Residents can post questions if they are unfamiliar with a government process.

Other community members can answer based on their actual experiences.

For example:

**Question:** "Sino na po nakapag-apply ng AICS sa San Jose? Ano pong requirements?"

**Community response:** "Ito po ang mga dokumentong hinanap noong nag-apply ako. Mas mabuting i-confirm din sa office dahil posibleng nagbago na."

Helpful responses can receive community votes and be highlighted for other users.

### E. Smart Requirements Checklist

Users can create checklists based on available community guides.

For example, an AICS guide may include a checklist of documents reported by contributors.

Residents can mark each item as prepared or incomplete.

The system reminds users that community checklists are informational and must be confirmed with the appropriate government office.

### F. Community Verification and Reporting

To reduce misinformation, users can:

- Suggest edits or corrections
- Report incorrect or misleading posts
- Submit public official references
- Mark information as potentially outdated
- Review when a contribution was last updated

Entries can be classified as community-submitted, community-reviewed, or official-source-supported.

A reviewed contribution does not automatically mean it has been officially approved by a government agency.

### G. Open-Source Contributions

The project source code will be publicly available through GitHub.

Developers, students, designers, and other volunteers can help improve the platform by contributing code, reporting bugs, suggesting features, and improving translations.

Community members do not need programming skills to contribute information through the website.

### H. Municipality-Based Discovery

Residents can explore information by location, including:

San Jose de Buenavista, Sibalom, Hamtic, San Remigio, Culasi, Pandan, and other municipalities of Antique.

Each municipality can have community-submitted government service guides, mapped public offices, questions, and useful discussions.

## 4. Real-Life Example: Finding AICS Assistance

Imagine someone from Sibalom who wants information about AICS financial assistance but does not know where to apply.

**Without BuligAI**

The resident might ask on Facebook, message several people, or travel to an office just to inquire about application procedures.

**With BuligAI**

1. The resident opens the website and searches for "AICS".
2. The system displays relevant community posts and public office map pins.
3. The resident selects a location and checks available directions.
4. They read community-contributed experiences and available requirements.
5. They generate a checklist to prepare their documents.
6. They confirm important details with the appropriate office before traveling.

After completing their own application, the resident can contribute updated information to help future applicants.

This creates a continuous cycle of community knowledge sharing.

## 5. How the Platform Operates Independently

BuligAI has one public community platform rather than separate citizen and government portals.

The platform owner maintains basic safety and moderation tools, but no government representative is required to operate the system.

Information comes from voluntary community contributions and publicly accessible reference materials.

Government service applications, approvals, and official transactions continue to take place through the proper government offices.

BuligAI does not collect government application records, approve assistance, or claim to represent government agencies.

To protect contributors, the platform focuses on public office locations rather than private applicant addresses and discourages uploading sensitive personal documents.

## 6. Proposed Technology

| Technology | Purpose |
|---|---|
| PHP 8.5 (latest stable series) + Composer 2 | Modular backend, PSR-4 autoloading, and application logic |
| Supabase | Authentication, PostgreSQL database, and private file storage |
| PHPMailer 7 | Application email notifications through authenticated SMTP with TLS |
| Google Gemini API | AI-powered question answering and information summaries |
| HTML, CSS, JavaScript | User interface |
| Tailwind CSS | Responsive design |
| Leaflet + OpenStreetMap | Community-contributed location map |
| GitHub | Open-source collaboration |
| GitHub Actions | Automated code checks, HTTP/session tests, policy tests, and optional deployment |
| Vercel + community PHP 8.5 runtime | Serverless PHP hosting with Supabase-backed data and files |

## 7. Initial Development Scope

The first version will focus on five essential functions:

1. Community registration and login
2. Government service guides and community posts
3. Interactive map with community-submitted public office locations
4. AI assistant grounded in available contributions
5. Community reporting, corrections, and basic moderation

Advanced features such as transportation fare estimates, document scanning, reputation systems, and offline access can be introduced in later versions.

## 8. Expected Benefits

BuligAI aims to make government service information easier to discover by bringing together local experiences and practical guides on a single platform.

Residents can help one another prepare for government transactions, discover service locations, and understand procedures.

The platform also encourages volunteerism, digital collaboration, community participation, and open-source software development in Antique.

Its usefulness and accuracy will depend on active contributors, responsible moderation, and regular verification of changing government requirements.

## 9. Conclusion

**BuligAI is not another government system. It is a community-built information platform for the people of Antique.**

By combining Artificial Intelligence, interactive mapping, community discussions, and open-source collaboration, the proposed platform gives residents a place to share what they know and learn from one another.

It aims to reduce confusion, improve access to practical information, and build a stronger culture of digital bayanihan.

**BuligAI — Built by the community. Powered by shared knowledge. Made for Antiqueños.**

## 10. Module-Based Project and File Management

The implementation uses a modular PHP application. Keep the files for each
business feature together under `app/Modules/<ModuleName>/`:

| Folder | Files to place here |
|---|---|
| `Controllers/` | HTTP request handlers that call the module's services |
| `Services/` | Business rules and feature workflows |
| `Repositories/` | Supabase queries and persistence code |
| `Requests/` | Reusable input validation and normalization |
| `Views/` | Escaped PHP/HTML page templates |

The modules are **Auth, Files, Notifications, Guides, Community, Locations,
Assistant, Questions, Checklists, Moderation, and Municipalities**. Shared HTTP,
security, Supabase transport, and mail infrastructure belongs in `app/Shared/`.

Browser assets belong in `public/assets/`, route definitions in `routes/`,
environment configuration in `config/`, database changes in
`supabase/migrations/`, tests in `tests/` and `supabase/tests/`, and developer
documentation in `docs/`. Only `public/` is exposed by the web server.

See the [complete file placement guide](docs/FILE_MANAGEMENT.md) for naming
conventions, module ownership, and examples of where to add new files.

## 11. Private File Upload Feature

The foundation includes a private file workspace. Existing Supabase users can
sign in, upload PDF/JPEG/PNG/WebP files (3 MiB on Vercel, 10 MiB locally), browse their files, download
them, and delete them. This supports public reference materials; BuligAI still
does not require or collect government application records.

Uploads are validated by actual MIME type, assigned safe randomized filenames,
and stored in a private Supabase bucket under the authenticated user's UUID.
Supabase Row Level Security policies and PHP ownership checks restrict access.
Persistent uploads are never placed in the public web directory or committed to Git.

## 12. Email and CI/CD

PHPMailer provides the application's SMTP email service. Credentials are read
from environment variables. Supabase Auth's verification and recovery emails use
Supabase's separate SMTP configuration.

GitHub Actions validates Composer configuration, checks PHP syntax and PSR-12
formatting, runs PHPUnit and Supabase policy tests, audits dependencies, and
smoke-tests the HTTP entry point with local and serverless session drivers.
After all checks pass, an enabled deployment job can build and publish the
application to Vercel using its community PHP 8.5 runtime.
Runtime credentials and deployment tokens are stored outside source control.

Production session state uses authenticated encryption in HttpOnly cookies, with
a server-only `APP_KEY`, so it does not depend on a persistent local filesystem.
Downloads redirect to signed Supabase URLs valid for 60 seconds. The Vercel
upload cap accounts for the platform's request-size limit. Docker is used only
for disposable local Supabase policy tests, not for hosting the application.

## 13. Implementation Status and Setup

The runnable foundation currently implements sign-in, token refresh, sign-out,
private file management, the SMTP sender, and CI/CD configuration. The other
community-platform modules have prepared folders and ownership documentation;
their application features remain part of the development roadmap.

Start with [README.md](README.md), then follow [Supabase setup](docs/SUPABASE.md)
and the [deployment guide](docs/DEPLOYMENT.md). Configure a Supabase project and
apply the storage migration before using uploads. A GitHub repository and
Vercel credentials and a session encryption key are required to activate hosted CI/CD.
