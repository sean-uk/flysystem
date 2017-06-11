<?php

namespace League\Flysystem\InterfaceStreaming;

use Hoa\Stream;
use Hoa\Stream\Context;
use Hoa\Stringbuffer\ReadWrite;
use InvalidArgumentException;

/**
 * A ResourceStream needs to provide an interface wrapper over a stream type resource
 *
 * @todo this is a bit of a messy substitute for {@see \GuzzleHttp\Psr7\stream_for}. Must be a better way!
 * @todo ideally this shouldn't implement Pointable
 *
 * Created by PhpStorm.
 * User: sean
 * Date: 07/06/17
 * Time: 19:21
 */
class ResourceStream extends ReadWrite
{
    /**
     * @var resource $resource a stream resource
     */
    private $resource;

    private $blocked;

    /**
     * ResourceStream constructor.
     * @param string|null $streamName
     * @param resource|null $streamResource a resource of type 'stream'
     */
    public function __construct($streamName = null, $streamResource = null)
    {
        if (empty($streamResource)) {
            $streamResource = fopen('php://memory', 'w+b');
        }
        if (!is_resource($streamResource) || get_resource_type($streamResource)!=='stream') {
            throw new InvalidArgumentException(__METHOD__ . ' expects argument #2 to be a valid stream resource.');
        }
        parent::__construct($streamName);
        $this->resource = $streamResource;
    }

    /**
     * {@inheritdoc}
     */
    public function read($length)
    {
        if ($this->blocked) {
            return '';
        }
        return parent::read($length);
    }

    /**
     * {@inheritdoc}
     */
    public function eof()
    {
        if ($this->isBlocked()) {
            return false;
        }
        return parent::eof();
    }

    /**
     * {@inheritdoc}
     */
    public function getStream()
    {
        return $this->resource;
    }

    /**
     * Set whether or not the stream is blocked.
     *
     * @param bool $blocked
     */
    public function block($blocked = true)
    {
        $this->blocked = boolval($blocked);
    }

    /**
     * @return bool
     */
    public function isBlocked()
    {
        return $this->blocked;
    }

    /**
     * A workaround: do not seek, return -1 for failure: {@see https://secure.php.net/manual/en/function.fseek.php}
     *
     * @param int $offset
     * @param int $whence
     * @return int
     */
    public function seek($offset, $whence = Stream\IStream\Pointable::SEEK_SET)
    {
        return -1;
    }

}