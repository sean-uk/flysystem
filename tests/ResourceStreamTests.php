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
}