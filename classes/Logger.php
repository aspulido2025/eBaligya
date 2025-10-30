<?php
namespace App\Classes;

class Logger {
    const AUTH        = 'auth';
    const TRANSACTION = 'transaction';
    const COOKIE      = 'cookie';
    const SMS         = 'sms';
    const EMAIL       = 'email';
    // const ERROR       = 'error';
    // const REPORT      = 'report';
    // const SCAN        = 'scan';
    // const QUEUE       = 'queue';

    private $db;

    public function __construct(DB $db) {
        $this->db = $db;
    }

    /**
     * Generic log dispatcher
     */
    public function log(string $type, array $params): bool {
        switch ($type) {
            case self::AUTH:        return $this->logAuth($params);
            case self::TRANSACTION: return $this->logTransaction($params);
            case self::COOKIE:      return $this->logCookie($params);
            case self::SMS:         return $this->logSms($params);
            case self::EMAIL:       return $this->logEmail($params);
            // case self::ERROR:       return $this->logError($params);
            // case self::REPORT:      return $this->logReport($params);
            // case self::SCAN:        return $this->logScan($params);
            // case self::QUEUE:       return $this->logQueue($params);
            default: throw new \InvalidArgumentException("Invalid log type: $type");
        }
    }

    /**
     * Unified Insert and Index
     */
    private function insertAndIndex(string $table, string $type, string $sql, array $values, array $params): bool {
        $result = $this->db->exec($sql, $values);
        if ($result['affected'] > 0) {
            $this->addToIndex($type, $result['insertId'], $params);
            return true;
        }
        return false;
    }

    /**
     * Master index
     */
    private function addToIndex(string $type, int $refId, array $params): void {
        $sql = "INSERT INTO system_log_index ( log_type, ref_id, user_id, session_id, ipaddress, user_agent )
                VALUES ( :log_type, :ref_id, :user_id, :master_session, :ipaddress, :user_agent )";

        $this->db->exec($sql, [
            'log_type'   => $type,
            'ref_id'     => $refId,
            'user_id'    => $params['user_id'] ?? null,
            'master_session' => $params['master_session'] ?? null,
            'ipaddress'  => $params['ipaddress'] ?? null,
            'user_agent' => $params['user_agent'] ?? null,
        ]);
    }


    /**
     * Authentication logs
     */
    private function logAuth(array $params): bool {
        $sql = "INSERT INTO log_authentication ( user_id, turnout, entity, entity_id, ipaddress, user_agent, before_data, after_data, extra_context ) 
                VALUES ( :user_id, :turnout, :entity, :entity_id, :ipaddress, :user_agent, :before_data, :after_data, :extra_context )";

        return $this->insertAndIndex('log_authentication', self::AUTH, $sql, [
            'user_id'       => $params['user_id'],
            'turnout'       => $params['turnout'] ?? null,
            'entity'        => $params['entity'] ?? null,
            'entity_id'     => $params['entity_id'] ?? null,
            'ipaddress'     => $params['ipaddress'] ?? null,
            'user_agent'    => $params['user_agent'] ?? null,
            'before_data'   => isset($params['before_data']) ? json_encode($params['before_data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'after_data'    => isset($params['after_data']) ? json_encode($params['after_data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'extra_context' => isset($params['extra_context']) ? json_encode($params['extra_context'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
        ], $params);
    }

    /**
     * Transaction logs
     */
    private function logTransaction(array $params): bool {
        $sql = "INSERT INTO log_transaction ( user_id, turnout, entity, entity_id, ipaddress, user_agent, before_data, after_data, extra_context ) 
                VALUES ( :user_id, :turnout, :entity, :entity_id, :ipaddress, :user_agent, :before_data, :after_data, :extra_context )";

        return $this->insertAndIndex('log_transaction', self::TRANSACTION, $sql, [
            'user_id'       => $params['user_id'],
            'turnout'       => $params['turnout'] ?? null,
            'entity'        => $params['entity'] ?? null,
            'entity_id'     => $params['entity_id'] ?? null,
            'ipaddress'     => $params['ipaddress'] ?? null,
            'user_agent'    => $params['user_agent'] ?? null,
            'before_data'   => isset($params['before_data']) ? json_encode($params['before_data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'after_data'    => isset($params['after_data']) ? json_encode($params['after_data'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
            'extra_context' => isset($params['extra_context']) ? json_encode($params['extra_context'], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) : null,
        ], $params);
    }

    /**
     * SMS logs
     */
    private function logSms(array $params): bool {
        $sql = "INSERT INTO log_sms ( user_id, mobile_number, sms_string, status_code, ipaddress, user_agent ) 
                VALUES ( :user_id, :mobile_number, :sms_string, :status_code, :ipaddress, :user_agent )";

        return $this->insertAndIndex('log_sms', self::SMS, $sql, [
            'user_id'       => $params['user_id'],
            'mobile_number' => $params['mobile_number'] ?? null,
            'sms_string'    => $params['sms_string'] ?? null,
            'status_code'   => $params['status_code'] ?? null,
            'ipaddress'     => $params['ipaddress'] ?? null,
            'user_agent'    => $params['user_agent'] ?? null,
        ], $params);
    }

    /**
     * EMAIL logs
     */
    private function logEmail(array $params): bool {
        $sql = "INSERT INTO log_email ( user_id, email_address, email_string, sending_status, ipaddress, user_agent ) 
                VALUES ( :user_id, :email_address, :email_string, :sending_status, :ipaddress, :user_agent )";

        return $this->insertAndIndex('log_email', self::EMAIL, $sql, [
            'user_id'           => $params['user_id'],
            'email_address'     => $params['email_address'] ?? null,
            'email_string'      => $params['email_string'] ?? null,
            'sending_status'    => $params['sending_status'] ?? null,
            'ipaddress'         => $params['ipaddress'] ?? null,
            'user_agent'        => $params['user_agent'] ?? null,
        ], $params);
    }


    /**
     * Error logs
     */
    private function logError(array $params): bool {
        $sql = "INSERT INTO log_error (
                    message, file, line, stack_trace, extra_context
                ) VALUES (
                    :message, :file, :line, :stack_trace, :extra_context
                )";

        $values = [
            'message'       => $params['message'],
            'file'          => $params['file']  ?? null,
            'line'          => $params['line']  ?? null,
            'stack_trace'   => $params['stack_trace'] ?? null,
            'extra_context' => isset($params['extra_context']) ? json_encode($params['extra_context'], JSON_UNESCAPED_UNICODE) : null,
        ];

        // return $this->db->exec($sql, $values) > 0;
        return $this->db->exec($sql, $values)['affected'] > 0;
    }

    /**
     * Cookie logs
     */
    private function logCookie(array $params): bool {
        $sql = "INSERT INTO log_cookie (
                    user_id, selector, hashed_validator, ipaddress, user_agent, expires_at
                ) VALUES (
                    :user_id, :selector, :hashed_validator, :ipaddress, :user_agent, :expires_at
                )";
        $values = [
            'user_id'       => $params['user_id'],
            'selector'      => $params['selector'],
            'hashed_validator'    => $params['hashed_validator'],
            'ipaddress'     => $params['ipaddress'] ?? null,
            'user_agent'    => $params['user_agent'] ?? null,
            'expires_at'    => $params['expires_at'] ?? null
        ];

        // return $this->db->exec($sql, $values) > 0;
        return $this->db->exec($sql, $values)['affected'] > 0;
    }



    
}
