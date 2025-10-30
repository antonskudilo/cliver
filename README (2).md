# Orders Statistic CLI

This is a small pet project that demonstrates **separation of concerns** in PHP code design.  
It provides a CLI tool to aggregate and render statistics from CSV files.

Currently, the project implements one main feature:  
**Calculating order statistics from three CSV files:**
- `orders.csv`
- `drivers.csv`
- `cities.csv`

---

## Features

- Clear **separation of responsibilities** between data access, business logic, and presentation.
- Support for **relations** (`driver`, `city`) with both lazy and eager loading.
- Flexible **filters** for narrowing down the dataset:
  - by driver(s)
  - by city(s)
  - by date
- Dedicated classes for **aggregation** and **rendering** of statistics.

---

## Usage

Run the CLI command:

```bash
php cliver orders_statistic [--driver=ID1,ID2,...] [--city=ID1,ID2,...] [--date=YYYY-MM-DD]
```

### Examples

1. Show statistics for all orders:

```bash
php cliver orders_statistic
```

2. Show statistics for a specific date:

```bash
php cliver orders_statistic --date=2025-08-04
```

3. Show statistics filtered by drivers:

```bash
php cliver orders_statistic --driver=1,2,5
```

4. Show statistics filtered by cities:

```bash
php cliver orders_statistic --city=10,11
```

---

## Example Output

```text
=== Orders statistics ===
Total orders:  0
Total sum:     0
Average sum:   0
```

---

## Architecture Overview

- **Repositories**  
  Encapsulate access to CSV data (`OrdersRepository`, `DriversRepository`, `CitiesRepository`).
  
- **Relations**  
  Repositories support `withRelation()` to eagerly load related entities (e.g. `driver`, `city`).

- **Filtering**  
  Repositories expose methods like `whereDriverId()`, `whereCityId()`, and `whereDate()`  
  to apply filters before fetching data.

- **Aggregation & Rendering**  
  `OrdersStatisticAggregator` handles business logic of calculating totals, averages, etc.  
  `OrdersStatisticRender` formats and outputs results to the console.

- **Command Layer**  
  `OrdersStatisticCommand` wires everything together: parses CLI arguments, fetches data, applies filters, aggregates, and renders the statistics.

---

## Requirements

- PHP **8.3+**
- Composer (for autoloading)

---

## Installation

1. Clone the repository
2. Install dependencies:
   ```bash
   composer install
   composer dump-autoload
   ```
3. Run the CLI command (see [Usage](#usage)).

---

## Notes

- This is a **pet project** and not intended for production.
- The main goal is to experiment with **separation of concerns** and **command design** in PHP.








добавить в readme:
- Отношения:
Мы можем определять many-to-many отношение в двух режимах:
С pivot-моделью (PivotModel) — если нам нужно работать с данными из таблицы связи.
Без pivot-модели — если нас интересует только связь, без доступа к дополнительным данным.
- + добавить пример синтаксиса