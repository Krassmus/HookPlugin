<?php

abstract class BulkDeliverer {

    abstract static public function forThenHookType();

    /**
     * Executes the pushes
     */
    abstract public function execute();

}