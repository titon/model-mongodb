<?php
/**
 * @copyright   2010-2013, The Titon Project
 * @license     http://opensource.org/licenses/bsd-license.php
 * @link        http://titon.io
 */

namespace Titon\Db\Mongo;

use Titon\Db\Driver\Schema;
use Titon\Db\Query;
use Titon\Db\Table;
use Titon\Utility\Hash;

/**
 * A table layer specific to MongoDB.
 *
 * @package Titon\Db\Mongo
 */
class Collection extends Table {

    /**
     * Configuration.
     *
     * @type array
     */
    protected $_config = [
        'entity' => 'Titon\Db\Mongo\Document'
    ];

    /**
     * {@inheritdoc}
     */
    final public function getPrimaryKey() {
        return '_id'; // Should always be _id
    }

    /**
     * {@inheritdoc}
     */
    public function getSchema() {
        if ($this->_schema instanceof Schema) {
            return $this->_schema;
        }

        $this->setSchema(new Schema($this->getTableName(), $this->_schema));

        return $this->_schema;
    }

    /**
     * {@inheritdoc}
     */
    public function query($type) {
        $this->data = [];

        $query = new MongoQuery($type, $this);
        $query->from($this->getTableName(), $this->getAlias());

        return $query;
    }

    /**
     * {@inheritdoc}
     */
    protected function _filterData(array &$data) {
        $aliases = array_keys($this->getRelations());
        $related = [];

        foreach ($aliases as $alias) {
            if (isset($data[$alias])) {
                $related[$alias] = $data[$alias];
                unset($data[$alias]);
            }
        }

        return $related;
    }

}