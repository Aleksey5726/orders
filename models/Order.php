<?php

namespace app\models;

use yii\db\ActiveRecord;

class Order extends ActiveRecord
{
    public $id;
    public $user_name;
    public $user_phone;
    public $warehouse_id;
    public $status;
    public $items_count;
    public $created_at;
    public $updated_at;

    public static function tableName()
    {
        return '{{orders}}';
    }

    public function rules()
    {
        return [
            [['user_name', 'user_phone', 'warehouse_id', 'status', 'items_count', 'created_at', 'updated_at'], 'required'],
            [['user_name'], 'string', 'length' => [1,100]],
            [['user_phone'], 'string', 'length' => [1,20]],
            [['warehouse_id', 'status', 'items_count', 'id'], 'integer'],
            [['created_at', 'updated_at'], 'datetime', 'format' => 'php:Y-m-d H:i:s']
        ];
    }
}
