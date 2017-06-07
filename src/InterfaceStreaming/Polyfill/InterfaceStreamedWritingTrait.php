<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 30/05/17
 * Time: 21:22
 */

namespace League\Flysystem\InterfaceStreaming\Polyfill;

use Hoa\Stream\IStream\In;
use Hoa\Stream\IStream\Out;
use League\Flysystem\Config;
use League\Flysystem\Util;
use RuntimeException;

trait InterfaceStreamedWritingTrait
{
    /**
     * @inheritdoc
     * @throws RuntimeException
     */
    public function writeStreamInterface($path, In $stream, Config $config)
    {
        $outputResource = $this->getOutputResource($path);
        if (!$outputResource) {
            return false;
        }

        $outputStream = Util::ensureStreamInterface($outputResource);
        if (!$outputStream instanceof Out) {
            throw new RuntimeException(__METHOD__ . ' failed to obtain output stream.');
        }

        // perform the write
        // @todo factor this out into a separate functin
        // (based on \GuzzleHttp\Psr7\copy_to_stream)
        $bufferSize = 1024;
        while (!$stream->eof()) {
            $chunk = $stream->read($bufferSize);
            if (!$outputStream->write($chunk, strlen($chunk))) {
                break;
            }
        }

        if (!$this->closeOutputResource($outputResource)) {
            return false;
        }

        if ($visibility = $config->get('visibility')) {
            $this->setVisibility($path, $visibility);
        }

        $type = 'file';

        return compact('type', 'path', 'visibility');
    }

    /**
     * @inheritdoc
     */
    public function updateStreamInterface($path, In $stream, Config $config)
    {
        return $this->writeStreamInterface($path, $stream, $config);
    }

    /**
     * @param $resource
     * @return bool
     */
    protected function closeOutputResource($resource)
    {
        return fclose($resource);
    }

    /**
     * @param string $path
     * @return resource|false
     */
    abstract protected function getOutputResource($path);

    /**
     * @see \League\Flysystem\AdapterInterface::setVisibility
     */
    abstract public function setVisibility($path, $visibility);
}