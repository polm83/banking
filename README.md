# Framework-Agnostic Bank Account Project

This project is a simple **banking domain** implementation in **pure PHP 8**, designed to be:

- ✅ **Framework-agnostic**
- ✅ **Domain-Driven Design (DDD)** compliant
- ✅ **SOLID principles** friendly
- ✅ **Fully unit-tested (TDD)**
- ✅ Easy to integrate (via CLI or other adapters)
- ✅ Configurable via JSON file

---

## 📦 Features

- `BankAccount` entity that handles currency, balance and tracks daily limits
- Domain service (`BankingService`) handles credit/debit logic
- Application service (`TransferMoneyService`) handles money transfers
- Value objects: `Currency`, `Payment`, `TransactionDate`
- Domain event: `MoneyTransferredEvent` (dispatched on transfers)
- In-memory `BankAccountRepositoryInterface` for storage abstraction
- `BankingConfig` loaded from `config/banking_config.json`

---

## 🚀 Getting Started

### 1. Install dependencies

```bash
composer install
```

### 2. Run unit tests

```bash
vendor/bin/phpunit
```

---

## ⚙️ Configuration

Located in `config/banking_config.json`:

```json
{
  "fee_percentage": 0.005,
  "max_daily_debits": 3,
  "supported_currencies": [
    "PLN",
    "EUR",
    "USD"
  ]
}
```

You can change these values to modify transaction fees or daily debit limits and supported currencies.

---

## 🧪 Tests

- `tests/BankAccountTest.php` — core logic
- `tests/TransferMoneyServiceTest.php` — application layer
- `tests/RepositoryTest.php` — fake repository
- `tests/ConfigLoaderTest.php` — config loading

---

## 🧱 Project Structure

```
src/
├── Application/Service/TransferMoneyService.php
├── Config/
│   ├── BankingConfig.php
│   └── ConfigLoader.php
├── Domain/
│   ├── Entity/BankAccount.php
│   └── Enum/
│       ├── Currency.php
│       ├── Direction.php
│       └── DomainError.php
│   └── Event/
│       ├── EventInterface.php
│       ├── MoneyTransferredEvent.php
│       └── EventDispatcherInterface.php
│   ├── Policy/TransferLimitTrackerInterface.php
│   └── Service/
│       ├── BankingServiceInterface.php
│       └── BankingService.php
│   └── ValueObject/
│       ├── Currency.php
│       ├── Payment.php
│       └── TransactionDate.php
```

---

## 🧩 Extending

- Add HTTP or API adapter (e.g. Slim, Symfony, PSR-7)
- Swap in real repository implementation (e.g. PostgreSQL, Redis)
- Dispatch domain events to message queues (e.g. RabbitMQ)

---

## 👨‍💻 Author Notes

This project is meant to demonstrate a clean architecture with clear boundaries between domain, application logic,
infrastructure and interface.

Feel free to use it as a starter template for your own DDD-based services.
