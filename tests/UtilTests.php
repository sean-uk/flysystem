<?php

namespace League\Flysystem;

use Psr\Http\Message\StreamInterface;
use GuzzleHttp\Psr7;
use InvalidArgumentException;

class UtilTests extends \PHPUnit_Framework_TestCase
{
    public function testEmulateDirectories()
    {
        $input = [
            ['dirname' => '', 'filename' => 'dummy', 'path' => 'dummy', 'type' => 'file'],
            ['dirname' => 'something', 'filename' => 'dummy', 'path' => 'something/dummy', 'type' => 'file'],
            ['dirname' => 'something', 'path' => 'something/dirname', 'type' => 'dir'],
        ];
        $output = Util::emulateDirectories($input);
        $this->assertCount(4, $output);
    }

    public function testContentSize()
    {
        $this->assertEquals(5, Util::contentSize('12345'));
        $this->assertEquals(3, Util::contentSize('135'));
    }

    public function mapProvider()
    {
        return [
            [['from.this' => 'value'], ['from.this' => 'to.this', 'other' => 'other'], ['to.this' => 'value']],
            [['from.this' => 'value', 'no.mapping' => 'lost'], ['from.this' => 'to.this'], ['to.this' => 'value']],
        ];
    }

    /**
     * @dataProvider  mapProvider
     */
    public function testMap($from, $map, $expected)
    {
        $result = Util::map($from, $map);
        $this->assertEquals($expected, $result);
    }

    public function dirnameProvider()
    {
        return [
            ['filename.txt', ''],
            ['dirname/filename.txt', 'dirname'],
            ['dirname/subdir', 'dirname'],
        ];
    }

    /**
     * @dataProvider  dirnameProvider
     */
    public function testDirname($input, $expected)
    {
        $result = Util::dirname($input);
        $this->assertEquals($expected, $result);
    }

    public function testEnsureConfig()
    {
        $this->assertInstanceOf('League\Flysystem\Config', Util::ensureConfig([]));
        $this->assertInstanceOf('League\Flysystem\Config', Util::ensureConfig(null));
        $this->assertInstanceOf('League\Flysystem\Config', Util::ensureConfig(new Config()));
    }

    /**
     * @expectedException  LogicException
     */
    public function testInvalidValueEnsureConfig()
    {
        Util::ensureConfig(false);
    }

    public function invalidPathProvider()
    {
        return [
            ['something/../../../hehe'],
            ['/something/../../..'],
            ['..'],
            ['something\\..\\..'],
            ['\\something\\..\\..\\dirname'],
        ];
    }

    /**
     * @expectedException  LogicException
     * @dataProvider       invalidPathProvider
     */
    public function testOutsideRootPath($path)
    {
        Util::normalizePath($path);
    }

    public function pathProvider()
    {
        return [
            ['.', ''],
            ['/path/to/dir/.', 'path/to/dir'],
            ['/dirname/', 'dirname'],
            ['dirname/..', ''],
            ['dirname/../', ''],
            ['dirname./', 'dirname.'],
            ['dirname/./', 'dirname'],
            ['dirname/.', 'dirname'],
            ['./dir/../././', ''],
            ['/something/deep/../../dirname', 'dirname'],
            ['00004869/files/other/10-75..stl', '00004869/files/other/10-75..stl'],
            ['/dirname//subdir///subsubdir', 'dirname/subdir/subsubdir'],
            ['\dirname\\\\subdir\\\\\\subsubdir', 'dirname/subdir/subsubdir'],
            ['\\\\some\shared\\\\drive', 'some/shared/drive'],
            ['C:\dirname\\\\subdir\\\\\\subsubdir', 'C:/dirname/subdir/subsubdir'],
            ['C:\\\\dirname\subdir\\\\subsubdir', 'C:/dirname/subdir/subsubdir'],
            ['example/path/..txt', 'example/path/..txt'],
            ['\\example\\path.txt', 'example/path.txt'],
            ['\\example\\..\\path.txt', 'path.txt'],
            ["some\0/path.txt", 'some/path.txt'],
        ];
    }

    /**
     * @dataProvider  pathProvider
     */
    public function testNormalizePath($input, $expected)
    {
        $result = Util::normalizePath($input);
        $double = Util::normalizePath(Util::normalizePath($input));
        $this->assertEquals($expected, $result);
        $this->assertEquals($expected, $double);
    }

    public function pathAndContentProvider()
    {
        return [
            ['/some/file.css', '.event { background: #000; } ', 'text/css'],
            ['/some/file.css', 'body { background: #000; } ', 'text/css'],
            ['/some/file.txt', 'body { background: #000; } ', 'text/plain'],
            ['/1x1', base64_decode('R0lGODlhAQABAIAAAAUEBAAAACwAAAAAAQABAAACAkQBADs='), 'image/gif'],
        ];
    }

    /**
     * @dataProvider  pathAndContentProvider
     */
    public function testGuessMimeType($path, $content, $expected)
    {
        $mimeType = Util::guessMimeType($path, $content);
        $this->assertEquals($expected, $mimeType);
    }

    public function testStreamSize()
    {
        $stream = tmpfile();
        fwrite($stream, 'aaa');
        $size = Util::getStreamSize($stream);
        $this->assertEquals(3, $size);
        fclose($stream);
    }

    public function testRewindStream()
    {
        $stream = tmpfile();
        fwrite($stream, 'something');
        $this->assertNotEquals(0, ftell($stream));
        Util::rewindStream($stream);
        $this->assertEquals(0, ftell($stream));
        fclose($stream);
    }

    public function testNormalizePrefix()
    {
        $this->assertEquals('test/', Util::normalizePrefix('test', '/'));
        $this->assertEquals('test/', Util::normalizePrefix('test/', '/'));
    }

    public function testEnsureStreamInterfaceNonResource()
    {
        $this->setExpectedException(InvalidArgumentException::class);
        Util::ensureStreamInterface('not a resource');
    }

    public function testEnsureStreamInterfaceResource()
    {
        $stream = tmpfile();
        $result = Util::ensureStreamInterface($stream);

        fclose($stream);
        $this->assertInstanceOf(StreamInterface::class, $result);
    }

    public function testEnsureStreamInterfaceAlreadyInteface()
    {
        $stream = Psr7\stream_for(tmpfile());
        $result = Util::ensureStreamInterface($stream);

        $stream->close();

        $this->assertInstanceOf(StreamInterface::class, $stream);
        $this->assertEquals($stream, $result);  // the original object should have been returned unchanged.
    }

    public function testEnsureStreamInterfaceNonStreamResource()
    {
        if (!extension_loaded('xml')) {
            $this->markTestSkipped("Skipping test, XML lib not loaded.");
        }

        $resource = xml_parser_create();

        $this->setExpectedException(InvalidArgumentException::class);
        Util::ensureStreamInterface($resource);
    }
}
