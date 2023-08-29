<?php
    // Создаем сокет
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    // Коннектимся к сокету
    $connect = socket_connect($socket, '127.0.0.1', 8090);
    
    // Получаем от сервера сообщение о успешном коннекте 
    echo socket_read($socket, 1024);

    while(true) {
        // Клиент вводит запрос
        $request = fgets(STDIN);

        // Отправляем запрос на сервер
        socket_write($socket, $request, strlen($request));

        // Получаем ответ от сервера
        $response = socket_read($socket, 1024);
        echo $response;

        // Если ответ - закрытие соединения, закрываем сокет
        if (($response == 'Close connection' . PHP_EOL) || $response == 'Close socket' . PHP_EOL) {
            socket_close($socket);
            break;
        }

        if ($response == 'First message' . PHP_EOL) {
            $response = socket_read($socket, 1024);
            echo $response;
        }
    }
