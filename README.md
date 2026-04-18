# CAS FormFlow

A custom multi-step contact form plugin for WordPress, built as part of a technical assignment.

## Overview

CAS FormFlow is a modular plugin that provides a flexible multi-step contact form system with lead storage and an extendable architecture.

The plugin is designed following WordPress best practices with a focus on scalability, maintainability, and clean code structure.

## Current Status

The plugin is under active development.

Implemented so far:

- Plugin scaffold and structure
- Activation hook
- Custom database table creation (via dbDelta)
- Database versioning system (option-based)
- Multi-step frontend form
- AJAX submission with nonce protection
- Server-side sanitization and validation
- Lead storage in the custom submissions table
- Admin email notification for new submissions
- SMTP delivery through WordPress PHPMailer

## Planned Features

- Admin panel for managing submissions
- Integrations (Telegram, Google Sheets, etc.)

## Installation

1. Copy the plugin folder into `/wp-content/plugins/`
2. Activate the plugin in the WordPress admin panel

## Usage

Add the `[cas_contact_form]` shortcode to a WordPress page or post.

## Development Environment

* IDE: Visual Studio Code
* Extensions: PHP Intelephense, ESLint, Prettier
* Local environment: MAMP PRO

## AI Tools Usage

The following AI tools were used during development:

- ChatGPT (GPT Plus) — architecture planning, debugging, and implementation guidance
- Codex (VS Code) — code generation and development acceleration

All generated code was reviewed, tested, and manually refined.

## Author

AlexBes
