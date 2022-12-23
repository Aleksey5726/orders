<?php

namespace app\repositories;

use app\models\Order;
use yii\db\Connection;

class OrderImportRepository
{
    private const CHUNK_SIZE = 500;

    private Connection $db;

    private string $queryFormat = '';
    private array $orders = [];
    private int $currentIndex = 0;

    public function __construct(Connection $db)
    {
        $this->db = $db;

        $this->queryFormat = "
            Insert into orders (id, user_name, user_phone, warehouse_id, status, items_count, created_at, updated_at)
            VALUES 
                %s
            ON DUPLICATE KEY UPDATE
                user_name = VALUES(user_name),
                user_phone = VALUES(user_phone),
                warehouse_id = VALUES(warehouse_id),
                status = VALUES(status),
                items_count = VALUES(items_count),
                created_at = VALUES(created_at),
                updated_at = VALUES(updated_at)
        ";
    }

    public function addOrder(Order $order): void
    {
        if ($this->currentIndex === 0) {
            $this->orders[] = ['query' => '', 'params' => []];
        }

        $orderCount = count($this->orders) - 1;
        $data = &$this->orders[$orderCount];

        $this->add($order->attributes, $data['query'], $data['params'], $this->currentIndex);

        $this->currentIndex++;
        if ($this->currentIndex === self::CHUNK_SIZE) {
            $this->currentIndex = 0;
        }
    }

    public function save(): void
    {
        $transaction = $this->db->beginTransaction();
        try {
            foreach ($this->orders as $index => $order) {
                $query = sprintf($this->queryFormat, $order['query']);
                $this->db->createCommand($query, $order['params'])->execute();
                unset($this->orders[$index]);
            }
            $transaction->commit();
        } catch (\Exception $exception) {
            $transaction->rollBack();
            throw $exception;
        }
    }

    private function add(array $order, string &$query, array &$params, int $index): void
    {
        $query .= $query ? ', ' : '';
        $query .= "(
                    :id{$index}, 
                    :user_name{$index}, 
                    :user_phone{$index},
                    :warehouse_id{$index},
                    :status{$index},
                    :items_count{$index},
                    :created_at{$index},
                    :updated_at{$index}
                )";
        $params[":id{$index}"] = $order['id'];
        $params[":user_name{$index}"] = $order['user_name'];
        $params[":user_phone{$index}"] = $order['user_phone'];
        $params[":warehouse_id{$index}"] = $order['warehouse_id'];
        $params[":status{$index}"] = $order['status'];
        $params[":items_count{$index}"] = $order['items_count'];
        $params[":created_at{$index}"] = $order['created_at'];
        $params[":updated_at{$index}"] = $order['updated_at'];
    }
}
