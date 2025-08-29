# PHP CLIVER
A minimalistic PHP CLI framework.  

## Core features
- Simple command registration
- Dependency Injection support
- Service providers and singletons

## Requirements
- PHP 8.3 or higher

## Installation

```bash
git clone https://github.com/antonskudilo/cliver.git
cd cliver
composer install
composer dump-autoload
```

## Quick start
```bash
php cliver
```

*By default, when no command name is provided, the application runs the HelpCommand.
It displays all available commands in the format signature → description:*

```bash
Available commands:
help   Show the list of available commands
```

*The default command can be redefined (see item "Service configuration").*


*documentation is being supplemented...*