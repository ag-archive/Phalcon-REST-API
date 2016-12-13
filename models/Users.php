<?php

namespace api;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Security;

class Users extends Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $firstname;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $lastname;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $password_hash;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $password_reset_token;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $oauth_client;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $oauth_client_user_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    public $email;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     * 0 = inactive
     * 1 = active
     * 2 = confirmed
     */
    public $status;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $created_at;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $updated_at;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    public $logged_at;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $avatar_path;

    /**
     *
     * @var integer
     * @Column(type="integer", length=1, nullable=false)
     */
    public $wrong_password_count;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    public $wrong_password_date;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("travel");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'users';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users[]|Users
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Users
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function createUser($data)
    {
        $user = new Users();
        $security = new Security();
        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->email = $data['email'];
        $user->password_hash = $security->hash($data['password']);
        $user->created_at = date('Y-m-d h:i:s',time());
        $user->logged_at = date('Y-m-d h:i:s',time());

        if ($user->save()){
            return $user;
        } else return false;
    }

    public static function getUserByEmail($email)
    {
        $user = Users::findFirst(
            [
                "email = :email:",
                "bind" => [
                    "email" => $email,
                ],
            ]
        );
        return $user;
    }

    public static function getUserByToken($token)
    {
        $user = UsersTokens::findFirst(
            [
                "access_token = :token:",
                "bind" => [
                    "token" => $token,
                ],
            ]
        );
        return $user;
    }

    public static function updateOnWrongPassword($email, $count, $attempt_date = false, $login_date = false)
    {
        $user = Users::findFirst(
            [
                "email = :email:",
                "bind" => [
                    "email" => $email,
                ],
            ]
        );
        $user->wrong_password_count = $count;
        $user->wrong_password_date = $attempt_date;
        $user->logged_at = $login_date;

        if ($user->save()){
            return true;
        } else return false;
    }

}
