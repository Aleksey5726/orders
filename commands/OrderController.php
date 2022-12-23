<?php

namespace app\commands;

use app\models\Order;
use app\services\OrderService;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class OrderController extends Controller
{
    public function actionUpdateNet(string $url): int
    {
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            $this->stdout('Некорректный url: ' . $url . "\n", Console::BG_RED);
            return ExitCode::IOERR;
        }
        /** @var OrderService $orderService */
        $orderService = \Yii::$container->get('order', ['type' => 'net']);
        return $this->update($orderService, $url);
    }

    public function actionUpdateLocal(string $path): int
    {
        /** @var OrderService $orderService */
        $orderService = \Yii::$container->get('order', ['type' => 'local']);
        return $this->update($orderService, $path);
    }

    public function actionInfo($orderId)
    {
        if (!filter_var($orderId, FILTER_VALIDATE_INT)) {
            $this->stdout('Некорректный id: ' . $orderId . "\n", Console::BG_RED);
            return ExitCode::IOERR;
        }
        $order = Order::findOne($orderId);
        if (!$order) {
            $this->stdout('Заказ ' . $orderId . ' не найден' . "\n", Console::BG_RED);
            return ExitCode::DATAERR;
        }
        $this->stdout(json_encode($order->oldAttributes, JSON_PRETTY_PRINT) . "\n");
        return ExitCode::OK;
    }

    private function update(OrderService $orderService, string $source): int
    {
        $orderService->import($source);

        $this->stdout($orderService->getSuccessMessage(), Console::BG_GREEN);

        if (count($orderService->getErrors()) > 0) {
            $this->stdout("\n" . $orderService->getErrorMessage() . "\n", Console::BG_RED);

            $this->stdout(json_encode($orderService->getErrors(), JSON_PRETTY_PRINT));
        }

        return ExitCode::OK;
    }
}
