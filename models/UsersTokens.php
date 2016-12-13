<?php

namespace api;

use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Message;
use Phalcon\Security;

class UsersTokens extends Model
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
    public $access_token;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $user_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    public $user_agent;

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
        return 'users_tokens';
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

    public static function findLast($parameters = null)
    {
        return parent::findLast($parameters);
    }

    public static function addToken($user)
    {
        $security = new Security;
        $users_token = new UsersTokens();
        $users_token->access_token = $security->hash($user->email . time());
        $users_token->user_id = $user->id;
        $users_token->user_agent = $_SERVER['HTTP_USER_AGENT'];
        if ($users_token->save()){
            return $users_token;
        } else return false;
    }
    public static function removeToken($token)
    {
        $users_token = UsersTokens::find(['access_token'=>$token]);
        if ($users_token->delete()) {
            return true;
        } else {
            return false;
        }
    }

}
