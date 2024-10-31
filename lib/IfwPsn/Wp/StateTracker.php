<?php

class IfwPsn_Wp_StateTracker
{
    const REQUEST_PARAM = 'ifw-wp-state-token';

    private static $_instance;

    private $token;

    public static function getInstance($token = null)
    {
        if (self::$_instance === null) {
            self::$_instance = new self($token);
        }
        return self::$_instance;
    }

    protected function __construct($token = null)
    {
        if (!empty($token)) {
            $this->token = $token;
        } elseif (!empty($_REQUEST[self::REQUEST_PARAM])) {
            $this->token = sanitize_text_field($_REQUEST[self::REQUEST_PARAM]);
        }
    }

    public function hasToken(): bool
    {
        return !empty($this->token);
    }

    /**
     * @return mixed|string
     */
    public function getToken()
    {
        return $this->token;
    }

    public function setState(IfwPsn_Wp_StateTracker_State $state, $ns = '', $expiration = 60): bool
    {
        return set_transient($this->getTransientName($ns), serialize($state), $expiration);
    }

    public function getState($ns = '')
    {
        $v = get_transient($this->getTransientName($ns));

        if (is_serialized($v)) {
            $v = unserialize($v);
            if ($v instanceof IfwPsn_Wp_StateTracker_State) {
                return $v;
            }
        }

        return new IfwPsn_Wp_StateTracker_State('undefined');
    }

    public function resetState($ns = ''): bool
    {
        return delete_transient($this->getTransientName($ns));
    }

    private function getTransientName($ns = ''): string
    {
        if (!empty($ns)) {
            $ns .= ':';
        }
        return sprintf('%s%s', $ns, $this->token);
    }
}