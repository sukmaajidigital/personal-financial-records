# Contributing to Personal Financial Records

Thank you for your interest in contributing to **Personal Financial Records**! This document provides guidelines and instructions for contributing to the project. We appreciate every contribution, whether it's a bug report, feature request, documentation improvement, or code change.

---

## Table of Contents

- [Code of Conduct](#code-of-conduct)
- [Getting Started](#getting-started)
- [How to Contribute](#how-to-contribute)
    - [Reporting Bugs](#reporting-bugs)
    - [Suggesting Features](#suggesting-features)
    - [Submitting Code Changes](#submitting-code-changes)
- [Development Setup](#development-setup)
- [Development Workflow](#development-workflow)
- [Coding Standards](#coding-standards)
    - [PHP / Laravel](#php--laravel)
    - [TypeScript / Vue](#typescript--vue)
    - [CSS / Tailwind](#css--tailwind)
- [Commit Message Guidelines](#commit-message-guidelines)
- [Pull Request Process](#pull-request-process)
- [Architecture Guidelines](#architecture-guidelines)
- [Testing Guidelines](#testing-guidelines)
- [Do Not Edit (Auto-Generated)](#do-not-edit-auto-generated)
- [Need Help?](#need-help)

---

## Code of Conduct

By participating in this project, you agree to maintain a respectful and inclusive environment. Please:

- Be respectful and considerate in all interactions
- Welcome newcomers and help them get oriented
- Focus on constructive feedback
- Accept differing viewpoints gracefully
- Report any unacceptable behavior to the project maintainers

---

## Getting Started

1. **Fork** the repository on GitHub
2. **Clone** your fork locally:
    ```bash
    git clone https://github.com/your-username/personal-financial-records.git
    cd personal-financial-records
    ```
3. **Add the upstream remote:**
    ```bash
    git remote add upstream https://github.com/original-owner/personal-financial-records.git
    ```
4. **Set up the development environment** (see [Development Setup](#development-setup))

---

## How to Contribute

### Reporting Bugs

Before submitting a bug report, please:

1. **Search existing issues** to avoid duplicates
2. **Use the latest version** to confirm the bug still exists
3. **Provide a clear description** including:
    - Steps to reproduce the behavior
    - Expected behavior vs actual behavior
    - Screenshots or screen recordings (if applicable)
    - Environment details (OS, PHP version, Node.js version, browser)
    - Error messages or stack traces (from `storage/logs/laravel.log` or browser console)

**Bug report template:**

```markdown
## Bug Description

A clear description of the bug.

## Steps to Reproduce

1. Go to '...'
2. Click on '...'
3. Fill in '...'
4. See error

## Expected Behavior

What you expected to happen.

## Actual Behavior

What actually happened.

## Screenshots

If applicable, add screenshots.

## Environment

- OS: [e.g., Windows 11, macOS 14, Ubuntu 24.04]
- PHP: [e.g., 8.2.15]
- Node.js: [e.g., 20.11.0]
- Browser: [e.g., Chrome 121]
- Database: [e.g., MySQL 8.0]
```

### Suggesting Features

Feature suggestions are welcome! Please:

1. **Search existing issues** and discussions for similar ideas
2. **Open an issue** with the "Feature Request" label
3. **Describe the feature** clearly:
    - What problem does it solve?
    - Who would benefit from this feature?
    - How should it work (user perspective)?
    - Any alternative solutions you've considered?

### Submitting Code Changes

1. Create a new branch from `main`:
    ```bash
    git checkout -b feature/your-feature-name
    # or
    git checkout -b fix/your-bug-fix
    ```
2. Make your changes following the [Coding Standards](#coding-standards)
3. Write or update tests for your changes
4. Ensure all tests pass
5. Submit a pull request

---

## Development Setup

### Prerequisites

- **PHP** >= 8.2 with required extensions (`mbstring`, `xml`, `ctype`, `json`, `bcmath`, `pdo`, `tokenizer`)
- **Composer** >= 2.x
- **Node.js** >= 20.x
- **npm** >= 10.x
- **MySQL** >= 8.0 (or MariaDB >= 10.6, SQLite for development)

### Quick Setup

```bash
# Install all dependencies and set up the project
composer setup

# Start the development server
composer dev
```

### Manual Setup

```bash
# Install PHP dependencies
composer install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your database in .env, then run migrations
php artisan migrate

# (Optional) Seed with sample data
php artisan db:seed

# Install frontend dependencies
npm install

# Start the development server
composer dev
```

The development server runs at `http://localhost:8000` with Vite HMR for instant feedback.

---

## Development Workflow

1. **Sync your fork** before starting work:

    ```bash
    git fetch upstream
    git checkout main
    git merge upstream/main
    ```

2. **Create a descriptive branch:**

    ```bash
    git checkout -b feature/add-budget-tracking
    git checkout -b fix/transaction-filter-date-range
    git checkout -b docs/update-api-documentation
    ```

3. **Make incremental commits** following the [Commit Message Guidelines](#commit-message-guidelines)

4. **Run checks before pushing:**

    ```bash
    # Run PHP linter
    composer lint

    # Run frontend linter
    npm run lint

    # Format frontend code
    npm run format

    # Run all tests
    composer test
    ```

5. **Push and create a pull request:**
    ```bash
    git push origin feature/your-feature-name
    ```

---

## Coding Standards

### PHP / Laravel

- **PHP 8.2+** features: Use typed properties, enums, `match`, named arguments, and `readonly` where appropriate.
- **PSR-12** coding style enforced by [Laravel Pint](https://laravel.com/docs/pint) with the Laravel preset.
- **Form Request** classes for all validation logic (in `app/Http/Requests/`).
- **Eloquent** relationships and scopes; avoid raw queries unless necessary for performance.
- **CarbonImmutable** for all date handling.
- **Action classes** for complex business logic (single-responsibility, invokable).
- **Policies** for authorization logic (in `app/Policies/`).

```bash
# Auto-fix PHP code style
composer lint

# Check without fixing
composer test:lint
```

**Example controller method:**

```php
public function store(TransactionRequest $request): RedirectResponse
{
    $request->user()->transactions()->create($request->validated());

    return to_route('transactions.index')
        ->with('success', 'Transaction created successfully.');
}
```

### TypeScript / Vue

- **Vue 3 Composition API** with `<script setup lang="ts">`.
- **TypeScript** everywhere — avoid `any` unless absolutely necessary.
- **Consistent type imports**: `import type { Foo } from '...'` (enforced by ESLint).
- **Import ordering**: builtin → external → internal → parent → sibling → index (alphabetized).
- **Props** defined with `defineProps<Props>()` using TypeScript generics.
- **`cn()`** from `@/lib/utils` for conditional class merging.
- **Icons** from `lucide-vue-next`.
- **Wayfinder route helpers** for type-safe routing (never hardcode URLs).

```bash
# Auto-fix JS/Vue lint issues
npm run lint

# Format with Prettier
npm run format
```

**Example Vue component:**

```vue
<script setup lang="ts">
import type { Category } from '@/types';

import { useForm } from '@inertiajs/vue3';
import { Plus } from 'lucide-vue-next';

import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';

const props = defineProps<{
    categories: Category[];
}>();

const form = useForm({
    name: '',
    color: '#3b82f6',
});
</script>
```

### CSS / Tailwind

- **Tailwind CSS v4** configured via CSS (`resources/css/app.css`), not a JS config file.
- Use **CSS custom properties** for theming (light/dark mode).
- Use **Tailwind utility classes** instead of custom CSS.
- Follow the existing design system and color variables.

---

## Commit Message Guidelines

We follow the [Conventional Commits](https://www.conventionalcommits.org/) specification:

```
<type>(<scope>): <subject>

[optional body]

[optional footer(s)]
```

### Types

| Type       | Description                                      |
| ---------- | ------------------------------------------------ |
| `feat`     | A new feature                                    |
| `fix`      | A bug fix                                        |
| `docs`     | Documentation changes                            |
| `style`    | Code style changes (formatting, no logic change) |
| `refactor` | Code refactoring (no feature or fix)             |
| `perf`     | Performance improvement                          |
| `test`     | Adding or updating tests                         |
| `build`    | Build system or dependency changes               |
| `ci`       | CI/CD configuration changes                      |
| `chore`    | Other changes (tooling, configs)                 |

### Scope Examples

`auth`, `dashboard`, `transactions`, `categories`, `settings`, `ui`, `api`, `db`, `deps`

### Examples

```
feat(transactions): add date range filter to transaction list
fix(dashboard): correct monthly trend calculation for empty months
docs: update installation instructions in README
style(categories): fix indentation in CategoryController
refactor(auth): extract password validation into a concern
test(transactions): add tests for transaction filtering
chore(deps): update laravel/framework to 12.1
```

### Rules

- Use the **imperative mood** in the subject line ("add feature" not "added feature")
- Do **not** capitalize the first letter of the subject
- Do **not** end the subject with a period
- Keep the subject line under **72 characters**
- Wrap the body at **72 characters**
- Use the body to explain **what** and **why**, not **how**

---

## Pull Request Process

### Before Submitting

- [ ] Code follows the project's coding standards
- [ ] All existing tests pass (`composer test`)
- [ ] New tests are added for new functionality
- [ ] PHP code style is clean (`composer lint`)
- [ ] Frontend code is linted (`npm run lint`)
- [ ] Frontend code is formatted (`npm run format`)
- [ ] Documentation is updated if needed
- [ ] Commit messages follow the guidelines

### PR Description Template

```markdown
## Description

A brief description of what this PR does.

## Type of Change

- [ ] Bug fix (non-breaking change that fixes an issue)
- [ ] New feature (non-breaking change that adds functionality)
- [ ] Breaking change (fix or feature that would cause existing functionality to change)
- [ ] Documentation update

## How Has This Been Tested?

Describe the tests you ran.

## Checklist

- [ ] My code follows the project's coding standards
- [ ] I have added tests that prove my fix/feature works
- [ ] All new and existing tests pass
- [ ] I have updated the documentation accordingly
```

### Review Process

1. At least **one maintainer** will review your PR
2. Reviewers may request changes — please address all feedback
3. Once approved, a maintainer will merge the PR
4. PRs are typically merged via **squash and merge** to keep a clean history

---

## Architecture Guidelines

### Adding a New Feature

Follow this checklist when adding new functionality:

1. **Model** — Create in `app/Models/` with:
    - A factory in `database/factories/`
    - A migration in `database/migrations/`
    - Proper relationships, casts, and fillable attributes

2. **Policy** — Create in `app/Policies/` for authorization rules

3. **Controller** — Create in `app/Http/Controllers/` (or subdirectory):
    - Use Form Request classes for validation
    - Authorize actions via policies
    - Return Inertia responses for pages

4. **Form Request** — Create in `app/Http/Requests/` for validation logic

5. **Routes** — Add to `routes/web.php` or a dedicated route file:
    - Wayfinder will auto-generate frontend route helpers after rebuild

6. **Vue Page** — Create in `resources/js/pages/` matching the Inertia render name

7. **Components** — Reusable components go in `resources/js/components/`:
    - Use shadcn-vue UI primitives from `components/ui/`

8. **Types** — Add TypeScript interfaces/types in `resources/js/types/`

9. **Tests** — Write Pest tests in `tests/Feature/` or `tests/Unit/`

### File Naming Conventions

| Type             | Convention                | Example                                           |
| ---------------- | ------------------------- | ------------------------------------------------- |
| PHP Classes      | PascalCase                | `TransactionController.php`                       |
| Migrations       | snake_case with timestamp | `2026_02_12_170615_create_transactions_table.php` |
| Vue Components   | PascalCase                | `AppSidebar.vue`, `Dashboard.vue`                 |
| TypeScript Files | camelCase                 | `useAppearance.ts`, `utils.ts`                    |
| CSS Files        | kebab-case                | `app.css`                                         |

---

## Testing Guidelines

### Framework

- **Pest 4** with `pestphp/pest-plugin-laravel`
- Tests use **SQLite in-memory** database (`:memory:`)

### Writing Tests

- Use **Pest syntax** (`it()`, `test()`, `expect()`), not PHPUnit class-based syntax
- Use `RefreshDatabase` trait for database tests
- Use model factories for test data
- Test both happy paths and edge cases
- Test authorization (ensure users can only access their own resources)

**Example test:**

```php
use App\Models\User;
use App\Models\Category;

it('can create a category', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->post('/categories', [
            'name' => 'Groceries',
            'color' => '#22c55e',
        ]);

    $response->assertRedirect('/categories');

    $this->assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'Groceries',
        'color' => '#22c55e',
    ]);
});

it('cannot access another user\'s category', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $category = Category::factory()->for($otherUser)->create();

    $this->actingAs($user)
        ->put("/categories/{$category->id}", ['name' => 'Hacked'])
        ->assertForbidden();
});
```

### Running Tests

```bash
# All tests with lint checks
composer test

# Tests only
php artisan test

# Specific suite
php artisan test --testsuite=Feature

# With coverage
php artisan test --coverage

# Specific file
php artisan test tests/Feature/DashboardTest.php
```

---

## Do Not Edit (Auto-Generated)

The following files and directories are auto-generated. Do not edit them manually:

| Path                          | Generator                   |
| ----------------------------- | --------------------------- |
| `resources/js/wayfinder/`     | Wayfinder Vite plugin       |
| `resources/js/actions/`       | Wayfinder action helpers    |
| `resources/js/routes/`        | Wayfinder route definitions |
| `resources/js/components/ui/` | shadcn-vue CLI              |
| `vendor/`                     | Composer                    |
| `node_modules/`               | npm                         |
| `public/build/`               | Vite                        |

To update shadcn-vue components, use:

```bash
npx shadcn-vue@latest add <component-name>
```

---

## Need Help?

- **Open an issue** on GitHub for bugs or feature requests
- **Start a discussion** for questions or ideas
- Check the [README](README.md) for setup and usage instructions
- Review the [Laravel documentation](https://laravel.com/docs)
- Review the [Vue 3 documentation](https://vuejs.org/guide/introduction.html)
- Review the [Inertia.js documentation](https://inertiajs.com/)

---

Thank you for contributing! Every contribution makes this project better. 🎉
