# Epesi Developer Tutorial

A free module available in Epesi Store that demonstrates how to build a RecordBrowser module. It contains
Projects and Tickets, with Tickets related to their parent Project, plus a project-owned Checkpoint
collection.

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

## Install for development

The module requires Epesi Core 3.x, RecordBrowser, CommonData, Currencies, Contacts and Companies.
Install those dependencies, then install this module through Administration → Modules or run:

```text
php artisan module:register Custom/Tutorial
php artisan migrate --path=modules/Custom/Tutorial/database/migrations
php artisan module:enable custom/tutorial
php artisan module:check Custom/Tutorial --schema
```

The module's migrations create the CommonData lists it reads, and an administrator can add options
to any of them in Administration → Common Data: `Tutorial_ProjectGroups` (a project's Groups:
Internal, Customer, Training), `Tutorial_ProjectCheckpointKinds` (a checkpoint's Kind: Milestone,
Review, Deadline) and `Tutorial_TicketImpact` (a ticket's Impact: Low, Medium, High). Currency entry
uses the configured currencies module.

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
