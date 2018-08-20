<?php
namespace asbamboo\session\_test;

use PHPUnit\Framework\TestCase;
use asbamboo\session\Session;
use asbamboo\session\handler\PdoHandler;

/**
 * Session TEST
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月10日
 */
class SessionTest extends TestCase
{   
    public function testStart()
    {
        session_write_close();
        $session        = new Session();
        
        $started    = $session->start();
        $this->assertTrue($started);
        return $session;
    }
    
    /**
     * @depends testStart
     */
    public function testSet($session)
    {
        $test_now   = time();
        $session->set('test_now', $test_now);
        $this->assertEquals($_SESSION['test_now'], $test_now);
        return $test_now;
    }
    
    /**
     * @depends testStart
     * @depends testSet
     */
    public function testGet($session, $test_now)
    {
        $this->assertEquals($_SESSION['test_now'], $session->get('test_now'));
        $this->assertEquals($test_now, $session->get('test_now'));
    }

    /**
     * @depends testStart
     */
    public function testSave($session)
    {
        $session->save();
        $this->assertTrue(true);
    }
    
    /**
     * @depends testStart
     */
    public function testUnset($session)
    {
        $session->unset();
        $this->assertTrue(true);
    }
}