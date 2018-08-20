<?php
namespace asbamboo\session\handler;

/**
 * Pdo session handler 目前仅支持mysql, mariadb。
 * @author 李春寅 <licy2013@aliyun.com>
 * @since 2018年3月10日
 */
class PdoHandler extends AbstractHandler
{
    /**
     * @var \PDO
     */
    private $Pdo;

    /**
     * session 存储的数据表
     * @var string
     */
    private $table;

    /**
     * session id column name
     * @var string
     */
    private $col_id;

    /**
     * session data column name
     * @var string
     */
    private $col_data;

    /**
     *  session data column name
     * @var string
     */
    private $col_time;

    /**
     * $options session 数据相关选项
     * @param \PDO $Pdo
     * @param array $options
     */
    public function __construct(\PDO $Pdo, array $options = [])
    {
        if(!is_null($Pdo)){
            $Pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            $this->setPdo($Pdo);
        }

        $this->table        = $options['table'] ?? 'session';
        $this->col_id       = $options['col_id'] ?? 'sid';
        $this->col_data     = $options['col_data'] ?? 'data';
        $this->col_time  = $options['col_time'] ?? 'time';
    }

    /**
     * {@inheritDoc}
     * @see \asbamboo\session\handler\AbstractHandler::open()
     */
    public function open($save_path, $session_name) : bool
    {
        $this->createTableIfNotExists();
        return parent::open($save_path, $session_name);
    }

    /**
     * {@inheritDoc}
     * @see \SessionHandler::close()
     */
    public function close()
    {
        return true;
    }

    /**
     *
     * {@inheritDoc}
     * @see \SessionHandler::read()
     */
    public function read(/*string*/ $session_id) : string
    {
        /*
         * 为了避免并发时可能session会被gc回收机制处理，首先第一步将session活跃时间修改为最新时间
         */
        $stmt   = $this->Pdo->prepare(
            "UPDATE `{$this->table}` SET `{$this->col_time}` = UNIX_TIMESTAMP() WHERE `{$this->col_id}` = :session_id"
        );
        $stmt->bindParam(':session_id', $session_id, \PDO::PARAM_STR);
        $stmt->execute();

        // 当session第一次初始化时，需要往数据库写入记录
        if($stmt->rowCount() == 0){
            try
            {
                $stmt   = $this->Pdo->prepare(
                    "INSERT INTO `{$this->table}` (`{$this->col_id}`, `{$this->col_data}`, `{$this->col_time}`) VALUES (:session_id, '', UNIX_TIMESTAMP())"
                );
                $stmt->bindParam(':session_id', $session_id, \PDO::PARAM_STR);
                $stmt->execute();
            }catch(\PDOException $e){
                //PDOException: SQLSTATE[23000]: Duplicate entry 23000是唯一索引已经被添加过。
                //如果这个session_id已经存在的话，可能客户端连续发起请求，这时，前面的update sql修改的行数也是0。
                if($e->getCode() != '23000'){
                    throw $e;
                }
            }
        }

        /*
         * 读取session
         */
        $stmt   = $this->Pdo->prepare(
            "SELECT `{$this->col_data}` FROM `{$this->table}` WHERE `{$this->col_id}` = :session_id"
        );

        $stmt->bindParam(':session_id', $session_id, \PDO::PARAM_STR);
        $stmt->execute();

        $session = $stmt->fetchColumn();
        return $session;
    }

    /**
     *
     * {@inheritDoc}
     * @see \SessionHandler::write()
     */
    public function write(/*string*/ $session_id, /*string*/ $session_data)
    {
        $stmt   = $this->Pdo->prepare(
            "UPDATE $this->table SET $this->col_data = :session_data, $this->col_time = UNIX_TIMESTAMP() WHERE {$this->col_id} = :session_id"
        );
        $stmt->bindParam(':session_id', $session_id, \PDO::PARAM_STR);
        $stmt->bindParam(':session_data', $session_data, \PDO::PARAM_STR);
        $stmt->execute();

        return true;
    }

    /**
     *
     * {@inheritDoc}
     * @see \asbamboo\session\handler\AbstractHandler::destroy()
     */
    public function destroy(/*string*/ $session_id)
    {
        $stmt = $this->Pdo->prepare("DELETE FROM `{$this->table}` WHERE `{$this->col_id}` = :session_id");
        $stmt->bindParam(':session_id', $session_id, \PDO::PARAM_STR);
        $stmt->execute();

        return parent::destroy($session_id);
    }

    /**
     * {@inheritDoc}
     * @see \SessionHandler::gc()
     */
    public function gc(/*int*/ $maxlifetime)
    {
        $stmt   = $this->Pdo->prepare("DELETE FROM `{$this->table}` WHERE `{$this->col_time}` < UNIX_TIMESTAMP() - :maxlifetime");
        $stmt->bindParam(':maxlifetime', $maxlifetime, \PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * 活跃session总数量
     */
    public function count()
    {
        $stmt   = $this->Pdo->prepare(
            "SELECT count(*) FROM `{$this->table}`"
        );
        $stmt->execute();
        return $stmt->fetchColumn();
    }

    /**
     *
     * @param \PDO $Pdo
     * @throws \RuntimeException
     * @return self
     */
    private function setPdo(\PDO $Pdo) : self
    {
        $driver = $Pdo->getAttribute(\PDO::ATTR_DRIVER_NAME);
        if($driver != 'mysql'){
            throw new \RuntimeException("session 组件不支持的 PDO DRIVER{$driver}.");
        }
        $this->Pdo  = $Pdo;

        return $this;
    }

    /**
     * 创建数据表
     */
    public function createTableIfNotExists() : void
    {
        $this->Pdo->exec("
            CREATE TABLE IF NOT EXISTS `{$this->table}` (
                `{$this->col_id}` CHAR(128) NOT NULL PRIMARY KEY,
                `{$this->col_data}` TEXT NOT NULL,
                `{$this->col_time}` INTEGER UNSIGNED NOT NULL DEFAULT '0'
            ) COLLATE utf8_bin, ENGINE = InnoDB
        ");
    }
}
