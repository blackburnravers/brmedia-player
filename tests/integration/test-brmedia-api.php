<?php
/**
 * Integration tests for BRMedia API endpoints.
 *
 * @package BRMedia\Tests\Integration
 */

use WP_UnitTestCase;

class Test_BRMedia_API extends WP_UnitTestCase {
    public function setUp(): void {
        parent::setUp();
        // Set up test data, e.g., create posts, set options
        $this->factory()->post->create(['post_type' => 'brmusic']);
    }

    public function test_analytics_summary_endpoint() {
        $response = $this->get('/brmedia/v1/analytics/summary');
        $this->assertEquals(200, $response->get_status());
        $this->assertArrayHasKey('total_plays', $response->get_data());
    }

    public function test_media_endpoint() {
        $response = $this->get('/brmedia/v1/media');
        $this->assertEquals(200, $response->get_status());
        $this->assertNotEmpty($response->get_data());
    }

    public function test_settings_endpoint() {
        $response = $this->get('/brmedia/v1/settings');
        $this->assertEquals(200, $response->get_status());
        $this->assertArrayHasKey('default_audio_template', $response->get_data());
    }
}