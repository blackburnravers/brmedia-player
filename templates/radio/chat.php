<?php
/**
 * Template Name: Radio Chat
 * Description: Provides a chat interface for radio listeners.
 *
 * @package BRMedia Player
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Unique chat ID
$chat_id = $args['chat_id'] ?? 'radio-chat';
?>

<div class="brmedia-radio-chat" id="<?php echo esc_attr( $chat_id ); ?>">
    <div class="brmedia-chat-messages" id="chat-messages"></div>
    <form id="chat-form">
        <input type="text" id="chat-message" placeholder="Type your message...">
        <button type="submit">Send</button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const chatMessage = document.getElementById('chat-message');

    // Load messages every 5 seconds
    setInterval(function() {
        fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>?action=brmedia_get_chat_messages')
            .then(response => response.json())
            .then(messages => {
                chatMessages.innerHTML = '';
                messages.forEach(message => {
                    const p = document.createElement('p');
                    p.textContent = message.user + ': ' + message.message;
                    chatMessages.appendChild(p);
                });
            });
    }, 5000);

    // Submit new message
    chatForm.addEventListener('submit', function(event) {
        event.preventDefault();
        const message = chatMessage.value;
        if (message) {
            fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>?action=brmedia_submit_chat_message', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'message=' + encodeURIComponent(message),
            });
            chatMessage.value = '';
        }
    });
});
</script>