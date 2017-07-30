<?php

use yii\db\Migration;
use app\modules\Gallery\repositories\entities\{Category, Image};

class m170422_210446_installDB extends Migration
{
    public function up()
    {
        $this->createTable((new ReflectionClass(new Category()))->getShortName(), [
            'id' => $this->primaryKey()->notNull(),
            'slug' => $this->string(15)->notNull()->unique(),
            'name' => $this->string(30)->notNull(),
            'accessibilityStatus'=> $this->integer(1)->notNull()
        ]);
        $this->createTable((new ReflectionClass(new Image()))->getShortName(), [
            'id' => $this->primaryKey()->notNull(),
            'author' => $this->string(30)->notNull(),
            'name' => $this->string(30)->notNull(),
            'categoryId' => $this->integer()->notNull(), 
            'accessibilityStatus'=> $this->integer(1)->notNull(),
            'uploadDate' => $this->string()->notNull()
        ]);
        //$this->add
        $this->addForeignKey('fk-'.(new ReflectionClass(new Image()))->getShortName().'-category_id', 
                (new ReflectionClass(new Image()))->getShortName(), 
                'categoryId',  
                (new ReflectionClass(new Category()))->getShortName(),
                'id'
            );
    }

    public function down()
    {
        echo "m170422_210446_installDB cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
