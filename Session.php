<?php
namespace asbamboo\session;

use asbamboo\session\handler\SessionHandlerInterface;

/**
 * session 管理 自定义session handler
 *
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月10日
 */
class Session implements SessionInterface
{
    /**
     *
     * @param SessionHandlerInterface $sessionHandler
     * @param array $option
     * @param boolean $is_start
     */
    public function __construct(SessionHandlerInterface $sessionHandler = NULL, array $option = [], $is_start = true)
    {
        if($sessionHandler){
            session_set_save_handler($sessionHandler);
        }
        if($is_start == true){
            $this->start($option);
        }
        session_register_shutdown();
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\SessionInterface::setId()
     */
    public function setId(string $id) : SessionInterface
    {
        session_id($id);
        return $this;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\SessionInterface::getId()
     */
    public function getId() : string
    {
        return session_id();
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\SessionInterface::setName()
     */
    public function setName(string $name) : SessionInterface
    {
        session_name($name);
        return $this;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\SessionInterface::getName()
     */
    public function getName() : string
    {
        return session_name();
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\SessionInterface::set()
     */
    public function set(string $name, $value) : SessionInterface
    {
        $_SESSION[$name]    = $value;
        return $this;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\SessionInterface::get()
     */
    public function get(string $name)
    {
        return $_SESSION[$name] ?? null;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\SessionInterface::start()
     */
    public function start(array $option = []) : bool
    {
        if(in_array(session_status(), [PHP_SESSION_ACTIVE])){
            return true;
        }
        return session_start($option);
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\SessionInterface::unset()
     */
    public function unset() : void
    {
        session_unset();
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\SessionInterface::save()
     */
    public function save() : void
    {
        session_write_close();
    }
}