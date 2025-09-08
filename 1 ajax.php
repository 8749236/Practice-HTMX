<?php

if ($_SERVER['REQUEST_URI'] === '/hx-get target.html') {
    header('Location: /tutorial/1 ajax.html');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && !isset($_GET['message'])) {
    include '1 ajax.html';
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    # there is a key value, that contains numeric string, parse as int and add 1.
    # if failed to parse as int, return 400 Bad Request
    if (isset($_POST['value'])) {
        $value = intval($_POST['value']);
        if ($value === 0 && $_POST['value'] !== '0') {
            echo 'Bad Request: key "value" is not a number';
            exit;
        }
        $value += 1;
        echo $value;
        exit;
    } else {
        echo 'Bad Request: missing key "value"';
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PUT') {
    # There are key "name" and "message", store them in sesson and return "Message saved".
    session_start();
    $raw = file_get_contents('php://input');
    parse_str($raw, $_PUT);
    echo "提交的数据: $raw<br>";
    if (isset($_PUT['name']) && isset($_PUT['message'])) {
        $_SESSION['name'] = $_PUT['name'];
        $_SESSION['message'] = $_PUT['message'];
        echo 'Message saved!';
        exit;
    } else {
        // http_response_code(400);
        echo 'Bad Request: missing key "name" or "message"';
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['message'])) {
    session_start();
    if (isset($_SESSION['name']) && isset($_SESSION['message'])) {
        echo '<div class="border padding">';
        echo '<strong>' . htmlspecialchars($_SESSION['name']) . '</strong>: ';
        echo htmlspecialchars($_SESSION['message']);
        echo '</div>';
        exit;
    } else {
        echo 'No message saved yet.';
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    sleep(1); // simulate delay
    session_start();
    $raw = file_get_contents('php://input');
    echo "提交的数据: $raw<br>";
    parse_str($raw, $_PATCH);

    if (empty($_PATCH)) {
        echo 'Bad Request: no data was submitted';
        exit;
    }

    if (isset($_PATCH['name']) && !empty($_PATCH['name'])) {
        $_SESSION['name'] = $_PATCH['name'];
        echo 'Name updated to ' . htmlspecialchars($_SESSION['name']);
    }

    if (isset($_PATCH['message']) && !empty($_PATCH['message'])) {
        $_SESSION['message'] = $_PATCH['message'];
        echo 'Message updated to ' . htmlspecialchars($_SESSION['message']);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    sleep(1); // simulate delay
    session_start();
    if (!isset($_SESSION['name']) && !isset($_SESSION['message'])) {
        echo 'Bad Request: no session available';
        exit;
    }

    // HTTP DELETE不支持请求体, 只能用查询参数，PHP会把查询参数放在$_GET里
    if (empty($_GET)) {
        echo 'Bad Request: no data was submitted';
        exit;
    }

    if (isset($_GET['name']) && $_GET['name'] === $_SESSION['name']) {
        // clear session
        unset($_SESSION['name']);
        unset($_SESSION['message']);
        echo 'Message cleared.';
        exit;
    } else {
        echo 'Bad Request: name does not match';
        exit;
    }
    exit;
}

