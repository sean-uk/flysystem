<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 28/05/17
 * Time: 19:51
 */

namespace League\Flysystem;

use Psr\Http\Message\StreamInterface;

interface StreamInterfaceAdapterInterface extends AdapterInterface
{
    /**
     * Write a new file using a StreamInterface
     *
     * @param $path
     * @param StreamInterface $stream
     * @param Config $config
     * @return array|false false on failure file meta data on success
     */
    public function writeStreamInterface($path, StreamInterface $stream, Config $config);

    /**
     * @param $path
     * @param StreamInterface $stream
     * @param Config $config
     * @return array|false false on failure file meta data on success
     */
    public function updateStreamInterface($path, StreamInterface $stream, Config $config);
}