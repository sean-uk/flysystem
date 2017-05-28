<?php

use League\Flysystem\Filesystem;
use Psr\Http\Message\StreamInterface;

class FlysystemStreamTests extends PHPUnit_Framework_TestCase
{
    public function testWriteStream()
    {
        $adapter = Mockery::mock('League\Flysystem\AdapterInterface');
        $adapter->shouldReceive('has')->andReturn(false);
        $adapter->shouldReceive('writeStream')->andReturn(['path' => 'file.txt'], false);
        $filesystem = new Filesystem($adapter);
        $this->assertTrue($filesystem->writeStream('file.txt', tmpfile()));
        $this->assertFalse($filesystem->writeStream('file.txt', tmpfile()));
    }

    public function testWritePsr7Stream()
    {
        $adapter = Mockery::mock(\League\Flysystem\AdapterInterface::class);
        $adapter->shouldReceive('has')->andReturn(false);
        $adapter->shouldReceive('writeStream')->andReturn(['path' => 'file.txt'], false);
        $filesystem = new Filesystem($adapter);

        $outStream = Mockery::mock(StreamInterface::class);
        $outStream->shouldReceive('tell')->andReturn(5000);
        $outStream->shouldReceive('isSeekable')->andReturn(false);
        $outStream->shouldNotReceive('seek');

        $this->assertTrue($filesystem->writeStream('file.txt', $outStream));
        $this->assertFalse($filesystem->writeStream('file.txt', $outStream));
    }

    public function testWritePsr7SeekableStream()
    {
        $adapter = Mockery::mock(\League\Flysystem\AdapterInterface::class);
        $adapter->shouldReceive('has')->andReturn(false);
        $adapter->shouldReceive('writeStream')->andReturn(['path' => 'file.txt'], false);
        $filesystem = new Filesystem($adapter);

        $outStream = Mockery::mock(StreamInterface::class);
        $outStream->shouldReceive('tell')->andReturn(5000);
        $outStream->shouldReceive('isSeekable')->andReturn(true);
        $outStream->shouldReceive('rewind');

        $this->assertTrue($filesystem->writeStream('file.txt', $outStream));
        $this->assertFalse($filesystem->writeStream('file.txt', $outStream));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testWriteStreamFail()
    {
        $adapter = Mockery::mock('League\Flysystem\AdapterInterface');
        $adapter->shouldReceive('has')->andReturn(false);
        $filesystem = new Filesystem($adapter);
        $filesystem->writeStream('file.txt', 'not a resource');
    }

    public function testUpdateStream()
    {
        $adapter = Mockery::mock('League\Flysystem\AdapterInterface');
        $adapter->shouldReceive('has')->andReturn(true);
        $adapter->shouldReceive('updateStream')->andReturn(['path' => 'file.txt'], false);
        $filesystem = new Filesystem($adapter);
        $this->assertTrue($filesystem->updateStream('file.txt', tmpfile()));
        $this->assertFalse($filesystem->updateStream('file.txt', tmpfile()));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testUpdateStreamFail()
    {
        $adapter = Mockery::mock('League\Flysystem\AdapterInterface');
        $adapter->shouldReceive('has')->andReturn(true);
        $filesystem = new Filesystem($adapter);
        $filesystem->updateStream('file.txt', 'not a resource');
    }

    public function testReadStream()
    {
        $adapter = Mockery::mock('League\Flysystem\AdapterInterface');
        $adapter->shouldReceive('has')->andReturn(true);
        $stream = tmpfile();
        $adapter->shouldReceive('readStream')->times(3)->andReturn(['stream' => $stream], false, false);
        $filesystem = new Filesystem($adapter);
        $this->assertInternalType('resource', $filesystem->readStream('file.txt'));
        $this->assertFalse($filesystem->readStream('other.txt'));
        fclose($stream);
        $this->assertFalse($filesystem->readStream('other.txt'));
    }
}
