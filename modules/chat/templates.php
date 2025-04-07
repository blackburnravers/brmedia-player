<?php
/**
 * Chat Templates
 * Advanced rendering of chat interface with moderation and multi-room support.
 */

if (!defined('ABSPATH')) {
    exit;
}

// Chat window template
function brmedia_chat_template($room = 'default', $height = '400px', $width = '300px', $theme = 'light') {
    $valid_themes = ['light', 'dark'];
    $theme = in_array($theme, $valid_themes) ? $theme : 'light';
    $rooms = explode(',', get_option('brmedia_chat_rooms', 'default,radio,gaming'));
    $room = in_array($room, $rooms) ? $room : 'default';
    $user = wp_get_current_user();
    $is_moderator = current_user_can('moderate_comments');

    ob_start();
    ?>
    <div id="brmedia-chat-<?php echo esc_attr($room); ?>" 
         class="brmedia-chat brmedia-theme-<?php echo esc_attr($theme); ?>" 
         data-room="<?php echo esc_attr($room); ?>"
         style="height: <?php echo esc_attr($height); ?>; width: <?php echo esc_attr($width); ?>;">
        <div class="chat-header">
            <h3>Chat Room: <?php echo esc_html($room); ?></h3>
            <span><?php echo $user->ID ? esc_html($user->display_name) : 'Guest'; ?></span>
        </div>
        <div class="chat-messages" id="chat-messages-<?php echo esc_attr($room); ?>"></div>
        <form class="chat-form" id="chat-form-<?php echo esc_attr($room); ?>">
            <input type="text" id="chat-input-<?php echo esc_attr($room); ?>" placeholder="Type a message..." autocomplete="off" required>
            <button type="submit"><i class="fas fa-paper-plane"></i> Send</button>
            <?php if ($is_moderator) : ?>
                <button type="button" class="chat-moderate-btn" onclick="showModerationOptions('<?php echo esc_attr($room); ?>')">
                    <i class="fas fa-gavel"></i> Moderate
                </button>
            <?php endif; ?>
        </form>
        <?php if ($is_moderator) : ?>
            <div class="chat-moderation" id="chat-moderation-<?php echo esc_attr($room); ?>" style="display:none;">
                <select id="mod-action-<?php echo esc_attr($room); ?>">
                    <option value="mute">Mute</option>
                    <option value="ban">Ban</option>
                    <option value="timeout">Timeout</option>
                </select>
                <input type="number" id="mod-duration-<?php echo esc_attr($room); ?>" placeholder="Duration (seconds)" value="0">
                <input type="number" id="mod-user-id-<?php echo esc_attr($room); ?>" placeholder="Target User ID">
                <button onclick="moderateUser('<?php echo esc_attr($room); ?>')">Apply</button>
            </div>
        <?php endif; ?>
        <script>
            jQuery(document).ready(function($) {
                const room = '<?php echo esc_attr($room); ?>';
                let lastId = 0;

                // Fetch messages periodically
                function fetchMessages() {
                    $.post(brmedia_chat.ajax_url, {
                        action: 'brmedia_chat_fetch',
                        room: room,
                        last_id: lastId,
                        nonce: brmedia_chat.nonce
                    }, function(response) {
                        if (response.success) {
                            const $messages = $('#chat-messages-' + room);
                            response.data.messages.forEach(msg => {
                                $messages.append(`<p data-msg-id="${msg.id}"><strong>${msg.user}</strong> (${msg.timestamp}): ${msg.message}</p>`);
                                lastId = Math.max(lastId, msg.id);
                            });
                            $messages.scrollTop($messages[0].scrollHeight);
                        }
                    });
                }

                // Handle message submission
                $('#chat-form-' + room).on('submit', function(e) {
                    e.preventDefault();
                    const $input = $('#chat-input-' + room);
                    const message = $input.val().trim();
                    if (!message) return;

                    $.post(brmedia_chat.ajax_url, {
                        action: 'brmedia_chat_message',
                        message: message,
                        room: room,
                        nonce: brmedia_chat.nonce
                    }, function(response) {
                        if (response.success) {
                            $input.val('');
                        } else {
                            alert('Error: ' + (response.data.message || 'Failed to send message'));
                        }
                    });
                });

                // Moderation controls
                window.showModerationOptions = function(room) {
                    $('#chat-moderation-' + room).toggle();
                };

                window.moderateUser = function(room) {
                    const action = $('#mod-action-' + room).val();
                    const duration = $('#mod-duration-' + room).val();
                    const targetUserId = $('#mod-user-id-' + room).val();

                    $.post(brmedia_chat.ajax_url, {
                        action: 'brmedia_chat_moderate',
                        action_type: action,
                        target_user_id: targetUserId,
                        room: room,
                        duration: duration,
                        nonce: brmedia_chat.nonce
                    }, function(response) {
                        if (response.success) {
                            alert(response.data.message);
                            $('#chat-moderation-' + room).hide();
                        } else {
                            alert('Error: ' + (response.data.message || 'Moderation failed'));
                        }
                    });
                };

                // Poll for new messages
                fetchMessages();
                setInterval(fetchMessages, <?php echo esc_attr(get_option('brmedia_chat_poll_interval', 5) * 1000); ?>);
            });
        </script>
    </div>
    <?php
    return ob_get_clean();
}