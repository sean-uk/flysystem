<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 28/05/17
 * Time: 19:51
 */

namespace League\Flysystem\InterfaceStreaming;

use Hoa\Stream\IStream\In;
use League\Flysystem\Config;

interface WritingInterface
{
    /**
     * Write a new file using an output stream interface
     *
     * @param $path
     * @param In $stream
     * @param Config $config
     * @return array|false false on failure file meta data on success
     */
    public function writeStreamInterface($path, In $stream, Config $config);

    /**
     * Update a file using an output stream interface
     *
     * @param $path
     * @param In $stream
     * @param Config $config
     * @return array|false false on failure file meta data on success
     */
    public function updateStreamInterface($path, In $stream, Config $config);
}