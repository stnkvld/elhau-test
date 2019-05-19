<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%results}}`.
 */
class m190519_082739_create_results_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('results', [
            'id' => $this->primaryKey(),
            'task_id' => $this->integer()->notNull(),
            'avito_url' => $this->string(255)->unique()->notNull(),
            'data' => $this->json()->notNull(),
            'created_at' => $this->dateTime()->defaultExpression('CURRENT_TIMESTAMP')->notNull(),
        ]);

        $this->addForeignKey(
            'FK_results-task_id',
            'results',
            'task_id',
            'tasks',
            'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK_results-task_id', 'results');

        $this->dropTable('results');
    }
}
