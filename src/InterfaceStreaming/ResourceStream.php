<?php

namespace League\Flysystem\InterfaceStreaming;

use Hoa\Stream\Context;
use Hoa\Stringbuffer\ReadWrite;
use InvalidArgumentException;

/**
 * A ResourceStream needs to provide an interface wrapper over a stream type resource
 *
 * @todo this is a bit of a messy substitute for {@see \GuzzleHttp\Psr7\stream_for}. Must be a better way!
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

    /**
     * {@inheritdoc}
     * @param resource $streamResource
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

    public function getStream()
    {
        return $this->resource;
    }
}