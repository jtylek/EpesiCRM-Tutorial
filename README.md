# Epesi Developer Tutorial

A free sample tutorial module for Epesi CRM 3.0.

> **Setting up the development environment?** Before working on this tutorial, follow the [Windows Setup Guide](https://github.com/jtylek/EpesiCRM-Tutorial/blob/main/docs/WindowsSetupGuide.md) for the complete Windows 10/11 setup, including PHP Windows DevStack, Composer, VS Code, and AI coding agents.

## What it demonstrates

- Every RecordBrowser scalar and relationship field family: text, long text, integer, decimal,
  boolean, date, date and time, time, select, multiselect, CommonData, relation, multiple
  relations, customer, customers, currency, calculated, autonumber, file, related records, and
  collections.
- The built-in e-mail, URL and phone field variants.
- Default Address, E-mail, Phone and Online Account collections, plus a custom Checkpoint
  collection type.
- Shared List, View, Create and Edit pages, the History addon, custom policies, stable morph
  aliases, and a parent record's Tickets addon.
- Row ownership visibility on tickets and validation that a ticket always belongs to a visible
  project.

The examples are intentionally compact. Review the resource `fields()` methods first, then follow
each field to its model and migration. The `recordsets.json` catalogue is registered in the service
provider and can be extended safely by the recordset generator.

## Install the tutorial module

This tutorial module is distributed as source code. Clone it into the EPESI installation's
`modules/Custom/Tutorial` directory. The module requires Epesi Core 3.x, RecordBrowser,
CommonData, Currencies, Contacts and Companies; make sure these dependencies are installed first.

### Command-line installation

Open a terminal in the root directory of your running EPESI installation (the directory containing
`artisan`), then run:

```bash
git clone https://github.com/jtylek/EpesiCRM-Tutorial.git modules/Custom/Tutorial
php artisan module:check Custom/Tutorial
php artisan module:register Custom/Tutorial
php artisan migrate --path=modules/Custom/Tutorial/database/migrations
php artisan module:enable custom/tutorial
php artisan module:check Custom/Tutorial --schema
```

The first check reports missing dependencies or manifest problems. Registration adds the module
already present under `modules/` to this EPESI installation. The migration command creates the
tutorial's tables and CommonData lists. The enable command makes the module available in the main
panel. The final check validates the registered module's schema.

If the tutorial repository is already cloned elsewhere, copy or move its contents so that
`module.json` is at `modules/Custom/Tutorial/module.json`, then start with the first Artisan
command above. Do not clone it into a nested `modules/Custom/Tutorial/EpesiCRM-Tutorial` folder.

### Discover, register, and install it in the Administrator Control Panel

After cloning the tutorial into `modules/Custom/Tutorial`:

1. Sign in to EPESI with an administrator account.
2. Open **Administration → Modules**.
3. Click **Discover modules**.
4. Find **Epesi Developer Tutorial** (module ID: `custom/tutorial`) in the discovered modules and choose **Register**.
5. Return to the Modules list. Find the registered tutorial and choose **Install**. If EPESI prompts you to confirm or run the module setup, follow the prompts.
6. Confirm that the module is installed and enabled.
7. Open the main panel and look for the tutorial's Projects and Tickets pages.

If the tutorial does not appear after discovery, check that its manifest is located at
`modules/Custom/Tutorial/module.json` and that the required modules are installed.

The module's migrations create the CommonData lists it reads. An administrator can add options
to them in **Administration → Common Data**: `Tutorial_ProjectGroups` (project Groups: Internal,
Customer, Training), `Tutorial_ProjectCheckpointKinds` (checkpoint Kind: Milestone, Review,
Deadline), and `Tutorial_TicketImpact` (ticket Impact: Low, Medium, High). Currency entry uses
the configured Currencies module.

## How the module was created

This package was generated with Epesi's module and recordset scaffolding, then completed as a
teaching example:

```text
php artisan make:epesi-module Custom/Tutorial
php artisan make:epesi-recordset Custom/Tutorial TutorialProject
php artisan make:epesi-recordset Custom/Tutorial TutorialTicket --ownership
```

The generator supplied the manifest, service provider, Filament plugin, model, migration, policy,
RecordBrowser resource and shared page stubs. The example then added its domain fields, parent/child
relationship, custom collection item, migrations, and documentation. Run
`php artisan module:check Custom/Tutorial` before installing, inspect generated SQL with
`php artisan migrate --pretend --path=modules/Custom/Tutorial/database/migrations`, and run the
module tests explicitly with `php artisan test modules/Custom/Tutorial/tests`.

See the repository's `AI-shared/AI-agent-module-development.md` for the full agent-oriented
workflow and `AI-shared/Epesi-custom-fields.md` for the field and collection contracts.
