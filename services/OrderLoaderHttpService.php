<?php

namespace app\services;

use app\exceptions\OrderLoadException;
use app\interfaces\OrderLoaderInterface;
use yii\db\Exception;

class OrderLoaderHttpService implements OrderLoaderInterface
{
    private CONST REQUEST_TIMEOUT = 5000;

    public function load(string $url): string
    {
        $client = curl_init();
        try {
            curl_setopt($client, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($client, CURLOPT_URL,$url);
            $result=curl_exec($client);
            curl_setopt($client,CURLOPT_TIMEOUT, self::REQUEST_TIMEOUT);
            curl_close($client);
            return $result;
        } catch (Exception $exception) {
            curl_close($client);
            throw new OrderLoadException($exception->getMessage());
        }
    }
}
