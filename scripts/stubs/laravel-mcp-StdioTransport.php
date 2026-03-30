<?php

declare(strict_types=1);

namespace Laravel\Mcp\Server\Transport;

use Closure;
use Laravel\Mcp\Server\Contracts\Transport;

/**
 * Patched for Cursor MCP on Windows (scripts/patch-laravel-mcp-stdio.php):
 * - LF + fflush (MCP stdio; avoid CRLF from PHP_EOL on Windows).
 * - php://stdin + rtrim; non-blocking loop (matches laravel/mcp 0.5.x constructor API).
 */
class StdioTransport implements Transport
{
    /**
     * @param  (Closure(string): void)|null  $handler
     */
    public function __construct(
        protected string $sessionId,
        protected ?Closure $handler = null,
    ) {
        //
    }

    public function onReceive(Closure $handler): void
    {
        $this->handler = $handler;
    }

    public function send(string $message, ?string $sessionId = null): void
    {
        fwrite(STDOUT, $message."\n");
        fflush(STDOUT);
    }

    public function run(): void
    {
        if (function_exists('stream_set_write_buffer')) {
            @stream_set_write_buffer(STDOUT, 0);
        }

        $stdin = fopen('php://stdin', 'rb');
        stream_set_blocking($stdin, false);

        while (! feof($stdin)) {
            $line = fgets($stdin);
            if ($line === false) {
                usleep(10000);

                continue;
            }

            $line = rtrim($line, "\r\n");
            if ($line === '') {
                continue;
            }

            if ($this->handler instanceof Closure) {
                ($this->handler)($line);
            }
        }
    }

    public function sessionId(): string
    {
        return $this->sessionId;
    }

    public function stream(Closure $stream): void
    {
        $stream();
    }
}
