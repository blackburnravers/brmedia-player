<?php
/**
 * BRMedia Dashboard AI Class
 *
 * This class handles the AI-driven dashboard display.
 *
 * @package BRMedia\Includes\Admin
 */

namespace BRMedia\Includes\Admin;

class BRMedia_Dashboard_AI {
    /** @var BRMedia_Analytics_AI AI analytics instance */
    private $analytics_ai;

    /**
     * Constructor
     *
     * @param BRMedia_Analytics_AI $analytics_ai AI analytics instance
     */
    public function __construct(BRMedia_Analytics_AI $analytics_ai) {
        $this->analytics_ai = $analytics_ai;
        add_action('admin_menu', [$this, 'add_dashboard_page']);
    }

    /**
     * Adds the AI Insights submenu page
     */
    public function add_dashboard_page() {
        add_submenu_page(
            'brmedia-dashboard',           // Parent slug
            'AI Insights',                 // Page title
            'AI Insights',                 // Menu title
            'manage_options',              // Capability
            'brmedia-ai-insights',         // Menu slug
            [$this, 'render_ai_insights']  // Callback function
        );
    }

    /**
     * Renders the AI Insights page
     */
    public function render_ai_insights() {
        $predictions = $this->analytics_ai->predict_trends();
        echo '<div class="wrap"><h1>AI Insights</h1><p>' . esc_html($predictions) . '</p></div>';
    }
}