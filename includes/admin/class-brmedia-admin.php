<?php
/**
 * BRMedia Admin Class
 *
 * This class extends the core admin functionality for specific tasks.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Admin extends BRMedia_Admin_Core {
    /**
     * Constructor
     *
     * @param DIContainer $di Dependency injection container
     */
    public function __construct(DIContainer $di) {
        parent::__construct($di);
        // Add additional admin functionality here
    }
}