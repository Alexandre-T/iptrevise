<?php
/**
 * This file is part of the IP-Trevise Application.
 *
 * PHP version 7.1
 *
 * (c) Alexandre Tranchant <alexandre.tranchant@gmail.com>
 *
 * @category Entity
 *
 * @author    Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @copyright 2017 Cerema — Alexandre Tranchant
 * @license   Propriétaire Cerema
 *
 */
namespace App\Bean;

use \DateTime as DateTime;

/**
 * Log bean to give some information about the last updates realized.
 *
 * @category Bean
 *
 * @author  Alexandre Tranchant <alexandre.tranchant@gmail.com>
 * @license Cerema 2017
 *
 */
class Log
{
    /**
     * @var int Version of the entity.
     */
    private $version = 0;
    /**
     * @var string update or create
     */
    private $action = '';
    /**
     * @var DateTime Date time of log
     */
    private $logged;
    /**
     * @var string username
     */
    private $username = '';
    /**
     * @var array data
     */
    private $data = [];
    /**
     * Has Log a version.
     *
     * @return bool
     */
    public function hasVersion(): bool
    {
        return !empty($this->version);
    }
    /**
     * Getter of version.
     *
     * @return int
     */
    public function getVersion(): int
    {
        return $this->version;
    }
    /**
     * Setter of version.
     *
     * @param int $version
     * @return Log
     */
    public function setVersion(int $version)
    {
        $this->version = $version;
        return $this;
    }
    /**
     * Getter of action.
     *
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }
    /**
     * Setter of action.
     *
     * @param string $action
     * @return Log
     */
    public function setAction(string $action)
    {
        $this->action = $action;
        return $this;
    }
    /**
     * Getter of DateTime.
     *
     * @return DateTime
     */
    public function getLogged(): DateTime
    {
        return $this->logged;
    }
    /**
     * Setter of Log time.
     *
     * @param DateTime $logged
     * @return Log
     */
    public function setLogged(DateTime $logged)
    {
        $this->logged = $logged;
        return $this;
    }
    /**
     * Is Log time initialized?
     *
     * @return bool
     */
    public function isLogged()
    {
        return $this->logged instanceof DateTime;
    }
    /**
     * Getter of username.
     *
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }
    /**
     * Setter of username.
     *
     * @param string $username
     * @return Log
     */
    public function setUsername(string $username = null)
    {
        if (is_null($username)) {
            $this->username = '';
        } else {
            $this->username = $username;
        }
        return $this;
    }
    /**
     * Getter of data.
     *
     * @return mixed
     */
    public function getData():array
    {
        return $this->data;
    }
    /**
     * Setter of data.
     *
     * @param array $data
     * @return Log
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }
}
