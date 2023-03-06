<?php

declare(strict_types=1);

namespace DotEnvParser;

use InvalidArgumentException;

class Parser
{
    private string $filename;

    /**
     * @throws InvalidArgumentException
     */
    public function __construct(string $filename)
    {
        $this->filename = $filename;
        if (! file_exists($filename)) {
            throw new InvalidArgumentException("{$filename} is not available");
        }
        if (! is_readable($filename)) {
            throw new InvalidArgumentException("{$filename} cannot be read");
        }
    }

    /**
     * Retrieve the contents of the nominated file
     */
    public function getContents(): array
    {
        return parse_ini_file($this->filename);
    }

    /**
     * Add an item to the nominated file
     *
     * @param mixed $value
     */
    public function addItem(string $key, $value): void
    {
        $content = $this->getContents();
        $content[$key] = $value;
        $fh = fopen($this->filename, 'w');
        foreach ($content as $key => $value) {
            fwrite($fh, "{$key}={$value}\n");
        }
        fclose($fh);
    }

    /**
     * Retrieve a single item from the nominated file
     *
     * @return bool|mixed
     */
    public function getItem(string $key)
    {
        $contents = $this->getContents();
        return (array_key_exists($key, $contents))
            ? $contents[$key]
            : null;
    }

    /**
     * Determine if the nominated file contains the given item
     */
    public function hasItem(string $key): bool
    {
        return array_key_exists($key, $this->getContents());
    }
}
