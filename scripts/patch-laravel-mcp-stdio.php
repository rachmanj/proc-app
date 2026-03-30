<?php

/**
 * Overwrites laravel/mcp StdioTransport with scripts/stubs/laravel-mcp-StdioTransport.php
 * so Cursor MCP on Windows gets LF + fflush + non-blocking stdin (see stub header).
 */
$stub = dirname(__DIR__).'/scripts/stubs/laravel-mcp-StdioTransport.php';
$target = dirname(__DIR__).'/vendor/laravel/mcp/src/Server/Transport/StdioTransport.php';

if (! is_file($stub)) {
    fwrite(STDERR, "patch-laravel-mcp-stdio: stub missing at {$stub}\n");

    exit(0);
}

if (! is_dir(dirname($target))) {
    fwrite(STDERR, "patch-laravel-mcp-stdio: vendor/laravel/mcp not installed, skip.\n");

    exit(0);
}

copy($stub, $target);

echo "patched: {$target}\n";
