<?php

class IfwPsn_Wp_StateTracker_State
{
    private $state;

    /**
     * @var \ArrayObject
     */
    private $data;



    public function __construct(string $state)
    {
        $this->state = $state;
    }

    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param array $data
     * @return \ArrayObject
     */
    private function initData(array $data = []): \ArrayObject
    {
        if (!($this->data instanceof \ArrayObject) || !empty($data)) {
            $this->data = new \ArrayObject($data);
        }
        return $this->data;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setData($key, $value)
    {
        $this->initData()->offsetSet($key, $value);
    }

    /**
     * @param $key
     */
    public function unsetData($key)
    {
        $this->initData()->offsetUnset($key);
    }

    public function resetData()
    {
        $this->exchangeData([]);
    }

    /**
     * @param array|object $array
     * @return array
     */
    public function exchangeData($array)
    {
        return $this->initData()->exchangeArray($array);
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasData($key = null)
    {
        if ($key !== null) {
            return $this->initData()->offsetExists($key);
        } else {
            return $this->countData() > 0;
        }
    }

    /**
     * @param $key
     * @return false|mixed
     */
    public function getData($key)
    {
        return $this->hasData($key) ? $this->initData()->offsetGet($key) : null;
    }

    /**
     * @return array
     */
    public function getAllData(): array
    {
        return $this->initData()->getArrayCopy();
    }

    /**
     * @return string
     */
    public function getAllDataJsonEncoded(): string
    {
        return json_encode($this->getAllData());
    }

    /**
     * @return int
     */
    public function countData()
    {
        return $this->initData()->count();
    }

    /**
     * @param $value
     * @return void
     */
    public function addData($value)
    {
        $this->setData(null, $value);
    }

    public function __serialize()
    {
        $data = $this->getAllData();
        $data['state'] = $this->getState();
        return $data;
    }

    public function __unserialize(array $data)
    {
        if (!empty($data['state'])) {
            $this->state = $data['state'];
            unset($data['state']);
        }

        $this->initData($data);
    }
}