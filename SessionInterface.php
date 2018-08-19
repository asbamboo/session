<?php
namespace dolibuild\session;

/**
 * Session 接口
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月9日
 */
interface SessionInterface
{
    /**
     * 设置session value
     * $_SESSION[$name] = $value
     * @param string $name
     * @param string|array $value
     * @return self
     */
    public function set(string $name, $value) : self;

    /**
     * 获取session value
     * 返回 $_SESSION[$name]
     * @param string $name
     */
    public function get(string $name);

    /**
     * 启动 session_start();
     * @param array $options
     * @return bool
     */
    public function start(array $options=[]) : bool;

    /**
     * 保存session
     */
    public function save() : bool;
}