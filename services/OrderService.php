<?php

namespace app\services;

use app\interfaces\OrderLoaderInterface;
use app\models\Order;
use app\repositories\OrderImportRepository;

class OrderService
{
    private OrderLoaderInterface $orderLoader;
    private OrderImportRepository $orderImportRepository;

    private int $ordersUpdated = 0;
    private int $ordersError = 0;
    private array $errors = [];

    public function __construct(OrderLoaderInterface $orderLoader, OrderImportRepository $orderImportRepository)
    {
        $this->orderLoader = $orderLoader;
        $this->orderImportRepository = $orderImportRepository;
    }

    public function import(string $source): void
    {
        $data = json_decode($this->orderLoader->load($source), true);
        if (empty($data) || !is_array($data) || empty($data['orders'])) {
            throw new \LogicException('Некорректная информация по заказам');
        }

        $dateCurrent = (new \DateTime())->format('Y-m-d H:i:s');
        foreach ($data['orders'] as $orderData) {
            $order = new Order;
            $order->attributes = $orderData;
            $order->updated_at = $dateCurrent;
            if (!empty($orderData['items']) && is_array($orderData['items'])) {
                $order->items_count = count($orderData['items']);
            } else {
                $this->errors[$orderData['id']] = ['items' => 'Incorrect data for order items'];
                $this->ordersError++;
            }
            if ($order->validate()) {
                $this->orderImportRepository->addOrder($order);
                $this->ordersUpdated++;
            } else {
                if (isset($orderData['id'])) {
                    $this->errors[$orderData['id']] = $order->errors;
                } else {
                    $this->errors[] = $order->errors;
                }
                $this->ordersError++;
            }
        }
        $this->orderImportRepository->save();
    }

    public function getSuccessMessage(): string
    {
        return 'Обновлено заказов: ' . $this->ordersUpdated;
    }

    public function getErrorMessage(): string
    {
        return 'Заказов с некорректными данными: ' . $this->ordersError;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
