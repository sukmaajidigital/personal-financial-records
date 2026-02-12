# Security Policy

## Supported Versions

We actively support the following versions with security updates:

| Version              | Supported |
| -------------------- | --------- |
| Latest (main branch) | ✅ Yes    |
| Older releases       | ❌ No     |

We recommend always running the latest version of Personal Financial Records to ensure you have the most recent security patches.

---

## Reporting a Vulnerability

We take the security of Personal Financial Records seriously. If you discover a security vulnerability, please report it responsibly.

### ⚠️ Do NOT

- **Do not** open a public GitHub issue for security vulnerabilities
- **Do not** disclose the vulnerability publicly before it has been addressed
- **Do not** exploit the vulnerability beyond what is necessary to demonstrate the issue

### How to Report

1. **Email**: Send a detailed report to the project maintainer via email (check the repository owner's profile for contact information)
2. **GitHub Security Advisories**: Use [GitHub's private vulnerability reporting](https://docs.github.com/en/code-security/security-advisories/guidance-on-reporting-and-writing-information-about-vulnerabilities/privately-reporting-a-security-vulnerability) feature on this repository

### What to Include

Please provide as much detail as possible to help us understand and reproduce the issue:

- **Description** of the vulnerability
- **Type** of vulnerability (e.g., SQL injection, XSS, CSRF, authentication bypass, authorization flaw, information disclosure)
- **Affected components** (e.g., specific route, controller, model, middleware)
- **Steps to reproduce** the vulnerability
- **Proof of concept** (code, screenshots, or HTTP requests/responses)
- **Impact assessment** — what an attacker could achieve
- **Suggested fix** (if you have one)

### Response Timeline

| Stage                     | Expected Timeline                            |
| ------------------------- | -------------------------------------------- |
| Acknowledgment of report  | Within **48 hours**                          |
| Initial assessment        | Within **1 week**                            |
| Fix development & testing | Within **2-4 weeks** (depending on severity) |
| Patch release             | As soon as the fix is verified               |
| Public disclosure         | After the fix is released                    |

### Recognition

We appreciate security researchers who report vulnerabilities responsibly. With your permission, we will:

- Credit you in the security advisory
- Acknowledge your contribution in the project changelog

---

## Security Measures

This project implements the following security measures:

### Authentication & Authorization

- **Laravel Fortify** handles authentication with industry-standard practices
- **Two-Factor Authentication (2FA)** via TOTP with recovery codes
- **Password hashing** using bcrypt (via Laravel's `Hash` facade)
- **Strong password requirements** in production:
    - Minimum 12 characters
    - Mixed case (uppercase and lowercase)
    - At least one number
    - At least one symbol
    - Checked against known compromised password databases (Have I Been Pwned)
- **Email verification** required for accessing protected features
- **Password confirmation** required for sensitive operations (account deletion, password changes)
- **Rate limiting** on sensitive endpoints (e.g., password update: 6 attempts per minute)

### Data Protection

- **Multi-tenancy isolation** — Users can only access their own data via Eloquent scoping and authorization policies
- **Authorization policies** (`CategoryPolicy`, `TransactionPolicy`) enforce resource ownership
- **Mass assignment protection** — Models explicitly define `$fillable` attributes
- **Sensitive data hidden** — Password, 2FA secret, and recovery codes are excluded from serialization via `$hidden`
- **CSRF protection** — Enabled by default on all state-changing routes via Laravel middleware
- **SQL injection prevention** — Eloquent ORM with parameterized queries; raw queries use parameter binding
- **XSS prevention** — Vue.js automatically escapes output; Inertia.js handles data serialization safely

### Infrastructure

- **Environment variables** for sensitive configuration (database credentials, API keys, mail settings)
- **Application key encryption** — All encrypted values use the `APP_KEY`
- **Destructive database commands prohibited** in production (`DB::prohibitDestructiveCommands()`)
- **HTTPS enforcement** — Recommended for all production deployments
- **Secure session configuration** — Configure `SESSION_SECURE_COOKIE=true` and `SESSION_SAME_SITE=lax` in production
- **Content Security Policy** — Recommended to configure via web server or middleware

### Dependencies

- **Composer audit** — Run `composer audit` regularly to check for known vulnerabilities in PHP dependencies
- **npm audit** — Run `npm audit` regularly to check for known vulnerabilities in Node.js dependencies
- **Dependency updates** — We strive to keep all dependencies up to date with security patches

---

## Security Best Practices for Deployment

When deploying Personal Financial Records to production, follow these best practices:

### Environment Configuration

```dotenv
APP_ENV=production
APP_DEBUG=false

# Use HTTPS
APP_URL=https://your-domain.com

# Secure session cookies
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
SESSION_LIFETIME=120

# Strong database credentials
DB_PASSWORD=use-a-strong-unique-password
```

### Server Configuration

1. **Use HTTPS** — Obtain and configure an SSL/TLS certificate (e.g., via Let's Encrypt)
2. **Set proper file permissions** — Web server should not have write access to application code
3. **Disable directory listing** — Ensure the web server does not expose directory contents
4. **Configure firewalls** — Only expose necessary ports (80, 443)
5. **Enable HTTP security headers:**
    - `Strict-Transport-Security` (HSTS)
    - `X-Content-Type-Options: nosniff`
    - `X-Frame-Options: DENY` or `SAMEORIGIN`
    - `X-XSS-Protection: 1; mode=block`
    - `Referrer-Policy: strict-origin-when-cross-origin`
    - `Content-Security-Policy` with appropriate directives

### Application Hardening

```bash
# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache

# Ensure debug mode is off
# APP_DEBUG=false in .env

# Run security audits
composer audit
npm audit
```

### Regular Maintenance

- **Update dependencies** regularly (`composer update`, `npm update`)
- **Monitor logs** (`storage/logs/laravel.log`) for suspicious activity
- **Rotate application key** if you suspect it has been compromised (re-encrypt existing data first)
- **Back up your database** regularly
- **Review access logs** periodically for unusual patterns
- **Run automated security scans** as part of your CI/CD pipeline

---

## Known Security Considerations

### SQLite (Development Only)

The default development configuration uses SQLite. **Do not use SQLite in production.** Use MySQL, MariaDB, or PostgreSQL with proper access controls.

### Two-Factor Authentication

- 2FA secrets are stored encrypted in the database
- Recovery codes are single-use and regeneratable
- Users should be encouraged to enable 2FA for additional account security

### File Uploads

This application currently does not handle file uploads. If file upload functionality is added in the future, ensure:

- File type validation (MIME type and extension)
- File size limits
- Storage outside the public directory
- Virus/malware scanning

---

## Changelog

Security-related changes are documented in the project's release notes and commit history. Significant security fixes will be noted with `[SECURITY]` prefix.

---

## Contact

For security-related inquiries, please use the reporting methods described above. For general questions, see the [Contributing Guidelines](CONTRIBUTING.md) or open a GitHub issue.

---

Thank you for helping keep Personal Financial Records secure! 🔒
