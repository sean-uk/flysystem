<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 07/06/17
 * Time: 19:23
 */

namespace League\Flysystem\Adapter;

use League\Flysystem\InterfaceStreaming\ResourceStream;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

class ResourceStreamTests extends PHPUnit_Framework_TestCase
{
    public function testConstructWithStream()
    {
        $resource = fopen('php://memory', 'w+b');
        $stream = new ResourceStream(null, $resource);
        $this->assertInstanceOf(ResourceStream::class, $stream);

        fclose($resource);
    }

    public function testConstructWithoutStream()
    {
        $stream = new ResourceStream();
        $this->assertInstanceOf(ResourceStream::class, $stream);
    }

    public function testConstructInvalidResource()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        new ResourceStream(null, 'invalid');
    }

    public function testReadAll()
    {
        $stream = new ResourceStream();
        $stream->initializeWith('hello world!');

        $result = $stream->readAll();
        $this->assertEquals('hello world!', $result);
    }

    public function testBlock()
    {
        $stream = new ResourceStream();
        $stream->initializeWith('hello world!');

        $stream->block();
        $result = $stream->read(5);

        $this->assertEmpty($result);
    }

    public function testReadBlockRead()
    {
        $stream = new ResourceStream();
        $stream->initializeWith('hello world!');

        $contents = "";
        $calls = 0;
        while(!$stream->eof()) {

            $contents .= $stream->read(3);
            $calls++;

            // toggle the stream's blocking
            $stream->block(!$stream->isBlocked());
        }

        $this->assertEquals('hello world!', $contents);
        $this->assertGreaterThan(4, $calls);
    }

    public function testNonBlockedEof()
    {
        $stream = new ResourceStream();
        $stream->read(1);

        $this->assertTrue($stream->eof());
    }

    public function testBlockedEof()
    {
        $stream = new ResourceStream();
        $stream->read(1);

        $stream->block();

        $this->assertFalse($stream->eof());
    }
}