# EPESI CRM 3.0 Module Developer Guide (Windows)

A practical start-to-finish setup for developing, running, and packaging a module for EPESI CRM 3.0 on Windows 10 or 11.

> **EPESI 3.0 is Laravel 12 + Filament 5.** Its module system lives under `modules/<Vendor>/<Name>/`. Use the current EPESI 3.0 repository and its README as the source of truth when commands or framework dependencies change.

## What you will install

- Windows 10 or 11, 64-bit
- A GitHub account
- [PHP Windows DevStack](https://github.com/jtylek/PHP-windows-devstack) for PHP 8.5, Apache, MariaDB and the local development database
- [Git for Windows](https://git-scm.com/download/win)
- [Visual Studio Code](https://code.visualstudio.com/Download) (the free editor; this is not the full Visual Studio IDE)
- [Composer](https://getcomposer.org/download/) installed separately
- [Node.js LTS](https://nodejs.org/en/download/) with npm (the EPESI frontend build needs it)
- Optional AI coding agents: GitHub Copilot, Claude Code, and OpenAI Codex
- Optional [ScreenCapture](https://github.com/jtylek/ScreenCapture) for annotated screenshots to share with an AI agent

The DevStack README describes its current runtime as PHP 8.5.12, Apache and MariaDB, with Apache at `http://127.0.0.1:8080` and MariaDB at `127.0.0.1:3307` by default. It is a development stack, not a production server. Review its current release notes and security/network guidance before installing or changing listener settings.

## 1. Install and verify the Windows development stack

1. Download and run the latest installer from the [PHP Windows DevStack Releases](https://github.com/jtylek/PHP-windows-devstack/releases). The project README currently lists `C:\PHPDevStack` as the default folder. Follow the installer prompts; the release is unsigned, so Windows may show a SmartScreen unknown-publisher prompt.
2. Start PHP Windows DevStack from the Start menu. Start Apache and MariaDB in the control panel.
3. Open phpMyAdmin at <http://127.0.0.1:8080/phpmyadmin>. Use the database password shown by the control panel or the DevStack instructions. Create a database for local EPESI development, for example `epesi_dev`.
4. Open the control panel's **Open PowerShell** button. This is the preferred terminal for PHP commands: it selects the DevStack PHP executable and generated `php.ini` for this terminal and its child processes.
5. Verify PHP and required extensions:

   ```powershell
   php --ini
   php -v
   php -m
   ```

   Keep this terminal open for Composer and Artisan commands. If you open a separate PowerShell window, it may use a different PHP installation. Reopen the DevStack PowerShell after changing PHP extension settings.

## 2. Install Composer separately

Install Composer with the official Windows installer from [getcomposer.org/download](https://getcomposer.org/download/). When prompted for the PHP executable, select the DevStack PHP executable, normally:

```text
C:\PHPDevStack\runtime\php\php.exe
```

If you selected a different DevStack folder, use its corresponding `runtime\php\php.exe`. Close and reopen PowerShell after the Composer installer completes, then check from the DevStack PowerShell:

```powershell
php -v
composer --version
composer diagnose
```

If Composer reports a different PHP version, rerun the Composer installer and point it to DevStack's `php.exe`. Confirm that HTTPS works from PHP; DevStack supplies a CA bundle for PHP cURL/OpenSSL in its current setup.

## 3. Install Git, VS Code, and Node.js

1. Install Git for Windows with its standard options. Open a new PowerShell and configure the commit name/email you use on GitHub:

   ```powershell
   git config --global user.name "Your Name"
   git config --global user.email "you@example.com"
   git --version
   ```

2. Install the free [Visual Studio Code User Installer](https://code.visualstudio.com/Download). Recommended extensions: **PHP Intelephense**, **Laravel Extra Intellisense** (optional), **EditorConfig** (optional), and the GitHub Copilot extension if you plan to use Copilot. Extensions are optional; do not install duplicate PHP formatters without deciding which one is authoritative.
3. Install the current Node.js LTS Windows x64 installer from [nodejs.org](https://nodejs.org/en/download/). npm comes with Node.js. Verify from a new terminal:

   ```powershell
   node --version
   npm --version
   ```

## 4. Set up your GitHub account and get the project

1. Create or sign in to your [GitHub account](https://github.com/). Enable two-factor authentication and add an SSH key or use Git Credential Manager when Git prompts you to authenticate.
2. In the DevStack PowerShell, clone the EPESI 3.0 repository into the DevStack web folder:

   ```powershell
   cd C:\PHPDevStack\www
   git clone https://github.com/jtylek/EpesiCRM.git epesi
   cd .\epesi
   code .
   ```

   If DevStack is installed elsewhere, substitute its `www` directory. You can also clone into another development folder and run Laravel's local server; keeping the project below `www` makes the DevStack Apache/phpMyAdmin environment convenient.
3. Keep your work on a feature branch. Do not commit local secrets, `.env`, database dumps, or generated dependencies:

   ```powershell
   git switch -c feature/my-module
   git status
   ```

## 5. Install EPESI dependencies and run it

In the DevStack PowerShell, from the cloned repository:

```powershell
composer install
npm install
npm run build
```

For a local development database with demo content, the repository README gives this path:

```powershell
php artisan migrate --seed
composer run dev
```

`composer run dev` starts the app's development processes, including the local web server and Vite. Open the address printed in the terminal. The README currently documents demo logins `admin@example.com`, `manager@example.com`, and `employee@example.com`, all with password `password`; use these only in a local throwaway development database and never on an internet-accessible instance.

For an empty database / guided installation, use:

```powershell
php artisan epesi:install
```

The installer checks the environment, creates `.env` and the application key, asks for database settings, and creates tables. It prints a setup code and browser address for completing setup. Follow the prompts and use the local DB name and credentials you created. Do not commit `.env`.

**If you choose Apache instead of Laravel's local server:** set Apache's document root to the project's `public` folder (not the repository root), and configure the rewrite rules and PHP handler per the DevStack and EPESI deployment docs. Never expose a development database or demo credentials to a public network.

## 6. Generate a module

The repository README documents these Artisan commands:

```powershell
php artisan make:epesi-module Acme/Invoices
php artisan make:epesi-recordset Acme/Invoices Invoice
php artisan module:register Acme/Invoices
```

Replace `Acme/Invoices` and `Invoice` with your vendor/module and record names. Run the first command from the EPESI repository root. Inspect the generated folder and files before editing; module scaffolding and command options can evolve, so confirm them with:

```powershell
php artisan list
php artisan help make:epesi-module
php artisan help make:epesi-recordset
```

The standard module location is:

```text
modules/<Vendor>/<ModuleName>/
```

A module has a `module.json` manifest and PSR-4 code. Modules can declare dependencies and be installed/enabled/disabled at runtime. Check the generated manifest and module's namespace before adding classes. Follow nearby module conventions, and keep migrations, authorization, navigation, translations, tests and assets inside the module where the current architecture supports them.

### Decide whether to use RecordBrowser

Use **RecordBrowser** when the feature manages a business record collection (for example invoices, products, or service cases). Extend `RecordsetResource` and declare fields through the `Field` DSL. EPESI derives list, view, create/edit screens, filters, search, and history from those declarations. This also allows administrator-created custom fields to use the same record path.

Use **Filament directly** for a workflow or page that is not a conventional record lifecycle, such as settings, reports, dashboards, a one-off tool, or a specialized interaction. Filament is the UI framework; RecordBrowser adds EPESI's record conventions and reusable record handling.

The README shows this example field declaration:

```php
public static function fields(): array
{
    return [
        Field::text('company_name')->required()->maxLength(128)->inTable(),
        Field::commonData('groups', 'Companies_Groups', multiple: true)->filterable(),
        Field::longText('memo')->notInTable(),
    ];
}
```

For a working project example, inspect [`modules/Epesi/CRM/Companies`](https://github.com/jtylek/EpesiCRM/tree/main/modules/Epesi/CRM/Companies) and the neighboring modules that best match your use case. Read the repository's `PORTING.md` and `INSTALL.md` when you need details about compatibility or deployment.

## 7. Build, check, and package your module

1. Implement the module in small increments. Keep changes isolated to the feature branch.
2. Run the record declaration checker:

   ```powershell
   php artisan recordset:check --strict
   ```

3. Run tests and formatting checks before sharing your work:

   ```powershell
   php artisan test
   php vendor/bin/pint --dirty
   ```

   The repository README says the test suite uses in-memory SQLite so it does not touch the development database. Add tests for module behavior, permissions, validation, relationships, and any important edge cases.
4. Create an installable module archive:

   ```powershell
   php artisan module:package Acme/Invoices
   ```

   Inspect the command output for the archive location and verify the package contains the manifest, source, and required assets. Test installation into a clean local EPESI instance before distributing it.
5. Review your changes, commit, and push your feature branch:

   ```powershell
   git status
   git diff --check
   git add modules/Acme/Invoices
   git commit -m "Add Acme invoices module"
   git push -u origin feature/my-module
   ```

   Open a pull request on GitHub with purpose, setup steps, screenshots where useful, tests run, and any known limitations.

## 8. Use AI coding agents

AI agents can inspect and edit project files, run tests, and explain errors. Treat them as assistants: review every diff, never paste passwords or production data into prompts, and do not let an agent run destructive database commands or push/merge changes without your review. Give the agent the module goal, constraints, acceptance criteria, and exact verification commands.

### GitHub Copilot Free in VS Code

1. Sign in to VS Code with the GitHub account you created.
2. Install/enable the official **GitHub Copilot** extension and sign in when prompted. Select the Copilot icon or Chat view to open chat/agent features.
3. Start with Copilot Free. GitHub documents a limited monthly allowance; current plan details and model access are listed on [Copilot plans](https://github.com/features/copilot/plans). Allowances and model access can change.
4. Ask for focused work, for example: “Inspect this EPESI module scaffold. Explain the generated files and propose the smallest implementation plan. Do not edit files yet.” Then ask it to make one change at a time and review the diff.

### Upgrade to a paid Copilot plan

On your personal GitHub account, open [Copilot plans](https://github.com/features/copilot/plans), choose an individual paid plan, and complete checkout. Then in VS Code, sign out/in to GitHub if the entitlement does not appear. GitHub currently lists individual Copilot Pro, Pro+, and Max plans with different included AI Credit allowances and model/agent access. The plans page is authoritative for current prices, features, eligibility, and usage limits. Monitor usage and billing controls in GitHub Settings → Copilot; check whether your account is already covered by an employer or organization before purchasing a personal plan.

Copilot Business and Enterprise are organization/enterprise subscriptions administered by the organization; ask its GitHub administrator to assign a seat. Do not assume those plans are interchangeable with individual billing.

### Claude Code (Anthropic coding agent)

Claude Code is an optional separate agent; it is not included in Copilot Free. Anthropic documents Windows support through WSL or native Windows with Git for Windows/Git Bash, plus Node.js 18 or newer. Since Node.js is already installed above, the simplest native setup is:

1. Install Git for Windows and verify Git Bash is available.
2. In a normal PowerShell, install Claude Code using Anthropic's documented command:

   ```powershell
   npm install -g @anthropic-ai/claude-code
   ```

3. If Claude Code cannot find Git Bash, set the path documented by Anthropic (adjust if Git is installed elsewhere):

   ```powershell
   $env:CLAUDE_CODE_GIT_BASH_PATH = "C:\Program Files\Git\bin\bash.exe"
   ```

   To make it persistent, set the environment variable in Windows Environment Variables, then restart the terminal.
4. Change to the EPESI project and start the agent:

   ```powershell
   cd C:\PHPDevStack\www\epesi
   claude
   ```

5. Sign in using the authentication flow shown by Claude Code. A compatible Claude plan or Anthropic API billing is required; check Anthropic's current options and usage terms. Review the agent's proposed actions and diffs before accepting them.

Use Anthropic's current [Claude Code setup guide](https://docs.anthropic.com/en/docs/claude-code/getting-started) for exact Windows prerequisites, authentication choices, and updates.

### OpenAI Codex coding agent

Codex is also separate from Copilot. You can use Codex through the official VS Code integration where available, or install the Codex CLI and run it from the project terminal. With Node/npm installed, the current CLI install command is:

```powershell
npm install -g @openai/codex
codex --version
cd C:\PHPDevStack\www\epesi
codex
```

Follow the sign-in flow offered by Codex. The available sign-in and usage options depend on the current Codex product and account plan; check the [Codex documentation](https://developers.openai.com/codex/) before use. If using the VS Code extension, install the official OpenAI Codex extension from the VS Code Marketplace and authenticate with your ChatGPT account as prompted. Keep the agent in the EPESI project folder and review its changes before accepting them.

### Give an agent useful project context

A good first prompt:

> We are adding a module to EPESI CRM 3.0 (Laravel 12 + Filament 5). Read the root README, module manifest examples, and the closest existing module. Propose a plan for `<feature>` using the existing module/RecordBrowser conventions. Do not modify files yet. Include files to create, permissions, data model, UI approach, tests, and verification commands.

After agreeing on the plan:

> Implement only step 1 on my feature branch. Follow nearby module conventions. Do not change unrelated files, edit `.env`, run destructive database commands, or push/merge. Run the focused checks and summarize changed files and results.

## 9. Share annotated screenshots with an agent

Install [ScreenCapture](https://github.com/jtylek/ScreenCapture/releases) on Windows. It can capture a region or window, add arrows/text/blur/crop, and copy the result to the clipboard or save it. Capture the EPESI screen or error, blur personal/customer data and credentials, add a short annotation, then paste or attach it in the AI agent conversation. The tool is optional and does not automatically send anything to the agent.

## 10. Can GitHub test this on a Windows computer?

**Yes, for automated build and test checks.** Add a GitHub Actions workflow that runs on a GitHub-hosted Windows runner (`windows-latest` or a pinned Windows Server image). It can check out EPESI, install PHP/Composer/Node dependencies, run build commands, execute Artisan tests, and upload logs or package artifacts. Standard GitHub-hosted runners are short-lived virtual machines, not a desktop the developer can log into and operate interactively. The hosted Windows image is Windows Server, so it does not prove that the DevStack installer/control panel behaves identically on Windows 10/11.

A CI job is especially useful for repeatable application checks (Composer install, npm build, `php artisan test`, Pint and module packaging). Running a full DevStack GUI install and testing the user experience requires a real Windows 10/11 desktop or a self-hosted Windows 10/11 GitHub Actions runner. A self-hosted runner is maintained by you and should not run untrusted pull-request code with access to your personal machine or secrets.

For a public repository, standard GitHub-hosted Actions usage is currently free; private-repository usage depends on the account's included compute allowance and billing. Check GitHub's current [Actions billing](https://docs.github.com/en/actions/concepts/billing-and-usage) before enabling workflows. To add CI, create `.github/workflows/windows.yml` with a `windows-latest` job and commit it on a branch. The repository owner or maintainer must enable/run Actions for that repository. This guide does not itself launch a GitHub-hosted machine or validate the installer interactively.

## Quick troubleshooting

- **`php` or Composer uses the wrong version:** start PowerShell from the DevStack control panel, verify `php --ini` and `php -v`, and make Composer point at the DevStack PHP binary.
- **Composer cannot download packages over HTTPS:** verify DevStack's CA bundle configuration and network access; check `php --ini` and `php -m` before changing TLS verification settings. Do not disable certificate verification.
- **`npm` is not recognized:** install Node.js LTS, then open a new terminal and verify `node --version` and `npm --version`.
- **Database connection fails:** confirm MariaDB is running, use the DevStack host/port (`127.0.0.1:3307` by default), and recheck the database name and credentials in `.env` without sharing that file.
- **Module command is not found:** confirm you are in the EPESI repository root, run `composer install`, and inspect `php artisan list` for commands provided by the current branch.
- **The local site is not reachable in Apache:** confirm Apache is started and the URL/port matches DevStack. If serving via Apache, its document root must point to EPESI's `public` directory.

## Official references

- [EPESI CRM 3.0 repository and developer README](https://github.com/jtylek/EpesiCRM)
- [PHP Windows DevStack](https://github.com/jtylek/PHP-windows-devstack)
- [ScreenCapture](https://github.com/jtylek/ScreenCapture)
- [GitHub Copilot plans](https://github.com/features/copilot/plans)
- [GitHub Copilot plan documentation](https://docs.github.com/en/copilot/get-started/plans)
- [Anthropic Claude Code setup](https://docs.anthropic.com/en/docs/claude-code/getting-started)
- [OpenAI Codex documentation](https://developers.openai.com/codex/)
- [Composer for Windows](https://getcomposer.org/doc/00-intro.md#installation-windows)
- [Visual Studio Code download](https://code.visualstudio.com/Download)
- [Node.js download](https://nodejs.org/en/download/)
- [GitHub Actions hosted runners](https://docs.github.com/en/actions/reference/runners/github-hosted-runners)