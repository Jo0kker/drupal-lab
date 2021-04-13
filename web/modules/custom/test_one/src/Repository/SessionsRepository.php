<?php

namespace Drupal\test_one\Repository;


use Drupal\Core\Database\Connection;

class SessionsRepository
{
    /**
     * @var Connection
     */
    private $database;

    public function __construct(Connection $database)
    {
        $this->database = $database;
    }

    public function findActiveSessions()
    {
        $query = $this->database->select('sessions', 's');
        $query->fields('s', array('hostname', 'timestamp'));
        $query->join('users_field_data', 'u', 'u.uid = s.uid');
        $query->fields('u', array('name'));
        return $query->execute()->fetchAll();
    }

    public function findLogStat()
    {
        $query = $this->database->select('test_one_user_stats', 's');
        $query->fields('s', array('type', 'timestamp'));
        $query->join('users_field_data', 'u', 'u.uid = s.uid');
        $query->fields('u', array('name'));
        return $query->execute()->fetchAll();
    }

    public function createLoginStat($uid)
    {
        $query = $this->database->insert('test_one_user_stats')
            ->fields([
                'type' => 'login',
                'timestamp' => \Drupal::time()->getRequestTime(),
                'uid' => (int)$uid
            ]);
        $query->execute();
    }

    public function createLogoutStat($uid)
    {
        $query = $this->database->insert('test_one_user_stats')
            ->fields([
                'type' => 'logout',
                'timestamp' => \Drupal::time()->getRequestTime(),
                'uid' => (int)$uid
            ]);
        $query->execute();
    }
}
