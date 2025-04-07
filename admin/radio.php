<?php
/**
 * Radio Management Page
 * Advanced interface for managing radio streaming, DJ timetables, auto-DJ, and chat integration.
 */

if (!defined('ABSPATH')) {
    exit; // Prevent direct access
}

// Radio management class
class BRMedia_Radio_Admin {
    private $db_table = 'brmedia_dj_timetables';

    public function __construct() {
        global $wpdb;
        $this->db_table = $wpdb->prefix . $this->db_table;

        add_action('admin_menu', [$this, 'add_radio_menu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_radio_assets']);
        add_action('wp_ajax_brmedia_save_radio_settings', [$this, 'save_radio_settings']);
        add_action('wp_ajax_brmedia_add_dj_timetable', [$this, 'add_dj_timetable']);
        add_action('wp_ajax_brmedia_delete_dj_timetable', [$this, 'delete_dj_timetable']);
        add_action('admin_init', [$this, 'create_dj_timetable_table']);
    }

    // Create custom table for DJ timetables
    public function create_dj_timetable_table() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sql = "CREATE TABLE IF NOT EXISTS {$this->db_table} (
            id BIGINT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
            dj_name VARCHAR(100) NOT NULL,
            start_time DATETIME NOT NULL,
            end_time DATETIME NOT NULL,
            stream_url VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    // Add radio submenu under BRMedia menu
    public function add_radio_menu() {
        add_submenu_page(
            'brmedia',
            'Radio Management',
            'Radio',
            'manage_options',
            'brmedia-radio',
            [$this, 'render_radio_page']
        );
    }

