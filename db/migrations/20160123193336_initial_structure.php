    <?php

use Phinx\Migration\AbstractMigration;

class InitialStructure extends AbstractMigration
{
    /**
     * LinkQuick inital DB structure
     */
    public function change()
    {
        // Migration for table entry
        $table = $this->table('entry');
        $table
            ->addColumn('domain', 'string', array('limit' => 25))
            ->addColumn('url', 'string', array('limit' => 255))
            ->addColumn('title', 'string', array('limit' => 255))
            ->addColumn('code', 'string', array('limit' => 64))
            ->addColumn('Countdown', 'integer', array('limit' => 11))
            ->addColumn('ExpireDate', 'datetime', array('null' => true))
            ->addColumn('ExpiryAction', 'enum', array('values' => array('stop','free')))
            ->addColumn('created', 'datetime')
            ->addColumn('deleted', 'boolean', array('limit' => 1))
            ->addColumn('used', 'integer', array('default' => '0', 'signed' => false))
            ->create();


        // Migration for table users
        $table = $this->table('user');
        $table
            ->addColumn('settings', 'text', array('null' => true))
            ->addColumn('email', 'string', array('limit' => 128))
            ->addColumn('firstname', 'string', array('null' => true, 'limit' => 32))
            ->addColumn('lastname', 'string', array('null' => true, 'limit' => 32))
            ->addColumn('password', 'string', array('limit' => 40))
            ->addColumn('login', 'string', array('limit' => 32))
            ->addColumn('DatCreate', 'datetime', array())
            ->addColumn('DatSave', 'datetime', array('null' => true))
            ->addColumn('last_modifier_id', 'integer', array('null' => true, 'signed' => false))
            ->create();
    }
}
