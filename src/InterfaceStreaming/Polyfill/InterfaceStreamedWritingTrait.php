<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 30/05/17
 * Time: 21:22
 */

namespace League\Flysystem\InterfaceStreaming\Polyfill;

use Psr\Http\Message\StreamInterface;
use League\Flysystem\Config;
use League\Flysystem\Util;
use GuzzleHttp\Psr7;

trait InterfaceStreamedWritingTrait
{
    /**
     * @inheritdoc
     */
    public function writeStreamInterface($path, StreamInterface $stream, Config $config)
    {
        $outputResource = $this->getOutputResource($path);
        if (!$outputResource) {
            return false;
        }

        $outputStream = Util::ensureStreamInterface($outputResource);

        Psr7\copy_to_stream($stream, $outputStream);

        // not using just StreamInterface::close because of the need to get the return value to know if it worked.
        if (!$this->closeOutputResource($outputResource)) {
            return false;
        }
        $outputStream->close();

        if ($visibility = $config->get('visibility')) {
            $this->setVisibility($path, $visibility);
        }

        $type = 'file';

        return compact('type', 'path', 'visibility');
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