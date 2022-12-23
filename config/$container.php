<?php
    YII::$container->set('order', function ($container, $params, $config) {
        $orderLoader = null;
        if ($params['type'] == 'net') {
            $orderLoader = new \app\services\OrderLoaderHttpService();
        } elseif ($params['type'] == 'local') {
            $orderLoader = new \app\services\OrderLoaderFileService(YII::$app->getBasePath(), YII::$app->params['storagePath']);
        } else {
            throw new \LogicException('Incorrect action for order import');
        }

        return new \app\services\OrderService($orderLoader, new \app\repositories\OrderImportRepository(YII::$app->getDb()));
    });
