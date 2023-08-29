<?php 
    echo 'SERVER START' . PHP_EOL;

    // Cоздаём сокет
    echo 'Socket create...' . PHP_EOL;
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

    if (false === $socket) {
        die('Error: ' . socket_strerror(socket_last_error()) . PHP_EOL);
    }
    
    // Привязываем сокет к IP и порту
    echo 'Socket bind...' . PHP_EOL;
    $bind = socket_bind($socket, '127.0.0.1', 8090);

    if (false === $bind) {
        die('Error: ' . socket_strerror(socket_last_error()) . PHP_EOL);
    }

    // Разрешаем использовать один порт для нескольких соединений
    echo 'Set options...' . PHP_EOL;
    $option = socket_set_option($socket, SOL_SOCKET, SO_REUSEADDR, 1);
    
    if (false === $option) {
        die('Error: ' . socket_strerror(socket_last_error()) . PHP_EOL);
    }

    // Слушаем сокет
    echo 'Listening socket...' . PHP_EOL;
    $listen = socket_listen($socket);
    if (false === $listen) {
        die('Error: ' . socket_strerror(socket_last_error()) . PHP_EOL);
    }

    while(true) {
        
        // Ожидаем подключения
        echo 'Waiting for connections...' . PHP_EOL;

        $connect = socket_accept($socket);
        if ($connect !== false) {
            
            // Клиент подключается к сокету, отправляем ему приветствие
            echo 'Client connected...' . PHP_EOL;
            echo 'Send message to client...' . PHP_EOL;
            socket_write($connect, 'Connection succesful!' . PHP_EOL);
            
            // Устанавливаем блокирующий режим на сокете на стороне сервера
            echo 'Set block connect...' . PHP_EOL;
            $block = socket_set_block($connect);
            if ($block) {
                echo 'Blocking mode set successfully...' . PHP_EOL;
            } else {
                die('Error: ' . socket_strerror(socket_last_error()) . PHP_EOL);
            }

            while (true) {
                // Читаем запрос клиента, выводим его на сервер
                $request = socket_read($connect, 1024);
                echo 'Client request: ' . $request;

                // Обрабатываем запрос клиента на закрытие соединения
                if ($request == 'exit' . PHP_EOL) {
                    echo 'Client closed connection' . PHP_EOL;
                    $response = 'Close connection' . PHP_EOL;
                    socket_write($connect, $response, strlen($response));
                    socket_close($connect);
                    break;
                }

                if ($request == 'close' . PHP_EOL) {
                    echo 'Client closed socket' . PHP_EOL;
                    $response = 'Close socket' . PHP_EOL;
                    socket_write($connect, $response, strlen($response));
                    socket_close($connect);
                    socket_close($socket);
                    break 2;
                }

                // Обрабатываем запросы клиента
                switch ($request) {
                    case 'double' . PHP_EOL:
                        $response = 'First message' . PHP_EOL;
                        socket_write($connect, $response, strlen($response));
                        sleep(1);
                        $response = 'Second message' . PHP_EOL;
                        socket_write($connect, $response, strlen($response));
                        break;
                    default:
                        $response = 'Unknown command' . PHP_EOL;
                        socket_write($connect, $response, strlen($response));
                        break;
                }
            }
        } else {
            echo 'Error: ' . socket_strerror(socket_last_error()) . PHP_EOL;
        }
    }


