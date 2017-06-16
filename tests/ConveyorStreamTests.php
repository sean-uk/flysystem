<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 07/06/17
 * Time: 19:23
 */

namespace League\Flysystem\Adapter;

use Hoa\Stream\IStream\In;
use Hoa\Stringbuffer\ReadWrite;
use League\Flysystem\InterfaceStreaming\Conveyable;
use League\Flysystem\InterfaceStreaming\ConveyorStream;
use PHPUnit_Framework_TestCase;
use InvalidArgumentException;

/**
 * Class ConveyorStreamTests
 * @package League\Flysystem\Adapter
 */
class ConveyorStreamTests extends PHPUnit_Framework_TestCase
{
    public function testConstructWithStream()
    {
        $conveyable = $this->prophesize(Conveyable::class)->reveal();
        $stream = new ConveyorStream(null, $conveyable);
        $this->assertInstanceOf(ConveyorStream::class, $stream);
    }

    public function testConstructWithoutConveyable()
    {
        $bad = $this->prophesize(In::class)->reveal();

        $this->setExpectedException(InvalidArgumentException::class);
        new ConveyorStream(null, $bad);
    }

    public function testReadAll()
    {
        $conveyable = new ReadWrite();
        $conveyable->initializeWith('hello world!');

        $stream = new ConveyorStream(null, $conveyable);

        $result = $stream->readAll();
        $this->assertEquals('hello world!', $result);
    }

    public function testBlock()
    {
        $conveyable = new ReadWrite();
        $conveyable->initializeWith('hello world!');

        $stream = new ConveyorStream(null, $conveyable);

        $stream->pause();
        $result = $stream->read(5);

        $this->assertEmpty($result);
    }

    public function testReadBlockRead()
    {
        $conveyable = new ReadWrite();
        $conveyable->initializeWith('hello world!');

        $stream = new ConveyorStream(null, $conveyable);

        $contents = "";
        $calls = 0;
        while(!$stream->eof()) {

            $contents .= $stream->read(3);
            $calls++;

            // toggle the stream's blocking
            $stream->pause(!$stream->isPaused());
        }

        $this->assertEquals('hello world!', $contents);
        $this->assertGreaterThan(4, $calls);
    }

    public function testNonPausedEof()
    {
        $conveyable = new ReadWrite();
        $stream = new ConveyorStream(null, $conveyable);
        $stream->read(1);

        $this->assertTrue($stream->eof());
    }

    public function testPausedEof()
    {
        $conveyable = new ReadWrite();
        $stream = new ConveyorStream(null, $conveyable);
        $stream->read(1);

        $stream->pause();

        $this->assertFalse($stream->eof());
    }
}