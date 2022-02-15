<?php
namespace asbamboo\session\_test\handler;

use PHPUnit\Framework\TestCase;
use asbamboo\session\handler\PdoHandler;

/**
 * Session pdo Handler Test
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月10日
 */
class PdoHandlerTest extends TestCase
{
    public $pdo;
    public $handler;

    public function setUp()
    {
        try{
            $this->pdo      = new \PDO('mysql:host=127.0.0.1;dbname=asbamboo_test', 'root', 'root');
        }catch(\PDOException $e){
            if($e->getCode() == '1049'){
                print "\r\n Session pdo handler test 请先创建本地数据库 new PDO('mysql:host=127.0.0.1;dbname=asbamboo_test', 'root', 'root') \r\n";
            }
        }
        $this->handler  = new PdoHandler($this->pdo);
    }

    public function testOpen()
    {
        if(session_status() != PHP_SESSION_ACTIVE){
            session_start();
        }
        $this->assertTrue($this->handler->open('','sid'));
    }

    public function testReadBeforeWrite()
    {
        $sid        = session_create_id('asbamboo-unitest');

        session_id($sid);
        $this->assertEquals($this->handler->read($sid), '');
    }

    public function testWrite()
    {
        $session['write_time']  = time();
        $session['sid']         = session_id();

        $_SESSION               = $session;
        $encoded_session        = session_encode();

        $this->assertTrue($this->handler->write($session['sid'], $encoded_session));

        session_write_close();

        return $session;
    }

    /**
     * @depends testWrite
     */
    public function testReadAfterWrite($session)
    {
        session_start();
        $sid    = $session['sid'];
        $sesson = $this->handler->read($sid);
        session_decode($sesson);
        $this->assertEquals($_SESSION['write_time'], $session['write_time']);
    }

    public function testDestroy()
    {
        $sid    = session_id();
        $this->assertTrue($this->handler->open('','sid'));
        $this->assertTrue($this->handler->destroy($sid));
    }

    public function testClose()
    {
        $this->assertTrue($this->handler->close());
    }

    public function testGc()
    {
        $sid    = session_id();
        $this->assertTrue($this->handler->gc(0));
    }

    public function testCount()
    {
        $sid    = session_id();
        $this->assertGreaterThanOrEqual(0, $this->handler->count());
    }
}