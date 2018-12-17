<?php
namespace asbamboo\session;

/**
 * Session 接口
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月9日
 */
interface SessionInterface
{
    /**
     * 设置当前会话 ID
     * session_id($id)
     *
     * @return SessionInterface
     */
    public function setId(string $id) : SessionInterface;

    /**
     * 返回当前会话 ID
     * session_id()
     *
     * @return SessionInterface
     */
    public function getId() : string;

    /**
     * 设置会话名称
     * session_name($name);
     *
     * @return SessionInterface
     */
    public function setName(string $name) : SessionInterface;

    /**
     * 返回会话名称
     * session_name()
     *
     * @return string
     */
    public function getName() : string;

    /**
     * 设置session value
     * $_SESSION[$name] = $value
     *
     * @param string $name
     * @param string|array $value
     * @return SessionInterface
     */
    public function set(string $name, $value) : SessionInterface;

    /**
     * 获取session value
     * 返回 $_SESSION[$name]
     *
     * @param string $name
     * @return mixed
     */
    public function get(string $name)/* : mixed*/;

    /**
     * 启动 session_start();
     *
     * @param array $options
     * @return bool
     */
    public function start(array $options=[]) : bool;

    /**
     * 释放session所有变量
     * 调用session_unset()
     *
     * @return void
     */
    public function unset() : void;

    /**
     * 保存session
     * 调用session_write_close();
     *
     * @return void
     */
    public function save() : void;
}