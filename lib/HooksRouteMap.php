<?php

class HooksRouteMap extends RESTAPI\RouteMap
{
    /**
     * @get /hooks
     */
    public function getHooks()
    {
        $this->hooks = Hook::findBySQL("user_id = ?", array($GLOBALS['user']->id));
        $output = array();
        foreach ($this->hooks as $hook) {
            $data = $hook->toArray();
            $data['activated'] = (bool) $data['activated'];
            $data['cronjob'] = (bool) $data['cronjob'];
            $data['editable'] = (bool) $data['editable'];
            $data['chdate'] = (int) $data['chdate'];
            $data['mkdate'] = (int) $data['mkdate'];
            $output[] = $data;
        }

        return $output;
    }

    /**
     * @get /hooks/:hook_id
     *
     * @param $hook_id : ID of the hook
     */
    public function getHookData($hook_id)
    {
        $this->hook = new Hook($hook_id);
        if (!$this->hook->isNew() && $this->hook['user_id'] !== $GLOBALS['user']->id) {
            $this->halt(403);
        }
        if ($this->hook->isNew()) {
            $this->notFound();
        }

        $data = $this->hook->toArray();
        $data['activated'] = (bool) $data['activated'];
        $data['cronjob'] = (bool) $data['cronjob'];
        $data['editable'] = (bool) $data['editable'];
        $data['chdate'] = (int) $data['chdate'];
        $data['mkdate'] = (int) $data['mkdate'];
        return $data;
    }

    /**
     * @delete /hooks/:hook_id
     *
     * @param $hook_id : ID of the hook
     */
    public function deleteHookData($hook_id)
    {
        $this->hook = new Hook($hook_id);
        if ($this->hook['user_id'] !== $GLOBALS['user']->id) {
            $this->halt(403);
        }
        if ($this->hook->isNew()) {
            $this->notFound();
        }

        if ($this->hook->delete()) {
            return "ok";
        } else {
            $this->halt(500);
            return;
        }
    }

    /**
     * @put /hooks/:hook_id
     *
     * @param $hook_id : ID of the hook
     */
    public function putHookData($hook_id)
    {
        $this->hook = new Hook($hook_id);
        if (!$this->hook->isNew() && $this->hook['user_id'] !== $GLOBALS['user']->id) {
            $this->halt(403);
        }
        if ($this->hook->isNew()) {
            $this->notFound();
        }

        if ($this->data['name']) {
            $this->hook['name'] = $this->data['name'];
        }
        if ($this->data['activated'] !== null) {
            $this->hook['activated'] = (int) $this->data['activated'];
        }
        if ($this->data['cronjob'] !== null) {
            $this->hook['cronjob'] = (int) $this->data['cronjobs'];
        }
        if ($this->data['if_type'] !== null) {
            $this->hook['if_type'] = $this->data['if_type'];
        }
        if ($this->data['if_settings'] !== null) {
            $this->hook['if_settings'] = $this->data['if_settings'];
        }
        if ($this->data['then_type'] !== null) {
            $this->hook['then_type'] = $this->data['then_type'];
        }
        if ($this->data['then_settings'] !== null) {
            $this->hook['then_settings'] = $this->data['then_settings'];
        }
        if ($this->data['editable'] !== null) {
            $this->hook['editable'] = (int) $this->data['editable'];
        }
        $consumer = RESTAPI\Consumer\Base::detectConsumer();
        if ($consumer && $consumer->getId()) {
            $this->hook['consumer_id'] = $consumer->getId();
        }
        $this->hook->store();

        return $this->hook->toArray();
    }

    /**
     * @post /hooks
     */
    public function postHook()
    {
        $this->hook = new Hook();
        if (!$this->data['name']) {
            $this->halt(403);
        }
        if ($this->data['hook_id']) {
            if (!Hook::find($this->data['hook_id'])) {
                $this->hook->setId($this->data['hook_id']);
            } else {
                $this->halt(409);
            }
        }
        $this->hook['user_id'] = $GLOBALS['user']->id;
        $this->hook['name'] = $this->data['name'];
        $this->hook['activated'] = (int) $this->data['activated'];
        $this->hook['cronjob'] = (int) $this->data['cronjob'];
        $this->hook['if_type'] = $this->data['if_type'];
        $this->hook['if_settings'] = $this->data['if_settings'];
        $this->hook['then_type'] = $this->data['then_type'];
        $this->hook['then_settings'] = $this->data['then_settings'];
        $this->hook['editable'] = (int) $this->data['editable'];
        $consumer = RESTAPI\Consumer\Base::detectConsumer();
        if ($consumer && $consumer->getId()) {
            $this->hook['consumer_id'] = $consumer->getId();
        }

        $this->hook->store();

        return $this->hook->toArray();
    }


}
