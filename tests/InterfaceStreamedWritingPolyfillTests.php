<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 04/06/17
 * Time: 11:36
 */

namespace League\Flysystem\Adapter;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\InterfaceStreaming\Polyfill\InterfaceStreamedWritingTrait;
use GuzzleHttp\Psr7;
use PHPUnit_Framework_TestCase;

/**
 * @todo a way to test the fclose failure condition in InterfaceStreamedWritingTrait::writeStreamInterface
 *
 * Class InterfaceStreamedWritingPolyfillTests
 * @package League\Flysystem\Adapter
 */
class InterfaceStreamedWritingPolyfillTests extends PHPUnit_Framework_TestCase
{
    public function testWrite()
    {
        // the stream inferface i'm trying to write
        $stream = Psr7\stream_for(tmpfile());

        // the actual output resource that'll be written to
        $outputResource = tmpfile();

        /** @var InterfaceStreamedWritingTrait $implementor */
        $implementor = $this->getMockForTrait(InterfaceStreamedWritingTrait::class);
        $implementor
            ->method('getOutputResource')
            ->willReturn($outputResource);

        $response = $implementor->writeStreamInterface('file.extension', $stream, new Config());

        $stream->close();

        $this->assertTrue(is_array($response));
        $this->assertEquals('file', $response['type']);
        $this->assertEquals('file.extension', $response['path']);
    }

    public function testWriteOnOutputResourceFail()
    {
        $stream = Psr7\stream_for(tmpfile());

        /** @var InterfaceStreamedWritingTrait $implementor */
        $implementor = $this->getMockForTrait(InterfaceStreamedWritingTrait::class);
        $implementor
            ->method('getOutputResource')
            ->willReturn(false);

        $response = $implementor->writeStreamInterface('file.extension', $stream, new Config());

        $stream->close();

        $this->assertFalse($response);
    }

    public function testWriteVisibilySet()
    {
        $stream = Psr7\stream_for(tmpfile());
        $outputResource = tmpfile();

        $config = new Config();
        $config->set('visibility', AdapterInterface::VISIBILITY_PRIVATE);

        /** @var InterfaceStreamedWritingTrait $implementor */
        $implementor = $this->getMockForTrait(InterfaceStreamedWritingTrait::class);
        $implementor
            ->method('getOutputResource')
            ->willReturn($outputResource);

        // set the expectation that setVisibility be called.
        $implementor
            ->expects($this->atLeastOnce())
            ->method('setVisibility')
            ->with(
                $this->equalTo('file.extension'),
                $this->equalTo(AdapterInterface::VISIBILITY_PRIVATE)
            );

        $implementor->writeStreamInterface('file.extension', $stream, $config);

        $stream->close();
    }
}