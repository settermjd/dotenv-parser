<?php

namespace DotEnvParserTest;

use DotEnvParser\Parser;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    public function setUp(): void
    {
        $root = vfsStream::setup();

        vfsStream::newFile('.env')
            ->at($root)
            ->setContent(file_get_contents(__DIR__ . '/_data/.env'));
        $root
            ->getChild('.env')
            ->chmod(0644);
        $root
            ->addChild(
                vfsStream::newFile(
                    '.unreadable.file',
                    0000
                )
            );
    }

    public function testCanParseEnvFileSuccessfully()
    {
        $parser = new Parser(vfsStream::url('root/.env'));
        $contents = $parser->getContents();
        $expectedResults = [
            'SETTING_ONE' => "ONE",
            'SETTING_TWO' => "TWO",
        ];
        $this->assertCount(sizeof($expectedResults), $contents);
        $this->assertEmpty(array_diff($contents, $expectedResults));
    }

    public function testCanUpdateEnvFileSuccessfully()
    {
        $parser = new Parser(vfsStream::url('root/.env'));
        $this->assertCount(2, $parser->getContents());
        $expectedResults = [
            'SETTING_ONE' => "ONE",
            'SETTING_TWO' => "TWO",
            'SIZE' => 2,
            'UUID' => "9b80091f-482d-4ee4-9a8c-0a09b041cddc",
        ];
        $parser->addItem('SIZE', 2);
        $parser->addItem('UUID', "9b80091f-482d-4ee4-9a8c-0a09b041cddc");

        $contents = $parser->getContents();
        $this->assertCount(sizeof($expectedResults), $contents);
        $this->assertEmpty(array_diff($contents, $expectedResults));
    }

    /**
     * @dataProvider retrieveItemDataProvider
     */
    public function testCanRetrieveItem(string $key, $expectedValue)
    {
        $parser = new Parser(vfsStream::url('root/.env'));

        $this->assertSame($expectedValue, $parser->getItem($key));
    }

    public function retrieveItemDataProvider(): array
    {
        return [
            ["SETTING_ONE", "ONE"],
            ['UUID', null]
        ];
    }

    /**
     * @dataProvider hasItemDataProvider
     */
    public function testCanTestIfItemAlreadyExists(string $key, bool $itemExists)
    {
        $parser = new Parser(vfsStream::url('root/.env'));

        $this->assertSame($itemExists, $parser->hasItem($key));
    }

    public function hasItemDataProvider(): array
    {
        return [
            ["SETTING_ONE", true],
            ['UUID', false]
        ];
    }

    public function testWillThrowAnExceptionIfIniFileDoesNotExist()
    {
        $filename = 'root/.dotenv';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('%s is not available', $filename));

        new Parser(vfsStream::url($filename));
    }

    public function testWillThrowAnExceptionIfIniFileCannotBeRead()
    {
        $filename = 'root/.unreadable.file';
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(sprintf('%s cannot be read', $filename));

        new Parser(vfsStream::url($filename));
    }
}
