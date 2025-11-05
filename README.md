# Orders Statistic CLI

This is a small pet project that demonstrates **clean architecture** and **separation of concerns** in PHP application design.  
It provides a CLI tool to aggregate and render statistics from both **CSV** and **Database** sources.

---

## Core Ideas

The project focuses on **clarity**, **extensibility**, and **decoupling** of logic layers:

- **Repositories** encapsulate domain logic and data access.
- **Relations** (`HasOne`, `HasMany`, `ManyToMany`) are implemented natively — similar to Eloquent — but without tying logic to a specific ORM.
- **Data sources** (`CSV`, `Database`, `Composite`) are interchangeable thanks to a unified `DataSourceInterface`.

---

## Data Source Abstraction

The project supports multiple storage layers:

- **CSV** — lightweight and fast for prototyping or offline analysis.
- **Database** — standard relational backend via PDO.
- **Composite Data Source** — seamlessly combines CSV and DB reads, allowing:
  - **Pattern-based file resolution** (e.g. `orders_2025-10-11.csv`)
  - **Lazy loading via generators (`yield`)**
  - **Fallback** logic — read from DB if no file matches.

### Example File Patterns

You can configure CSV patterns per entity to optimize I/O:
```bash
return [
    'orders' => [
        new CompositeCsvDataSource(
            directory: 'orders',
            pattern: 'orders_%s.csv',
            fileKeyField: 'date',
        ),
        DatabaseDataSource::class,
    ],
];
```


You can define entity-level configuration that determines:
- which data sources to use (`csv`, `database`, or both);
- custom **file name patterns** for fast CSV lookup.

The repository layer implements a relation system inspired by Laravel’s Eloquent, but designed to be **storage-agnostic**:

- Lazy and eager loading with `->withRelation(['drivers'])`
- Nested querying via `->whereHas('drivers', $conditions)`

## Design Principles

- **Separation of concerns** — repositories, data access, and models have distinct responsibilities.
- **Single responsibility** — each class does one thing: load data, describe a relation, or manage configuration.
- **Extensibility** — adding a new data source or relation type requires minimal changes.
- **Declarative configuration** — relations and data source settings are defined explicitly in repository maps.

---

## Tech Stack

- PHP 8.2+
- PDO (for DB access)
- Custom CSV reader with lazy streaming
- Dependency Injection container for managing data sources