<?php

use PHPUnit\Framework\TestCase;
use twid\logger\Logger;

class LoggerTest extends TestCase
{
    /** @var Logger */
    private $logger;

    protected function setUp(): void
    {
        parent::setUp();
        $this->logger = new Logger('info');
    }

    /** @test */
    public function it_logs_a_message()
    {
        $message = 'Test message';
        $data = ['test' => 'verified'];
        $this->logger->log($message, $data);

        $date = date('Y-m-d', strtotime('now'));
        $logContent = $this->getTheLastLineOfTheFile('storage/logs/info-' . $date . '.log');
        $this->assertEquals($message, $logContent['message']);
        $this->assertEquals($data['test'], $logContent['context']['test']);
    }

    /** @test */
    public function it_throws_exception_if_channel_configuration_not_found()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Channel configuration for 'nonexistent-channel' not found. Please check logging.php");

        new Logger('nonexistent-channel');
    }

    public function testItMasksSensitiveFields()
    {
        $logger = new Logger();
        $method = new \ReflectionMethod($logger, 'maskFields');
        $method->setAccessible(true);

        $result = $method->invoke($logger, ['password' => 'secret']);

        $this->assertEquals(['password' => '********'], $result);
    }

    /** @test */
    public function it_retrieves_metadata_from_request()
    {
        // Mocking $_REQUEST for testing
        $_REQUEST = ['user_id' => 123, 'ip_address' => '127.0.0.1'];

        $logger = new Logger();
        $method = new \ReflectionMethod($logger, 'metadata');
        $method->setAccessible(true);

        $metadata = $method->invoke($logger);    

        $this->assertEquals(['user_id' => 123, 'ip_address' => '127.0.0.1'], $metadata);
    }

    /** @test */
    public function it_logs_with_metadata()
    {
        // Mocking $_REQUEST for testing
        $_REQUEST = ['user_id' => 123, 'ip_address' => '127.0.0.1'];

        $this->logger->log('Test message with metadata');

        $date = date('Y-m-d', strtotime('now'));
        $logContent = $this->getTheLastLineOfTheFile('storage/logs/info-' . $date . '.log');

        $this->assertEquals($_REQUEST['ip_address'], $logContent['context']['metadata']['ip_address']);
        $this->assertEquals($_REQUEST['user_id'], $logContent['context']['metadata']['user_id']);
    }

    public function getTheLastLineOfTheFile($path)
    {
        $file = new \SplFileObject($path);

        $file->seek(PHP_INT_MAX);
        $lastLineNumber = $file->key() ? ($file->key() - 1)  : 0;

        // Seek to the beginning of the last line
        $file->seek($lastLineNumber);
        $lastLine = $file->current();

        return json_decode($lastLine, true);
    }


    protected function tearDown(): void
    {
        // Add any cleanup code here
        parent::tearDown();
    }
}
