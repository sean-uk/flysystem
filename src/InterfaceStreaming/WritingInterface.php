<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 28/05/17
 * Time: 19:51
 */

namespace League\Flysystem\InterfaceStreaming;

use Psr\Http\Message\StreamInterface;
use League\Flysystem\Config;

interface WritingInterface
{
    /**
     * Write a new file using a InterfaceStreaming
     *
     * @param $path
     * @param StreamInterface $stream
     * @param Config $config
     * @return array|false false on failure file meta data on success
     */
    public function writeStreamInterface($path, StreamInterface $stream, Config $config);

    /**
     * Update a file using a InterfaceStreaming.
     *
     * @param $path
     * @param StreamInterface $stream
     * @param Config $config
     * @return array|false false on failure file meta data on success
     */
    public function updateStreamInterface($path, StreamInterface $stream, Config $config);
}