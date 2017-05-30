<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 30/05/17
 * Time: 20:23
 */

namespace League\Flysystem\Adapter\StreamInterface;

use League\Flysystem\AdapterInterface;
use Psr\Http\Message\StreamInterface;

interface ReadingInterface extends AdapterInterface
{
    /**
     * Read a file as a StreamInterface.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStreamInterface($path);
}