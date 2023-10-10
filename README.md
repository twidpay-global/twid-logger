# Twid Logger

Centralized logging package for maintaining consistent logging across projects.

## Installation

You can install this package via [Composer](https://getcomposer.org/):

```bash
composer require twidpay/twid-logger
```

## Configuration

Publish the configuration file using the following artisan command:

```bash
php artisan vendor:publish --tag=config
```

This will create a `logging.php` file in your `config` directory. You can customize this file to define your log channels, metadata, and masked fields.

### Laravel Configuration

#### Alias

Add the following alias to the aliases array in your `config/app.php` file:

```bash 
'TLog' => twid\logger\Facades\TLog::class,
```

#### Provider

Add the following service provider to the providers array in your `config/app.php` file:

```bash
twid\logger\TwidLoggerServiceProvider::class,
```

### Lumen Configuration

#### Alias

Open your `bootstrap/app.php` file and add the following alias:

```bash
class_alias('twid\logger\Facades\TLog', 'TLog');
```

#### Provider

Open your `bootstrap/app.php` file and register the service provider:

```bash
$app->register(twid\logger\TwidLoggerServiceProvider::class);
```






## Usage

### Facade: TLog

This package provides a facade named `TLog` for logging messages. You can use this facade to log messages to different channels dynamically.

Example:

```bash
use twid\logger\Facades\TLog;

// Log an information message to the 'default' channel
TLog::info('This is an information message');

// Log an error message to the 'error' channel
TLog::error('This is an error message', ['error_code' => 500]);
```

If you use a channel name as a method, the facade will log the message to that channel. If the method is not explicitly defined, it is treated as a channel name, and the log method is invoked on the corresponding logger instance.

### Logger

The `Logger` class is responsible for handling log messages. It's initialized with a log channel, and you can use it to log messages with specific data.

Example:

```bash
use twid\logger\Logger;

// Create a logger instance for the 'info' channel
$logger = new Logger('info');

// Log a message with additional data
$logger->log('This is a log message', ['user_id' => 123]);
```

### License

This package is open-source software licensed under the MIT license.

