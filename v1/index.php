<?php

use Phalcon\Loader;
use Phalcon\Mvc\Micro;
use Phalcon\Di\FactoryDefault;

use Phalcon\Http\Request;
use Phalcon\Http\Response;

use api\Users;
use api\UsersTokens;

use Phalcon\Db\Adapter\Pdo\Mysql as PdoMysql;

// use Loader() to autoload our models
$loader = new Loader();

$loader->registerNamespaces(
    [
        'api' => __DIR__ . "/../models/",
    ]
);
$loader->register();

$di = new FactoryDefault();
// DB setup (database interface - di)
$di->set(
    "db",
    function () {
        return new PdoMysql(require('../config/db.php'));
    }
);

// create application with di
$app = new Micro($di);

// login user by email and password
$app->post(
    '/users/login',
    function () use ($app) {
        $request = $app->request->getJsonRawBody();
        $response = new Response();

        //check if correct params are given
        if ($request->email && $request->password){
            $search = Users::getUserByEmail($request->email);

            $count = $search->wrong_password_count;
            $date = $search->wrong_password_date;

            //check if no delay is set
            if ($date < date('Y-m-d h:i:s',time())){
                //email not found
                if ($search === false) {
                    $response->setJsonContent(
                        [
                            'status' => "EMAIL_NOT_FOUND",
                            'data' => [
                                'error' => 'Email not found in our database',
                            ]
                        ]
                    );
                } elseif (!$this->security->checkHash($request->password, $search->password_hash)) { //wrong password
                    if ($count < 5){
                        $count++;
                        $status = Users::updateOnWrongPassword($request->email, $count);

                        if (!$status) {
                            return json_encode($status->success()); //cannot write changes!
                        }
                    } else {
                        $count++;
                        $wrong_password_date = date('Y-m-d h:i:s', time() + 60*3);
                        $status = Users::updateOnWrongPassword($request->email, $count, $wrong_password_date);

                        if (!$status) {
                            return 'cannot write changes!';
                        }
                    }
                    $response->setJsonContent(
                        [
                            'status' => "WRONG_PASSWORD",
                            'data' => [
                                'error' => "Your password doesn't match",
                            ],
                        ]
                    );
                } else { //everything is OK
                    //clear wrong attempts counter and generate token
                    $count = 0;
                    $wrong_password_date = NULL;
                    $logged_at = date('Y-m-d h:i:s',time());
                    $status = Users::updateOnWrongPassword($request->email, $count, $wrong_password_date, $logged_at);

                    if (!$status) {
                        return 'cannot write changes!';
                    }

                    $users_tokens = UsersTokens::addToken($search);

                    if (!$status || !$users_tokens) {
                        return 'cannot write changes!';
                    }

                    $response->setJsonContent(
                        [
                            'status' => 'OK',
                            'data'   => [
                                'access_token' => $users_tokens->access_token,
                            ]
                        ]
                    );
                }
            } else {
                // date for delay + 3 minutes
                $count++;
                $wrong_password_date = date('Y-m-d h:i:s', time() + 60*3);
                $status = Users::updateOnWrongPassword($request->email, $count, $wrong_password_date);

                if (!$status) {
                    return 'cannot write changes!';
                }

                $response->setJsonContent(
                    [
                        'status' => "TOO_MANY_ATTEMPTS",
                        'data' => [
                            'error' => 'Too many attempts. Delay was set. Please try again later (in 3 min).',
                        ]
                    ]
                );
            }

        } else {
            $response->setJsonContent(
                [
                    'status' => "UNAUTHORIZED",
                    'data' => [
                        'error' => 'wrong request',
                    ]
                ]
            );
        }

        return $response;
    }
);

// logout user by token
$app->delete(
    '/users/logout',
    function () use ($app) {
        $request = $app->request->getJsonRawBody();
        $response = new Response();

        //check if correct params are given
        if ($request->access_token){
            $status = UsersTokens::removeToken($request->acces_token);
            if ($status === true) {
                $response->setJsonContent(
                    [
                        'status' => "OK",
                        'data' => [
                            'result' => 'Request processed',
                        ]
                    ]
                );
            } else {
                $response->setJsonContent(
                    [
                        'status' => "ERROR",
                        'data' => [
                            'error' => 'wrong request'
                        ]
                    ]
                );
            }
        } else {
            $response->setJsonContent(
                [
                    'status' => "UNAUTHORIZED",
                    'data' => [
                        'error' => 'wrong request',
                    ]
                ]
            );
        }

        return $response;
    }
);

// register new user
$app->post(
    "/users/signup",
    function () use ($app) {
        $request = $app->request->getJsonRawBody();
        $response = new Response();
        //check if correct params are given
        if ($request->firstname && $request->lastname && $request->email && $request->password){
            //check criteria
            if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
                $response->setJsonContent(
                    [
                        'status' => "EMAIL_WRONG_FORMAT",
                        'data' => [
                            'error' => "Your email doesn't match criteria (email@domain.com)",
                        ]
                    ]
                );
                return $response;
            } elseif (strlen($request->password)<6) {
                $response->setJsonContent(
                    [
                        'status' => "PASSWORD_WRONG_FORMAT",
                        'data' => [
                            'error' => "Your password doesn't match criteria (>=6 symbols)",
                        ]
                    ]
                );
                return $response;
            }
            $search = Users::getUserByEmail($request->email);
            //email not found, go register
            if (!$search) {
                $data = [
                    'firstname' => $request->firstname,
                    'lastname' => $request->lastname,
                    'email' => $request->email,
                    'password' => $request->password,
                ];
                $user = Users::createUser($data);
                $users_tokens = UsersTokens::addToken($user);

                if (!$user || !$users_tokens) {
                    return 'cannot write changes!';
                }

                $response->setJsonContent(
                    [
                        'status' => 'OK',
                        'data'   => [
                            'access_token' => $users_tokens->access_token,
                        ]
                    ]
                );

            } else {
                $response->setJsonContent(
                    [
                        'status' => "EMAIL_USED",
                        'data' => [
                            'error' => 'This email is already used',
                        ]
                    ]
                );
            }
        } else {
            $response->setJsonContent(
                [
                    'status' => "UNAUTHORIZED",
                    'data' => [
                        'error' => 'wrong request',
                    ]
                ]
            );
        }

        return $response;
    }
);

$app->notFound(
    function () use ($app) {
        $app->response->setStatusCode(404, "Not Found");

        $app->response->sendHeaders();

        echo "Sorry, no matches were found for your query. Please check your request URL";
    }
);

$app->handle();