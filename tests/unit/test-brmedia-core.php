<?php
/**
 * Unit tests for BRMedia core classes.
 *
 * @package BRMedia\Tests\Unit
 */

use PHPUnit\Framework\TestCase;
use BRMedia\Includes\Core\BRMedia_DI_Container;

class Test_BRMedia_Core extends TestCase {
    public function test_di_container() {
        $di = new BRMedia_DI_Container();
        $di->register('test_service', 'stdClass');
        $service = $di->get('test_service');
        $this->assertInstanceOf('stdClass', $service);
    }

    public function test_utils_sanitize_array() {
        $input = ['<script>alert("xss")</script>', 'safe'];
        $sanitized = BRMedia\Includes\Core\BRMedia_Utils::sanitize_array($input);
        $this->assertEquals(['alert("xss")', 'safe'], $sanitized);
    }
}