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

一个集成快递100物流追踪 API 的 Symfony 组件包。该组件包提供实时物流追踪、
自动状态同步和中国快递服务的订阅管理功能。

## 目录

- [功能特性](#功能特性)
- [安装](#安装)
- [系统要求](#系统要求)
- [快速开始](#快速开始)
  - [1. 配置](#1-配置)
  - [2. 数据库设置](#2-数据库设置)
  - [3. 加载基础数据](#3-加载基础数据)
  - [4. 基本使用](#4-基本使用)
- [API 接口](#api-接口)
- [控制台命令](#控制台命令)
  - [kuaidi100:query-number](#kuaidi100query-number)
  - [kuaidi100:set-subscribed](#kuaidi100set-subscribed)
- [实体类](#实体类)
- [服务类](#服务类)
- [高级用法](#高级用法)
  - [自定义追踪逻辑](#自定义追踪逻辑)
  - [Webhook 处理](#webhook-处理)
- [安全](#安全)
  - [API 凭据](#api-凭据)
  - [Webhook 安全](#webhook-安全)
  - [速率限制](#速率限制)
- [测试](#测试)
- [文档](#文档)
- [贡献](#贡献)
- [许可证](#许可证)
- [支持](#支持)

## 功能特性

- 🚚 **实时追踪**：使用快递100 API 实时查询物流状态
- 🔄 **自动同步**：后台命令自动更新状态和管理订阅
- 📦 **多承运商支持**：支持所有主流中国快递公司
- 🎯 **自动识别**：从快递单号自动识别快递公司
- 📍 **地址解析**：解析和处理中文地址
- 🔔 **Webhook 支持**：处理快递100回调通知
- 📊 **数据库集成**：使用 Doctrine ORM 存储和管理追踪数据
- ⚡ **后台处理**：基于 Cron 的自动追踪更新

## 安装

```bash
composer require tourze/kuaidi100-query-bundle
```

## 系统要求

- PHP 8.1 或更高版本
- Symfony 6.4 或更高版本
- Doctrine ORM 3.0+
- 有效的快递100 API 凭据

## 快速开始

### 1. 配置

在 Symfony 应用程序中注册该组件包，并在服务配置中设置您的快递100 API 凭据。

### 2. 数据库设置

运行迁移以创建必要的数据库表：

```bash
php bin/console doctrine:migrations:migrate
```

### 3. 加载基础数据

加载基本快递公司数据：

```bash
php bin/console doctrine:fixtures:load
```

### 4. 基本使用

```php
use Kuaidi100QueryBundle\Service\LogisticsService;
use Kuaidi100QueryBundle\Entity\LogisticsNum;

// 注入服务
public function __construct(
    private LogisticsService $logisticsService
) {}

// 查询并同步追踪信息
$trackingNumber = new LogisticsNum();
$trackingNumber->setNumber('1234567890');
$trackingNumber->setCompany('圆通速递');

$this->logisticsService->queryAndSync($trackingNumber);
```

## API 接口

组件包提供多个 HTTP 接口：

- `POST /kuaidi100/query` - 查询物流状态
- `POST /kuaidi100/auto-number` - 自动识别快递公司
- `POST /kuaidi100/poll` - 设置追踪订阅
- `POST /kuaidi100/sync` - 处理 webhook 回调
- `POST /kuaidi100/address-resolution` - 地址解析

## 控制台命令

组件包包含自动化控制台命令：

### kuaidi100:query-number

自动查询并同步所有追踪包裹的物流状态。

命令：`kuaidi100:query-number`

```bash
php bin/console kuaidi100:query-number
```

**功能特性：**
- 通过 cron 每分钟运行一次（使用 `@AsCronTask` 配置）
- 处理所有需要状态更新的包裹
- 在数据库中更新追踪信息
- 处理速率限制和错误恢复

### kuaidi100:set-subscribed

为未订阅的包裹设置追踪订阅。

命令：`kuaidi100:set-subscribed`

```bash
php bin/console kuaidi100:set-subscribed
```

**功能特性：**
- 通过 cron 每分钟运行一次（使用 `@AsCronTask` 配置）
- 为新包裹订阅推送通知
- 通过 webhook 启用自动状态更新
- 在数据库中管理订阅状态

## 实体类

- **LogisticsNum**：表示带有相关元数据的追踪号码
- **LogisticsStatus**：存储单个追踪状态更新
- **KuaidiCompany**：包含快递公司信息和 API 代码
- **Account**：管理快递100 API 账户凭据

## 服务类

- **LogisticsService**：追踪操作的主要服务
- **Kuaidi100Service**：快递100 的低级别 API 客户端
- **AttributeControllerLoader**：处理自动路由注册

## 高级用法

### 自定义追踪逻辑

```php
use Kuaidi100QueryBundle\Request\Kuaidi100QueryRequest;
use Kuaidi100QueryBundle\Service\Kuaidi100Service;

public function customTracking(Kuaidi100Service $apiService): array
{
    $request = new Kuaidi100QueryRequest();
    $request->setCom('yuantong'); // 快递公司代码
    $request->setNum('1234567890'); // 追踪号码
    $request->setPhoneNumber('138****8888'); // 可选的电话号码
    
    return $apiService->request($request);
}
```

### Webhook 处理

组件包自动处理快递100 webhook 回调。请将您的 webhook URL 配置为
指向 `/kuaidi100/sync` 接口。

## 安全

### API 凭据

- 使用 Symfony 的密钥管理功能安全存储您的快递100 API 凭据
- 绝不要将 API 密钥提交到版本控制系统
- 使用环境变量进行敏感配置

### Webhook 安全

- 为 webhook 回调实现签名验证
- 使用 HTTPS 端点作为 webhook URL
- 验证和清理所有传入的 webhook 数据

### 速率限制

- 组件包包含 API 调用的内置速率限制
- 监控您的 API 使用情况以避免超出配额
- 为失败的请求实现指数回退

## 测试

运行测试套件：

```bash
vendor/bin/phpunit packages/kuaidi100-query-bundle/tests
```

## 文档

- [快递100 API 文档](https://api.kuaidi100.com/document/shishichaxunchanpinjieshao)
- [快递100 官方网站](https://www.kuaidi100.com/)

## 贡献

1. Fork 仓库
2. 创建您的特性分支 (`git checkout -b feature/amazing-feature`)
3. 提交您的更改 (`git commit -m 'Add some amazing feature'`)
4. 推送到分支 (`git push origin feature/amazing-feature`)
5. 打开一个 Pull Request

## 许可证

该组件包在 MIT 许可证下发布。详情请查看 [LICENSE](LICENSE)。

## 支持

- 📧 在 [GitHub](https://github.com/tourze/kuaidi100-query-bundle/issues) 提交问题
- 📖 阅读[文档](https://github.com/tourze/kuaidi100-query-bundle)
- 💬 加入我们的社区讨论