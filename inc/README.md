# Inc Folder Organization

This folder has been reorganized for better maintainability and clarity.

## Structure

```
inc/
├── admin/              # Admin-specific functionality
│   └── tinymce-btns.php
├── core/               # Core theme framework
│   ├── framework.class.php
│   ├── framework-hooks.class.php
│   ├── helpers.php
│   └── theme-setup.php
├── features/           # Feature-specific modules
│   └── book/          # Book generation feature
│       ├── book-maker.php
│       ├── generate-book-pdf.php
│       ├── generate-book-pdf-html.php
│       └── vendor/    # Third-party libraries (dompdf)
├── integrations/       # Third-party integrations
│   ├── acf-config.php     # Advanced Custom Fields
│   ├── ajax.php           # AJAX handlers
│   ├── third-party.php    # Misc integrations (Userback, etc.)
│   ├── translations.php   # Polylang translations
│   └── woocommerce.php    # WooCommerce customizations
└── setup/              # Initial theme setup
    ├── assets.php         # CSS/JS enqueuing
    ├── navigation.php     # Menu registration
    └── post-types.php     # Custom post types
```

## What Changed

### Before
- All files were in the root `inc/` folder
- The `book/` folder contained both custom code and a massive third-party library (dompdf) with 400+ files
- Hard to navigate and understand the structure

### After
- Files are organized by purpose into logical subdirectories
- Third-party libraries are isolated in `vendor/` folder
- Clear separation between core, setup, integrations, and features
- Easier to locate and maintain specific functionality

## Load Order (in functions.php)

1. **Core** - Framework and helper functions
2. **Setup** - Theme initialization (assets, navigation, post types)
3. **Integrations** - Third-party services and plugins
4. **Features** - Specific features like book generation

---
*Last updated: November 2025*