    // Enqueue styles and scripts
    public function enqueue_radio_assets($hook) {
        if ($hook !== 'brmedia_page_brmedia-radio') {
            return;
        }
        wp_enqueue_style('brmedia-radio-css', plugins_url('assets/css/radio.css', __FILE__), [], '1.2.0');
        wp_enqueue_script('brmedia-radio-js', plugins_url('assets/js/radio.js', __FILE__), ['jquery'], '1.2.0', true);
        wp_localize_script('brmedia-radio-js', 'brmediaRadio', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('brmedia_radio_nonce'),
        ]);
    }

    // AJAX handler to save radio settings
    public function save_radio_settings() {
        check_ajax_referer('brmedia_radio_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $settings = wp_unslash($_POST['settings'] ?? []);
        $allowed_settings = [
            'selected_source' => '',
            'auto_dj_enabled' => 0,
            'chat_integration' => 0,
            'chat_api_key' => '',
        ];
        $sanitized_settings = array_intersect_key($settings, $allowed_settings);
        $sanitized_settings['selected_source'] = sanitize_text_field($sanitized_settings['selected_source'] ?? 'Icecast');
        $sanitized_settings['auto_dj_enabled'] = isset($sanitized_settings['auto_dj_enabled']) ? 1 : 0;
        $sanitized_settings['chat_integration'] = isset($sanitized_settings['chat_integration']) ? 1 : 0;
        $sanitized_settings['chat_api_key'] = sanitize_text_field($sanitized_settings['chat_api_key'] ?? '');

        update_option('brmedia_radio_settings', $sanitized_settings);
        wp_send_json_success(['message' => 'Settings saved successfully.']);
    }

    // AJAX handler to add DJ timetable
    public function add_dj_timetable() {
        global $wpdb;
        check_ajax_referer('brmedia_radio_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $timetable = wp_unslash($_POST['timetable'] ?? []);
        $dj_name = sanitize_text_field($timetable['dj_name'] ?? '');
        $start_time = sanitize_text_field($timetable['start_time'] ?? '');
        $end_time = sanitize_text_field($timetable['end_time'] ?? '');
        $stream_url = esc_url_raw($timetable['stream_url'] ?? '');

        if (empty($dj_name) || empty($start_time) || empty($end_time) || empty($stream_url)) {
            wp_send_json_error(['message' => 'All fields are required.']);
        }

        if (strtotime($end_time) <= strtotime($start_time)) {
            wp_send_json_error(['message' => 'End time must be after start time.']);
        }

        $wpdb->insert($this->db_table, [
            'dj_name' => $dj_name,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'stream_url' => $stream_url,
        ]);

        if ($wpdb->insert_id) {
            wp_send_json_success([
                'message' => 'Timetable added successfully.',
                'id' => $wpdb->insert_id,
                'row' => $this->get_timetable_row($wpdb->insert_id, $dj_name, $start_time, $end_time, $stream_url),
            ]);
        } else {
            wp_send_json_error(['message' => 'Failed to add timetable.']);
        }
    }

    // AJAX handler to delete DJ timetable
    public function delete_dj_timetable() {
        global $wpdb;
        check_ajax_referer('brmedia_radio_nonce', 'nonce');
        if (!current_user_can('manage_options')) {
            wp_send_json_error(['message' => 'Unauthorized action.']);
        }

        $id = intval($_POST['id'] ?? 0);
        if ($id <= 0) {
            wp_send_json_error(['message' => 'Invalid timetable ID.']);
        }

        $deleted = $wpdb->delete($this->db_table, ['id' => $id], ['%d']);
        if ($deleted) {
            wp_send_json_success(['message' => 'Timetable deleted successfully.', 'id' => $id]);
        } else {
            wp_send_json_error(['message' => 'Failed to delete timetable.']);
        }
    }

    // Helper to generate timetable row HTML
    private function get_timetable_row($id, $dj_name, $start_time, $end_time, $stream_url) {
        return sprintf(
            '<tr data-id="%d"><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td><button class="button delete-timetable">Delete</button></td></tr>',
            esc_attr($id),
            esc_html($dj_name),
            esc_html(date('Y-m-d H:i', strtotime($start_time))),
            esc_html(date('Y-m-d H:i', strtotime($end_time))),
            esc_html($stream_url)
        );
    }

    // Render the radio management page
    public function render_radio_page() {
        if (!current_user_can('manage_options')) {
            wp_die('You do not have sufficient permissions to access this page.');
        }

        global $wpdb;
        $streaming_sources = [
            'Icecast' => 'Icecast',
            'Shoutcast' => 'Shoutcast',
            'Winamp' => 'Winamp',
            'Windows Media Encoder' => 'Windows Media Encoder',
            'Facebook Live' => 'Facebook Live',
            'YouTube Live' => 'YouTube Live',
            'Flash Stream' => 'Flash Stream',
        ];

        $settings = get_option('brmedia_radio_settings', [
            'selected_source' => 'Icecast',
            'auto_dj_enabled' => 0,
            'chat_integration' => 1,
            'chat_api_key' => '',
        ]);

        $timetables = $wpdb->get_results("SELECT * FROM {$this->db_table} ORDER BY start_time ASC", ARRAY_A);

        ?>
        <div class="wrap">
            <h1>Radio Management</h1>
            <div class="brmedia-radio-management">
                <!-- Streaming Source Selection -->
                <section class="brmedia-streaming-source">
                    <h2>Streaming Source</h2>
                    <form id="streaming-source-form">
                        <label>
                            Select Source:
                            <select name="settings[selected_source]">
                                <?php foreach ($streaming_sources as $key => $label): ?>
                                    <option value="<?php echo esc_attr($key); ?>" <?php selected($settings['selected_source'], $key); ?>>
                                        <?php echo esc_html($label); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                        <button type="button" id="save-streaming-source" class="button button-primary">Save Source</button>
                    </form>
                </section>

                <!-- DJ Timetables -->
                <section class="brmedia-dj-timetables">
                    <h2>DJ Timetables</h2>
                    <form id="add-timetable-form">
                        <div class="form-row">
                            <label>DJ Name: <input type="text" name="timetable[dj_name]" required></label>
                            <label>Start Time: <input type="datetime-local" name="timetable[start_time]" required></label>
                            <label>End Time: <input type="datetime-local" name="timetable[end_time]" required></label>
                            <label>Stream URL: <input type="url" name="timetable[stream_url]" required placeholder="https://example.com/stream"></label>
                            <button type="button" id="add-timetable" class="button button-primary">Add Timetable</button>
                        </div>
                    </form>
                    <table class="wp-list-table widefat fixed striped" id="timetables-table">
                        <thead>
                            <tr>
                                <th>DJ Name</th>
                                <th>Start Time</th>
                                <th>End Time</th>
                                <th>Stream URL</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($timetables as $timetable): ?>
                                <tr data-id="<?php echo esc_attr($timetable['id']); ?>">
                                    <td><?php echo esc_html($timetable['dj_name']); ?></td>
                                    <td><?php echo esc_html(date('Y-m-d H:i', strtotime($timetable['start_time']))); ?></td>
                                    <td><?php echo esc_html(date('Y-m-d H:i', strtotime($timetable['end_time']))); ?></td>
                                    <td><?php echo esc_html($timetable['stream_url']); ?></td>
                                    <td><button class="button delete-timetable">Delete</button></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($timetables)): ?>
                                <tr><td colspan="5">No timetables found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </section>

                <!-- Auto-DJ Playlists -->
                <section class="brmedia-auto-dj">
                    <h2>Auto-DJ Playlists</h2>
                    <label>
                        <input type="checkbox" name="settings[auto_dj_enabled]" value="1" <?php checked($settings['auto_dj_enabled']); ?>>
                        Enable Auto-DJ
                    </label>
                    <p>Auto-DJ activates when no live DJ is scheduled. Configure playlists in the <a href="#">Playlist Manager</a> (coming soon).</p>
                </section>

                <!-- Chat Integration -->
                <section class="brmedia-chat-integration">
                    <h2>Chat Integration</h2>
                    <label>
                        <input type="checkbox" name="settings[chat_integration]" value="1" <?php checked($settings['chat_integration']); ?>>
                        Enable Chat for Radio
                    </label>
                    <label>
                        Chat API Key: <input type="text" name="settings[chat_api_key]" value="<?php echo esc_attr($settings['chat_api_key']); ?>" placeholder="Enter your chat service API key">
                    </label>
                    <p>Integrate with external chat services (e.g., Discord, Slack) for listener interaction.</p>
                </section>

                <button id="save-radio-settings" class="button button-primary">Save All Settings</button>
            </div>
        </div>
        <script>
            jQuery(document).ready(function($) {
                // Save streaming source
                $('#save-streaming-source').on('click', function() {
                    const settings = { selected_source: $('select[name="settings[selected_source]"]').val() };
                    $.post(brmediaRadio.ajax_url, {
                        action: 'brmedia_save_radio_settings',
                        settings: settings,
                        nonce: brmediaRadio.nonce
                    }, function(response) {
                        alert(response.success ? response.data.message : response.data.message);
                    });
                });

                // Add DJ timetable
                $('#add-timetable').on('click', function() {
                    const timetable = $('#add-timetable-form').serializeArray().reduce((acc, item) => {
                        const match = item.name.match(/timetable\[(.+)\]/);
                        if (match) acc[match[1]] = item.value;
                        return acc;
                    }, {});
                    $.post(brmediaRadio.ajax_url, {
                        action: 'brmedia_add_dj_timetable',
                        timetable: timetable,
                        nonce: brmediaRadio.nonce
                    }, function(response) {
                        if (response.success) {
                            $('#timetables-table tbody').append(response.data.row);
                            $('#add-timetable-form')[0].reset();
                            alert(response.data.message);
                        } else {
                            alert(response.data.message);
                        }
                    });
                });

                // Delete DJ timetable
                $(document).on('click', '.delete-timetable', function() {
                    const $row = $(this).closest('tr');
                    const id = $row.data('id');
                    if (confirm('Are you sure you want to delete this timetable?')) {
                        $.post(brmediaRadio.ajax_url, {
                            action: 'brmedia_delete_dj_timetable',
                            id: id,
                            nonce: brmediaRadio.nonce
                        }, function(response) {
                            if (response.success) {
                                $row.remove();
                                alert(response.data.message);
                            } else {
                                alert(response.data.message);
                            }
                        });
                    }
                });

                // Save all settings
                $('#save-radio-settings').on('click', function() {
                    const settings = {
                        selected_source: $('select[name="settings[selected_source]"]').val(),
                        auto_dj_enabled: $('input[name="settings[auto_dj_enabled]"]').is(':checked') ? 1 : 0,
                        chat_integration: $('input[name="settings[chat_integration]"]').is(':checked') ? 1 : 0,
                        chat_api_key: $('input[name="settings[chat_api_key]"]').val()
                    };
                    $.post(brmediaRadio.ajax_url, {
                        action: 'brmedia_save_radio_settings',
                        settings: settings,
                        nonce: brmediaRadio.nonce
                    }, function(response) {
                        alert(response.success ? response.data.message : response.data.message);
                    });
                });
            });
        </script>
        <?php
    }
}

new BRMedia_Radio_Admin();