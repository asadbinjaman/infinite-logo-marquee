<?php
/**
 * Plugin Name: Infinite Client Logo Marquee
 * Description: A lightweight Gutenberg block for an infinite multi-directional logo ticker with bulk upload and custom CSS height units.
 * Version: 1.0.2
 * Author: Custom Dev
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Allow custom CSS properties (--*) in block wrapper styles.
 * WordPress's safecss_filter_attr() strips unknown properties by default.
 */
add_filter( 'safe_style_css', 'iclm_allow_custom_properties' );
function iclm_allow_custom_properties( $allowed ) {
    $custom_props = array(
        '--iclm-height',
        '--iclm-gap',
        '--iclm-speed',
        '--iclm-wrapper-height',
    );
    return array_merge( $allowed, $custom_props );
}

function iclm_register_block() {
    $plugin_url = plugin_dir_url( __FILE__ );

    wp_register_script(
        'iclm-block-editor',
        $plugin_url . 'block.js',
        array( 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n' ),
        '1.0.2',
        true
    );

    wp_register_style(
        'iclm-block-style',
        $plugin_url . 'style.css',
        array(),
        '1.0.2'
    );

    wp_register_style(
        'iclm-block-editor-style',
        $plugin_url . 'editor.css',
        array( 'wp-edit-blocks' ),
        '1.0.2'
    );

    register_block_type( 'custom/infinite-logo-marquee', array(
        'editor_script'   => 'iclm-block-editor',
        'style'           => 'iclm-block-style',
        'editor_style'    => 'iclm-block-editor-style',
        'render_callback' => 'iclm_render_marquee_block',
        'attributes'      => array(
            'images' => array(
                'type'    => 'array',
                'default' => array(),
            ),
            'logoHeight' => array(
                'type'    => 'string',
                'default' => '50px',
            ),
            'logoGap' => array(
                'type'    => 'string',
                'default' => '48px',
            ),
            'speed' => array(
                'type'    => 'number',
                'default' => 30,
            ),
            'pauseOnHover' => array(
                'type'    => 'boolean',
                'default' => true,
            ),
            'direction' => array(
                'type'    => 'string',
                'default' => 'left',
            ),
            'wrapperHeight' => array(
                'type'    => 'string',
                'default' => '200px',
            ),
        ),
    ) );
}
add_action( 'init', 'iclm_register_block' );

function iclm_render_marquee_block( $attributes ) {
    $images = isset( $attributes['images'] ) ? $attributes['images'] : array();
    if ( empty( $images ) ) {
        return '';
    }

    $height         = esc_attr( $attributes['logoHeight'] ?? '50px' );
    $gap            = esc_attr( $attributes['logoGap'] ?? '48px' );
    $speed          = intval( $attributes['speed'] ?? 30 );
    $pause_on_hover = ! empty( $attributes['pauseOnHover'] ) ? 'true' : 'false';
    $direction      = esc_attr( $attributes['direction'] ?? 'left' );
    $wrapper_height = esc_attr( $attributes['wrapperHeight'] ?? '200px' );
    $is_vertical    = in_array( $direction, array( 'up', 'down' ), true );

    // Sanitize direction for CSS class
    $allowed_directions = array( 'left', 'right', 'up', 'down' );
    $direction_class = in_array( $direction, $allowed_directions, true ) ? $direction : 'left';

    // Build style: custom properties + explicit height/max-height for vertical
    $style = "--iclm-height: {$height}; --iclm-gap: {$gap}; --iclm-speed: {$speed}s;";
    if ( $is_vertical ) {
        $style .= " --iclm-wrapper-height: {$wrapper_height}; height: {$wrapper_height}; max-height: {$wrapper_height};";
    }

    $wrapper_attributes = get_block_wrapper_attributes( array(
        'class'          => 'iclm-marquee-wrapper iclm-dir-' . $direction_class,
        'style'          => $style,
        'data-pause'     => $pause_on_hover,
        'data-direction' => $direction,
    ) );

    ob_start();
    ?>
    <div <?php echo $wrapper_attributes; ?>>
        <div class="iclm-marquee-track">
            <?php foreach ( $images as $img ) : 
                $url = esc_url( $img['url'] ?? '' );
                $alt = esc_attr( $img['alt'] ?? '' );
                if ( ! $url ) continue;
            ?>
                <div class="iclm-logo-item">
                    <img src="<?php echo $url; ?>" alt="<?php echo $alt; ?>" loading="eager" decoding="async" />
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script>
    (function() {
        document.querySelectorAll('.iclm-marquee-wrapper:not(.iclm-initialized)').forEach(function(wrapper) {
            wrapper.classList.add('iclm-initialized');
            const track = wrapper.querySelector('.iclm-marquee-track');
            if (!track) return;

            const direction = wrapper.dataset.direction || 'left';
            const isVertical = direction === 'up' || direction === 'down';

            const initMarquee = () => {
                const items = Array.from(track.children);
                if (items.length === 0) return;

                // Measure the correct dimension based on direction
                // Use clientHeight/clientWidth for accurate rendered size
                const wrapperSize = isVertical 
                    ? (wrapper.clientHeight || parseFloat(getComputedStyle(wrapper).height) || 200)
                    : (wrapper.clientWidth || window.innerWidth);
                const trackSize = isVertical ? track.scrollHeight : track.scrollWidth;

                // Guard against zero size
                if (wrapperSize <= 0) return;

                // Ensure single set is larger than viewport (handles 1-image edge case)
                let currentSize = trackSize;
                while (currentSize < wrapperSize * 1.2) {
                    items.forEach(item => {
                        const clone = item.cloneNode(true);
                        clone.setAttribute('aria-hidden', 'true');
                        track.appendChild(clone);
                    });
                    currentSize = isVertical ? track.scrollHeight : track.scrollWidth;
                }

                // Clone the entire filled set once more for a seamless -50% infinite loop
                const fullSet = Array.from(track.children);
                fullSet.forEach(item => {
                    const clone = item.cloneNode(true);
                    clone.setAttribute('aria-hidden', 'true');
                    track.appendChild(clone);
                });

                // CSS keyframes handle initial position (0% for left/up, -50% for right/down)
                // No manual transform needed here - animation starts from 0% keyframe

                wrapper.classList.add('iclm-is-ready');
            };

            const images = track.querySelectorAll('img');
            let loaded = 0;
            const checkLoad = () => {
                loaded++;
                if (loaded >= images.length) initMarquee();
            };

            images.forEach(img => {
                if (img.complete) checkLoad();
                else {
                    img.addEventListener('load', checkLoad);
                    img.addEventListener('error', checkLoad);
                }
            });
        });
    })();
    </script>
    <?php
    return ob_get_clean();
}