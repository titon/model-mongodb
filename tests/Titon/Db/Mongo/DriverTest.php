<?php
/**
 * @copyright   2010-2013, The Titon Project
 * @license     http://opensource.org/licenses/bsd-license.php
 * @link        http://titon.io
 */

namespace Titon\Db\Mongo;

use Titon\Common\Config;
use Titon\Test\TestCase;
use \Exception;

/**
 * Test class for driver specific testing.
 *
 * @property \Titon\Db\Mongo\MongoDriver $object
 */
class DriverTest extends TestCase {

    /**
     * This method is called before a test is executed.
     */
    protected function setUp() {
        parent::setUp();

        $this->object = new MongoDriver(Config::get('db'));
    }

    /**
     * Test exceptions are thrown if no servers defined.
     */
    public function testReplicaSet() {
        try {
            $this->object->setConfig('replicaSet', 'rs');
            $this->object->connect();

            $this->assertTrue(false);
        } catch (Exception $e) {
            $this->assertTrue(true);
        }
    }

    /**
     * Test server string building.
     */
    public function testGetServer() {
        $this->assertEquals('mongodb://127.0.0.1:27017', $this->object->getServer());

        $this->object->setConfig('socket', '/path/to/unix.sock');
        $this->assertEquals('mongodb:///path/to/unix.sock', $this->object->getServer());

        $this->object->setConfig('servers', ['domain.com:27017', 'localhost:27017']);
        $this->assertEquals('mongodb://domain.com:27017,localhost:27017', $this->object->getServer());
    }

    /**
     * Test DB commands.
     */
    public function testCommandQuery() {
        $this->object->connect();
        $this->loadFixtures('Users');

        $this->assertEquals(5, $this->object->executeQuery(['count' => 'users'])->count());

        $this->assertEquals([
            'miles',
            'batman',
            'superman',
            'spiderman',
            'wolverine'
        ], $this->object->executeQuery(['distinct' => 'users', 'key' => 'username'])->find());
    }

}