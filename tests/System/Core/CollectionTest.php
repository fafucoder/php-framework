<?php
namespace Tests\System\Core;

use System\Core\Collectin;
use PHPUnit\Framework\TestCase;

/**
 * The class is based on Slim implimentation, MIT license (https://github.com/slimphp/Slim/blob/3.x/LICENSE.md).
 */
class CollectionTest extends TestCase {
    /**
     * @var Collection
     */
    protected $collection;

    public function setUp() {
        $this->collection = new Collection();
    }

    public function testArrayAccess() {
        $collection = new Collection(array('foo' => 'bar'));
        $this->assertEquals('bar', $collection['foo']);

        $collection['hello'] = 'world';
        $this->assertEquals('world', $collection->get('hello'));

        unset($collection['hello']);
        $this->assertFalse($collection->has('hello'));
    }

    public function testInitializeWithData() {
        $collection = new Collection(array('foo' => 'bar'));
        $collectionProperty = new ReflectionProperty($collection, 'data');
        $collectionProperty->setAccessible(true);

        $this->assertEquals(array('foo' => 'bar'), $collectionProperty->getValue($collection));
    }

    public function testAdd() {
        $this->collection->add('foo', 'bar');
        $this->assertArrayHasKey('foo', $this->property->getValue($this->collection));
        $collection = $this->property->getValue($this->collection);
        $this->assertEquals('bar', $collection['foo']);
    }

    public function testSet() {
        $this->collection->set('foo', 'bar');
        $this->assertArrayHasKey('foo', $this->property->getValue($this->collection));
        $collection = $this->property->getValue($this->collection);
        $this->assertEquals('bar', $collection['foo']);
    }

    public function testOffsetSet() {
        $this->collection['foo'] = 'bar';
        $this->assertArrayHasKey('foo', $this->property->getValue($this->collection));
        $collection = $this->property->getValue($this->collection);
        $this->assertEquals('bar', $collection['foo']);
    }

    public function testGet() {
        $this->property->setValue($this->collection, array('foo' => 'bar'));
        $this->assertEquals('bar', $this->collection->get('foo'));
    }

    public function testGetWithDefault() {
        $this->property->setValue($this->collection, array('foo' => 'bar'));
        $this->assertEquals('default', $this->collection->get('abc', 'default'));
    }

    public function testReplace() {
        $this->collection->replace(array(
            'abc' => '123',
            'foo' => 'bar',
        ));
        $this->assertArrayHasKey('abc', $this->property->getValue($this->collection));
        $this->assertArrayHasKey('foo', $this->property->getValue($this->collection));
        $collection = $this->property->getValue($this->collection);
        $this->assertEquals('123', $collection['abc']);
        $this->assertEquals('bar', $collection['foo']);
    }

    public function testExchange() {
        $this->collection->set('foo', 'bar');
        $this->assertEquals(array('foo' => 'bar'), $this->collection->exchange(array('bar' => 'foo')));
        $this->assertEquals(array('bar' => 'foo'), $this->collection->all());
    }

    public function testAll() {
        $data = array(
            'abc' => '123',
            'foo' => 'bar',
        );
        $this->property->setValue($this->collection, $data);
        $this->assertEquals($data, $this->collection->all());
    }

    public function testKeys() {
        $data = array(
            'abc' => '123',
            'foo' => 'bar',
        );
        $this->property->setValue($this->collection, $data);
        $this->assertEquals(array('abc', 'foo'), $this->collection->keys());
    }

    public function testHas() {
        $this->property->setValue($this->collection, array('foo' => 'bar'));
        $this->assertTrue($this->collection->has('foo'));
        $this->assertFalse($this->collection->has('abc'));
    }

    public function testOffsetExists() {
        $this->property->setValue($this->collection, array('foo' => 'bar'));
        $this->assertTrue(isset($this->collection['foo']));
    }

    public function testRemove() {
        $data = array(
            'abc' => '123',
            'foo' => 'bar',
        );
        $this->property->setValue($this->collection, $data);
        $this->collection->remove('foo');
        $this->assertEquals(array('abc' => '123'), $this->property->getValue($this->collection));
    }

    public function testOffsetUnset() {
        $data = array(
            'abc' => '123',
            'foo' => 'bar',
        );
        $this->property->setValue($this->collection, $data);

        unset($this->collection['foo']);
        $this->assertEquals(array('abc' => '123'), $this->property->getValue($this->collection));
    }

    public function testClear() {
        $data = array(
            'abc' => '123',
            'foo' => 'bar',
        );
        $this->property->setValue($this->collection, $data);
        $this->collection->clear();
        $this->assertEquals(array(), $this->property->getValue($this->collection));
    }

    public function testCount() {
        $this->property->setValue($this->collection, array('foo' => 'bar', 'abc' => '123'));
        $this->assertEquals(2, $this->collection->count());
    }

    public function testJsonSerialize() {
        $data = array(
            'abc' => '123',
            'foo' => 'bar',
        );

        $collection = new Collection($data);

        $this->assertEquals(json_encode($data), json_encode($collection));
    }
}
