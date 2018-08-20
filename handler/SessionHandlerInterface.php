<?php
namespace asbamboo\session\handler;

/**
 * 继承SessionHandlerInterface，添加create_sid方法
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月10日
 */
interface SessionHandlerInterface extends \SessionHandlerInterface, \SessionIdInterface
{
}