<?php

class CreatePointsTable extends Ruckusing_Migration_Base
{
    public function up()
    {
        $t = $this->create_table('points', ['id' => true, 'options' => 'Engine=InnoDB']);
        $t->column('name', 'string', ['limit' => 50]);
        $t->column('address', 'string', ['limit' => 255]);
        $t->column('created_at', 'datetime');
        $t->column('updated_at', 'datetime');
        $t->finish();
    }

    public function down()
    {
        $this->drop_table("points");
    }
}
