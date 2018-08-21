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
     */
    public function __construct(SessionHandlerInterface $sessionHandler = NULL)
    {
        session_register_shutdown();

        if($sessionHandler){
            session_set_save_handler($sessionHandler);
        }
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