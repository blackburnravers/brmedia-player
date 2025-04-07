<?php
// music.php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}
?>

<div class="wrap brmedia-wrap">
    <h1><?php _e( 'Manage Music', 'brmedia-player' ); ?></h1>
    <div class="main-content">
        <a href="<?php echo admin_url( 'post-new.php?post_type=brmusic' ); ?>" class="button button-primary"><?php _e( 'Add New Music', 'brmedia-player' ); ?></a>
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th><?php _e( 'Title', 'brmedia-player' ); ?></th>
                    <th><?php _e( 'Artist', 'brmedia-player' ); ?></th>
                    <th><?php _e( 'Album', 'brmedia-player' ); ?></th>
                    <th><?php _e( 'Year', 'brmedia-player' ); ?></th>
                    <th><?php _e( 'Music File', 'brmedia-player' ); ?></th>
                    <th><?php _e( 'Tracklist File', 'brmedia-player' ); ?></th>
                    <th><?php _e( 'Actions', 'brmedia-player' ); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $query_args = array(
                    'post_type' => 'brmusic',
                    'posts_per_page' => -1,
                );

                $query = new WP_Query( $query_args );

                if ( $query->have_posts() ) {
                    while ( $query->have_posts() ) {
                        $query->the_post();
                        $post_id = get_the_ID();
                        ?>
                        <tr>
                            <td><?php the_title(); ?></td>
                            <td><?php echo esc_html( get_post_meta( $post_id, '_brmusic_artist', true ) ); ?></td>
                            <td><?php echo esc_html( get_post_meta( $post_id, '_brmusic_album', true ) ); ?></td>
                            <td><?php echo esc_html( get_post_meta( $post_id, '_brmusic_year', true ) ); ?></td>
                            <td><?php echo esc_html( get_post_meta( $post_id, '_brmusic_file', true ) ); ?></td>
                            <td><?php echo esc_html( get_post_meta( $post_id, '_brmusic_tracklist', true ) ); ?></td>
                            <td>
                                <a href="<?php echo get_edit_post_link( $post_id ); ?>"><?php _e( 'Edit', 'brmedia-player' ); ?></a> |
                                <a href="<?php echo get_delete_post_link( $post_id ); ?>"><?php _e( 'Delete', 'brmedia-player' ); ?></a>
                            </td>
                        </tr>
                        <?php
                    }
                    wp_reset_postdata();
                } else {
                    ?>
                    <tr>
                        <td colspan="7"><?php _e( 'No music found', 'brmedia-player' ); ?></td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>