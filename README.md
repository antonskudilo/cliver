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

*By default, when no command name is provided, the application runs the `HelpCommand`.
It displays all available commands in the format signature → description:*

```bash
Available commands:
help   Show the list of available commands
```


*The default command can be redefined in the `Providers/AppServiceProvider` (see item "Service configuration").*

## Running commands
To register and use commands, they must be added to `Providers/CommandServiceProvider`.

#### Register the command in `Providers/CommandServiceProvider`:

```bash
class CommandServiceProvider extends BaseCommandServiceProvider
{
    protected function commands(): array
    {
        return [
            App\Console\Commands\PrintCommand::class,
        ];
    }
}
```

Each command defines its own static `getName()` method, which is used as the CLI signature.

#### Example of a custom command:

```bash
final class PrintCommand implements CommandInterface
{
    private PrinterInterface $printer;

    public function __construct(PrinterInterface $printer)
    {
        $this->printer = $printer;
    }
    
    public static function getName(): string
    {
        return 'print';
    }

    public static function getDescription(): string
    {
        return 'Example print command';
    }

    public function execute(array $arguments = []): void
    {
        $this->printer->print();
    }
}
```

## Dependency injection container

The framework core includes a simple DI container.
Services can be registered in `Providers/AppServiceProvider` or provided dynamically in tests.

#### PrinterInterface:

```bash
interface PrinterInterface
{
    public function print(): void;
}
```

#### ConsolePrinter:

```bash
class ConsolePrinter implements PrinterInterface
{
    public function print(): void
    {
        println('Hello, I`m a ConsolePrinter');
    }
}
```

## Service configuration

#### Service bindings could be defined in the `Providers/AppServiceProvider`.
It contains the `register` method, which can be used for `bind` and `singleton`, using the container instance passed as a parameter.

This allows you to configure how dependencies are resolved across the application, and makes it easy to swap or mock implementations in tests.

#### `Providers/AppServiceProvider`:

```bash
final class AppServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->bind(
          App\Services\PrinterInterface::class, 
          App\Services\ConsolePrinter::class
        );
    }
}
```

#### Now you can run:

```bash
php bin/cliver print
```

## Overriding dependencies in tests

When writing tests, you can replace services on-the-fly using the DI container.
This is useful for mocking dependencies like console output, external API calls, etc.

#### Example: overriding the PrinterInterface with a test implementation:

```bash
$this->container->bind(
    App\Services\PrinterInterface::class,
    new App\Tests\TestPrinter()
);
```
This allows your command to use the test printer instead of the real console printer.

#### TestPrinter:

```bash
class TestPrinter implements PrinterInterface
{
    public function print(): void
    {
        echo 'Hello, I`m a TestPrinter';
    }
}
```

## Base test case

A base test class `tests/TestCase.php` is provided to bootstrap the application and container for every test:

```bash
class TestCase extends BaseTestCase
{
    protected Container $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->container = Bootstrap::init();
    }

    protected function makeApp(): Application
    {
        return $this->container->get(Application::class);
    }
    
    protected function fake(string $abstract, object $fake): object
    {
        $this->swap($abstract, $fake);

        return $fake;
    }
    
    protected function swap(string $abstract, object $fake): void
    {
        $this->container->bind($abstract, $fake);
    }
    
    protected function fakeSingleton(string $abstract, object $fake): object
    {
        $this->swapSingleton($abstract, $fake);

        return $fake;
    }
    
    protected function swapSingleton(string $abstract, object $fake): void
    {
        $this->container->singleton($abstract, $fake);
    }
}
```

## Testing a command

#### Example of a test for a command registered in `App/Providers/CommandServiceProvider`:

```bash
final class PrintCommandTest extends TestCase
{
    public function testCommandOutput(): void
    {
        $this->fakeSingleton(
            PrinterInterface::class, 
            new TestPrinter()
        );

        ob_start();
        $app->run(['print']);
        $output = ob_get_clean();
        
        $this->assertStringContainsString(
            'Hello, I`m a TestPrinter', 
            $output
        );
    }
}
```

## Running tests

```bash
vendor/bin/phpunit --colors=always
```

## Helpers

This project includes a set of helper functions grouped into console, environment, and path utilities.
They simplify working with CLI output, environment variables, and project paths.

#### Console helpers

- `errorln(string $message = '')` – print a message to STDERR with a [Error] prefix.
- `pad(string $label, string $value, int $padLength = 25)` – format label/value pairs with aligned output.
- `padAuto(array $rows)` – automatically align and print an array of key => value pairs.
- `println(string $message = '')` – print a message to STDOUT with a newline.

#### Environment helpers

- `env(string $key, mixed $default = null)` – retrieve an environment variable with type casting (true/false/null).
- `is_debug()` – check if APP_DEBUG is enabled.
- `loadEnv(string $path)` – load variables from a .env file into $_ENV, $_SERVER, and getenv().

#### Path helpers

- `base_path(string $path = '')` – get the absolute path relative to the project root.
- `config_path(string $path = '')` – get the absolute path to the config/ directory.
- `join_path(string $base, string $path = '')` – safely concatenate directory paths.

## Environment configuration

The application uses a `.env` file in the project root to configure environment variables.
A template file `.env.example` is provided and can be copied to create your own `.env`.

#### Currently, it supports:

- `APP_DEBUG` — when set to true, full stack traces are displayed in the console.
  Otherwise, only a short error message is shown. This allows easy switching between development and production modes.


## Project structure

```bash
├── bootstrap/                    
│   └── providers.php             # Registering Application Service Providers
├── app/                          
│   ├── Console/                  
│   │   └── Commands/             # CLI commands
│   └── Providers/                # Application Service Providers
├── tests/                        # PHPUnit tests
├── cliver                        # CLI entry script
├── composer.json
└── phpunit.xml
```

### License

MIT


