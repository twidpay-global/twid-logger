<?php

use PHPUnit\Framework\TestCase;
use twid\logger\Logger;

class LoggerTest extends TestCase
{
    /** @var Logger */
    private $logger;

    /** @var string */
    private static string $logDir;

    public static function setUpBeforeClass(): void
    {
        // Use a temp directory for all log files during tests
        self::$logDir = sys_get_temp_dir() . '/twid-logger-tests';
        if (!is_dir(self::$logDir)) {
            mkdir(self::$logDir, 0777, true);
        }
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = new Logger('info');
    }

    public static function tearDownAfterClass(): void
    {
        // Clean up temp log files
        $files = glob(self::$logDir . '/*.log');
        if ($files) {
            foreach ($files as $file) {
                @unlink($file);
            }
        }
        @rmdir(self::$logDir);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_logs_a_message(): void
    {
        $message = 'Test message';
        $data = ['test' => 'verified'];
        $this->logger->log($message, $data);

        $logContent = $this->getTheLastLineOfTheFile('storage/logs/info.log');
        $this->assertEquals($message, $logContent['message']);
        $this->assertEquals($data['test'], $logContent['context']['test']);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_throws_exception_if_channel_configuration_not_found(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Channel configuration for 'nonexistent-channel' not found. Please check logging.php");

        new Logger('nonexistent-channel');
    }

    public function testItMasksSensitiveFields(): void
    {
        $logger = new Logger();
        $method = new \ReflectionMethod($logger, 'maskFields');

        $result = $method->invoke($logger, ['password' => 'secret']);

        $this->assertEquals(['password' => '********'], $result);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_retrieves_metadata_from_request(): void
    {
        $_REQUEST = ['user_id' => 123, 'ip_address' => '127.0.0.1'];

        $logger = new Logger();
        $method = new \ReflectionMethod($logger, 'metadata');

        $metadata = $method->invoke($logger);

        $this->assertEquals(['user_id' => 123, 'ip_address' => '127.0.0.1'], $metadata);
    }

    #[\PHPUnit\Framework\Attributes\Test]
    public function it_logs_with_metadata(): void
    {
        $_REQUEST = ['user_id' => 123, 'ip_address' => '127.0.0.1'];

        $this->logger->log('Test message with metadata');

        $logContent = $this->getTheLastLineOfTheFile('storage/logs/info.log');

        $this->assertEquals($_REQUEST['ip_address'], $logContent['context']['metadata']['ip_address']);
        $this->assertEquals($_REQUEST['user_id'], $logContent['context']['metadata']['user_id']);
    }

    public function getTheLastLineOfTheFile(string $path): array
    {
        $file = new \SplFileObject($path);
        $file->seek(PHP_INT_MAX);
        $lastLineNumber = $file->key() ? ($file->key() - 1) : 0;
        $file->seek($lastLineNumber);
        $lastLine = $file->current();

        return json_decode($lastLine, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
