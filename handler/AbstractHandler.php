<?php
namespace asbamboo\session\handler;

/**
 * Session Abstract Handler
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月10日
 */
class AbstractHandler extends \SessionHandler implements SessionHandlerInterface
{
    /**
     * @var string
     */
    protected $session_name;
    
    /**
     * {@inheritDoc}
     * @see \SessionHandler::open()
     */
    public function open($save_path, $session_name)
    {
        $this->session_name = $session_name;
        
        if (!headers_sent() && !ini_get('session.cache_limiter') && '0' !== ini_get('session.cache_limiter')){
            header(sprintf('Cache-Control: max-age=%d, private, must-revalidate', 60 * (int) ini_get('session.cache_expire')));
        }
        
        return true;
    }
    
    /**
     * {@inheritDoc}
     * @see \SessionHandler::destroy()
     */
    public function destroy($session_id)
    {
        if (!headers_sent() && ini_get('session.use_cookies')) {
            if (!$this->session_name) {
                throw new \LogicException(sprintf('Session name 不能为空, 是不是没有调用 "parent::open()" in "%s"?.', get_class($this)));
            }
            $session_cookie         = sprintf(' %s=', urlencode($this->session_name));
            $session_cookie_with_id = sprintf('%s%s;', $session_cookie, urlencode($session_id));
            $session_cookie_found   = false;
            $other_cookies          = array();
            foreach (headers_list() as $h) {
                if (0 !== stripos($h, 'Set-Cookie:')) {
                    continue;
                }
                if (11 === strpos($h, $session_cookie, 11)) {
                    $session_cookie_found = true;
                    
                    if (11 !== strpos($h, $session_cookie_with_id, 11)) {
                        $other_cookies[] = $h;
                    }
                } else {
                    $other_cookies[] = $h;
                }
            }
            if ($session_cookie_found) {
                header_remove('Set-Cookie');
                foreach ($other_cookies as $h) {
                    header('Set-Cookie:'.$h, false);
                }
            } else {
                setcookie($this->session_name, '', 0, ini_get('session.cookie_path'), ini_get('session.cookie_domain'), ini_get('session.cookie_secure'), ini_get('session.cookie_httponly'));
            }
        }
        
        return true;
    }
}