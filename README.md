# CAS FormFlow

A custom multi-step contact form plugin for WordPress, built as part of a technical assignment.

## Overview

CAS FormFlow is a modular plugin that provides a flexible multi-step contact form system with lead storage and an extendable architecture.

The plugin is designed following WordPress best practices with a focus on scalability, maintainability, and clean code structure.

## Status

The plugin is ready for review as a completed technical assignment.

Implemented features:

- Plugin scaffold and structure
- Activation hook
- Custom database table creation (via dbDelta)
- Database versioning system (option-based)
- Multi-step frontend form
- AJAX submission with nonce protection
- Server-side sanitization and validation
- Lead storage in the custom submissions table
- Admin submissions list page
- Capability checks for admin-only submissions access
- Escaping, sanitization, and SQL identifier hardening
- Admin email notification for new submissions
- SMTP delivery through WordPress PHPMailer

## Screenshots

Screenshots are stored in the plugin root inside the `screenshots` directory.

## Installation

1. Copy the plugin folder into `/wp-content/plugins/`
2. Activate the plugin in the WordPress admin panel

## Usage

Add the `[cas_contact_form]` shortcode to a WordPress page or post.

## Development Environment

- IDE: Visual Studio Code
- Extensions: PHP Intelephense, ESLint, Prettier
- Local environment: MAMP PRO
- WordPress: local development installation
- PHP: 8.3

## Time Spent

Total time spent: approximately 3 days.

Development was split into setup and architecture, form flow and submission handling, admin/email functionality, QA, screenshots, and final documentation.

## AI Tools Usage

AI-assisted tools were used during development:

- ChatGPT (GPT Plus): architecture planning, debugging support, and implementation guidance
- Codex (VS Code): code generation support, refactoring assistance, and documentation updates

All AI-assisted output was reviewed, tested, and manually refined before being included in the final plugin.

## Author

AlexBes
