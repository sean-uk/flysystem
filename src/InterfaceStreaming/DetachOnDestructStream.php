<?php
/**
 * Created by PhpStorm.
 * User: sean
 * Date: 29/05/17
 * Time: 19:34
 */

namespace League\Flysystem\InterfaceStreaming;

use GuzzleHttp\Psr7\StreamDecoratorTrait;
use Psr\Http\Message\StreamInterface;

/**
 * This is a workaround for use when wrapping stream resources in a StreamInterface.
 * It detaches on destruct, so that the resource isn't automatically closed and should still be usable
 * until it's explicitly closed.
 *
 * Class DetachOnDestructStream
 * @package League\Flysystem\StreamInterface
 */
class DetachOnDestructStream implements StreamInterface
{
    use StreamDecoratorTrait;

    public function __destruct()
    {
        if (isset($this->stream)) {
            $this->stream->detach();
        }
    }
}