<?php
function register(array $data)
{
    $values = [
        $data['name'],
        $data['email'],
        password_hash($data['password'], PASSWORD_ARGON2ID),
        (new DateTime())->format('Y-m-d H:i:s')
    ];
    return insert($values);
}

function validate(array $request)
{
    $errors = [];
    if (!isset($request['email']) || strlen($request['email']) == 0) {
        $errors[]['email'] = 'Email не указан';
    } elseif (!filter_var($request['email'], FILTER_VALIDATE_EMAIL)) {
        $errors[]['email'] = 'Неправильный формат email';
    } elseif (strlen($request['email']) < 4) {
        $errors[]['email'] = 'Email должен быть больше 4х символов';
    } elseif (isEmailAlreadyExists($request['email'])) {
        $errors[]['email'] = 'Email уже используется';
    }
    if (!isset($request['name']) || empty($request['name'])) {
        $errors[]['name'] = 'Имя не указано';    }
    if (!isset($request['password']) || empty($request['password'])) {
        $errors[]['password'] = 'Пароль не указан';
    }
    if (!isset($request['repeat-password']) || empty($request['repeat-password'])) {

        $errors[]['repeat-password'] = 'Нужно повторить пароль';

    } elseif ((isset($request['password']) && isset($request['repeat-password'])) && ($request['password'] != $request['repeat-password'])) {

        $errors[]['repeat-password'] = 'Пароли не совпадают';
    }
    return $errors;}
function isEmailAlreadyExists(string $email)
{
    if (getUserByEmail($email)) {
        return true;
    }
    return false;
}