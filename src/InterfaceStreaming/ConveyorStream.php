<?php

namespace League\Flysystem\InterfaceStreaming;

use Hoa\Stream;
use InvalidArgumentException;

/**
 * A ConveyorStream wraps another stream interface to disable seeking and allow 'pausing'.
 * A Paused conveyor can not be read from
 *
 * @todo this is a bit of a messy substitute for {@see \GuzzleHttp\Psr7\stream_for}. Must be a better way!
 * @todo implement Hoa\Stream\Composite, no?
 *
 * Created by PhpStorm.
 * User: sean
 * Date: 07/06/17
 * Time: 19:21
 */
class ConveyorStream implements Stream\IStream\In, Stream\IStream\Out
{
    /** @var Conveyable $stream */
    private $stream;

    private $paused;

    /**
     * ResourceStream constructor.
     * @param string|null $streamName
     * @param Conveyable|mixed $stream ie; an object implementing Hoa\Stream\IStream\In AND Hoa\Stream\IStream\Out
     */
    public function __construct($streamName = null, $stream)
    {
        // sadly you can't require an argument to implement multiple interfaces, so I think this will have to do!
        if (!$stream instanceof Stream\IStream\In || !$stream instanceof Stream\IStream\Out) {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #2 to be an instance of Hoa\Stream\IStream\In and Hoa\Stream\IStream\Out');
        }
        $this->stream = $stream;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if ($this->paused) {
            return '';
        }
        return $this->stream->read($length);
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        if ($this->isPaused()) {
            return false;
        }
        return $this->stream->eof();
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        return $this->stream->getStream();
    }

    /**
     * Set whether or not the stream is paused.
     *
     * @param bool $blocked
     */
    public function pause($paused = true)
    {
        $this->paused = boolval($paused);
    }

    /**
     * @return bool
     */
    public function isPaused()
    {
        return $this->paused;
    }

    /** {@inheritdoc} */
    public function readString($length)
    {
        if ($this->paused) {
            return null;
        }
        return $this->stream->readString($length);
    }

    /** {@inheritdoc} */
    public function readCharacter()
    {
        if ($this->paused) {
            return null;
        }
        return $this->stream->readCharacter();
    }

    /** {@inheritdoc} */
    public function readBoolean()
    {
        if ($this->paused) {
            return null;
        }
        return $this->stream->readBoolean();
    }

    /** {@inheritdoc} */
    public function readInteger($length = 1)
    {
        if ($this->paused) {
            return null;
        }
        return $this->stream->readInteger($length);
    }

    /** {@inheritdoc} */
    public function readFloat($length = 1)
    {
        if ($this->paused) {
            return null;
        }
        return $this->stream->readFloat($length);
    }

    /** {@inheritdoc} */
    public function readArray($argument = null)
    {
        if ($this->paused) {
            return null;
        }
        return $this->stream->readArray($argument);
    }

    /** {@inheritdoc} */
    public function readLine()
    {
        if ($this->paused) {
            return null;
        }
        return $this->stream->readLine();
    }

    /** {@inheritdoc} */
    public function readAll($offset = 0)
    {
        if ($this->paused) {
            return null;
        }
        return $this->stream->readAll($offset);
    }

    /** {@inheritdoc} */
    public function scanf($format)
    {
        if ($this->paused) {
            return null;
        }
        return $this->stream->scanf($format);
    }

    /** {@inheritdoc} */
    public function write($string, $length)
    {
        return $this->stream->write($string, $length);
    }

    /** {@inheritdoc} */
    public function writeString($string)
    {
        return $this->stream->writeString($string);
    }

    /** {@inheritdoc} */
    public function writeCharacter($character)
    {
        return $this->stream->writeCharacter($character);
    }

    /** {@inheritdoc} */
    public function writeBoolean($boolean)
    {
        return $this->stream->writeBoolean($boolean);
    }

    /** {@inheritdoc} */
    public function writeInteger($integer)
    {
        return $this->stream->writeInteger($integer);
    }

    /** {@inheritdoc} */
    public function writeFloat($float)
    {
        return $this->stream->writeFloat($float);
    }

    /** {@inheritdoc} */
    public function writeArray(array $array)
    {
        return $this->stream->writeArray($array);
    }

    /** {@inheritdoc} */
    public function writeLine($line)
    {
        return $this->stream->writeLine($line);
    }

    /** {@inheritdoc} */
    public function writeAll($string)
    {
        $this->stream->writeAll($string);
    }

    /** {@inheritdoc} */
    public function truncate($size)
    {
        $this->stream->truncate($size);
    }
}