# Kuaidi100 Query Bundle

[![Latest Version](https://img.shields.io/packagist/v/tourze/kuaidi100-query-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/kuaidi100-query-bundle)
[![Build Status](https://img.shields.io/github/actions/workflow/status/tourze/php-monorepo/test.yml?branch=master&style=flat-square)](
https://github.com/tourze/php-monorepo/actions)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](
LICENSE)
[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.1-blue.svg?style=flat-square)](
https://php.net/)
[![Symfony](https://img.shields.io/badge/symfony-%3E%3D6.4-green.svg?style=flat-square)](
https://symfony.com/)
[![Quality Score](https://img.shields.io/scrutinizer/g/tourze/kuaidi100-query-bundle.svg?style=flat-square)](
https://scrutinizer-ci.com/g/tourze/kuaidi100-query-bundle)
[![Code Coverage](https://img.shields.io/codecov/c/github/tourze/kuaidi100-query-bundle.svg?style=flat-square)](
https://codecov.io/gh/tourze/kuaidi100-query-bundle)
[![Total Downloads](https://img.shields.io/packagist/dt/tourze/kuaidi100-query-bundle.svg?style=flat-square)](
https://packagist.org/packages/tourze/kuaidi100-query-bundle)

[English](README.md) | [中文](README.zh-CN.md)

A Symfony bundle for integrating with Kuaidi100 (快递100) logistics tracking API. 
This bundle provides real-time logistics tracking, automatic status synchronization, 
and subscription management for Chinese express delivery services.

## Table of Contents

- [Features](#features)
- [Installation](#installation)
- [Requirements](#requirements)
- [Quick Start](#quick-start)
  - [1. Configuration](#1-configuration)
  - [2. Database Setup](#2-database-setup)
  - [3. Load Fixture Data](#3-load-fixture-data)
  - [4. Basic Usage](#4-basic-usage)
- [API Endpoints](#api-endpoints)
- [Console Commands](#console-commands)
  - [kuaidi100:query-number](#kuaidi100query-number)
  - [kuaidi100:set-subscribed](#kuaidi100set-subscribed)
- [Entities](#entities)
- [Services](#services)
- [Advanced Usage](#advanced-usage)
  - [Custom Tracking Logic](#custom-tracking-logic)
  - [Webhook Handling](#webhook-handling)
- [Security](#security)
  - [API Credentials](#api-credentials)
  - [Webhook Security](#webhook-security)
  - [Rate Limiting](#rate-limiting)
- [Testing](#testing)
- [Documentation](#documentation)
- [Contributing](#contributing)
- [License](#license)
- [Support](#support)

## Features

- 🚚 **Real-time tracking**: Query logistics status in real-time using Kuaidi100 API
- 🔄 **Automatic synchronization**: Background commands for status updates and subscription management
- 📦 **Multi-carrier support**: Support for all major Chinese express delivery companies
- 🎯 **Auto-detection**: Automatic courier company detection from tracking numbers
- 📍 **Address resolution**: Parse and resolve Chinese addresses
- 🔔 **Webhook support**: Handle Kuaidi100 callback notifications
- 📊 **Database integration**: Store and manage tracking data with Doctrine ORM
- ⚡ **Background processing**: Cron-based automated tracking updates

## Installation

```bash
composer require tourze/kuaidi100-query-bundle
```

## Requirements

- PHP 8.1 or higher
- Symfony 6.4 or higher
- Doctrine ORM 3.0+
- Valid Kuaidi100 API credentials

## Quick Start

### 1. Configuration

Register the bundle in your Symfony application and configure your Kuaidi100 API 
credentials in your services configuration.

### 2. Database Setup

Run migrations to create the necessary database tables:

```bash
php bin/console doctrine:migrations:migrate
```

### 3. Load Fixture Data

Load the basic courier company data:

```bash
php bin/console doctrine:fixtures:load
```

### 4. Basic Usage

```php
use Kuaidi100QueryBundle\Service\LogisticsService;
use Kuaidi100QueryBundle\Entity\LogisticsNum;

// Inject the service
public function __construct(
    private LogisticsService $logisticsService
) {}

// Query and sync tracking information
$trackingNumber = new LogisticsNum();
$trackingNumber->setNumber('1234567890');
$trackingNumber->setCompany('圆通速递');

$this->logisticsService->queryAndSync($trackingNumber);
```

## API Endpoints

The bundle provides several HTTP endpoints:

- `POST /kuaidi100/query` - Query logistics status
- `POST /kuaidi100/auto-number` - Auto-detect courier company
- `POST /kuaidi100/poll` - Set up tracking subscription
- `POST /kuaidi100/sync` - Handle webhook callbacks
- `POST /kuaidi100/address-resolution` - Parse addresses

## Console Commands

The bundle includes automated console commands:

### kuaidi100:query-number

Automatically queries and synchronizes logistics status for all tracked packages.

Command: `kuaidi100:query-number`

```bash
php bin/console kuaidi100:query-number
```

**Features:**
- Runs every minute via cron (configured with `@AsCronTask`)
- Processes all packages that need status updates
- Updates tracking information in the database
- Handles rate limiting and error recovery

### kuaidi100:set-subscribed

Sets up tracking subscriptions for unsubscribed packages.

Command: `kuaidi100:set-subscribed`

```bash
php bin/console kuaidi100:set-subscribed
```

**Features:**
- Runs every minute via cron (configured with `@AsCronTask`)
- Subscribes to push notifications for new packages
- Enables automatic status updates via webhooks
- Manages subscription state in the database

## Entities

- **LogisticsNum**: Represents a tracking number with associated metadata
- **LogisticsStatus**: Stores individual tracking status updates
- **KuaidiCompany**: Contains courier company information and API codes
- **Account**: Manages Kuaidi100 API account credentials

## Services

- **LogisticsService**: Main service for tracking operations
- **Kuaidi100Service**: Low-level API client for Kuaidi100
- **AttributeControllerLoader**: Handles automatic route registration

## Advanced Usage

### Custom Tracking Logic

```php
use Kuaidi100QueryBundle\Request\Kuaidi100QueryRequest;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;

public function customTracking(Kuaidi100Service $apiService): array
{
    $request = new Kuaidi100QueryRequest();
    $request->setCom('yuantong'); // Courier company code
    $request->setNum('1234567890'); // Tracking number
    $request->setPhoneNumber('138****8888'); // Optional phone number
    
    return $apiService->request($request);
}
```

### Webhook Handling

The bundle automatically handles Kuaidi100 webhook callbacks. Configure your 
webhook URL to point to `/kuaidi100/sync` endpoint.

## Security

### API Credentials

- Store your Kuaidi100 API credentials securely using Symfony's secrets management
- Never commit API keys to version control
- Use environment variables for sensitive configuration

### Webhook Security

- Implement signature verification for webhook callbacks
- Use HTTPS endpoints for webhook URLs
- Validate and sanitize all incoming webhook data

### Rate Limiting

- The bundle includes built-in rate limiting for API calls
- Monitor your API usage to avoid exceeding quotas
- Implement exponential backoff for failed requests

## Testing

Run the test suite:

```bash
vendor/bin/phpunit packages/kuaidi100-query-bundle/tests
```

## Documentation

- [Kuaidi100 API Documentation](https://api.kuaidi100.com/document/shishichaxunchanpinjieshao)
- [Official Kuaidi100 Website](https://www.kuaidi100.com/)

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This bundle is released under the MIT License. See [LICENSE](LICENSE) for details.

## Support

- 📧 Submit issues on [GitHub](https://github.com/tourze/kuaidi100-query-bundle/issues)
- 📖 Read the [documentation](https://github.com/tourze/kuaidi100-query-bundle)
- 💬 Join our community discussions