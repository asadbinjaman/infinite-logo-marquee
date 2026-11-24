(function (blocks, element, blockEditor, components, i18n) {
    const el = element.createElement;
    const __ = i18n.__;
    const { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } = blockEditor;
    const { PanelBody, Button, RangeControl, ToggleControl, __experimentalUnitControl: UnitControl, SelectControl } = components;

    blocks.registerBlockType('custom/infinite-logo-marquee', {
        title: __('Client Logo Marquee', 'iclm'),
        icon: 'slides',
        category: 'media',
        description: __('Infinite multi-directional (left, right, up, down) scrolling client logo ticker.', 'iclm'),

        edit: function (props) {
            const { attributes, setAttributes } = props;
            const { images, logoHeight, logoGap, speed, pauseOnHover, direction, wrapperHeight } = attributes;

            const blockProps = useBlockProps({
                className: 'iclm-editor-wrapper',
                style: {
                    '--iclm-height': logoHeight,
                    '--iclm-gap': logoGap
                }
            });

            const onSelectImages = (newImages) => {
                const formatted = newImages.map((img) => ({
                    id: img.id,
                    url: img.url,
                    alt: img.alt || ''
                }));
                setAttributes({ images: formatted });
            };

            const removeImage = (indexToRemove) => {
                const updated = images.filter((_, idx) => idx !== indexToRemove);
                setAttributes({ images: updated });
            };

            const units = [
                { value: 'px', label: 'px', default: 50 },
                { value: 'vh', label: 'vh', default: 10 },
                { value: 'vw', label: 'vw', default: 5 },
                { value: 'rem', label: 'rem', default: 3 },
                { value: 'em', label: 'em', default: 3 },
                { value: '%', label: '%', default: 100 }
            ];

            return el(
                element.Fragment,
                null,
                // RIGHT SIDEBAR INSPECTOR CONTROLS
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Client Logos (' + images.length + ')', 'iclm'), initialOpen: true },
                        el(
                            MediaUploadCheck,
                            null,
                            el(MediaUpload, {
                                onSelect: onSelectImages,
                                allowedTypes: ['image'],
                                multiple: true,
                                gallery: true,
                                value: images.map((img) => img.id),
                                render: function ({ open }) {
                                    return el(
                                        Button,
                                        {
                                            onClick: open,
                                            variant: 'primary',
                                            style: { width: '100%', justifyContent: 'center', marginBottom: '12px' }
                                        },
                                        images.length > 0 ? __('Add / Edit Logos', 'iclm') : __('Upload / Select Logos', 'iclm')
                                    );
                                }
                            })
                        ),
                        images.length > 0 &&
                            el(
                                'div',
                                { className: 'iclm-sidebar-grid' },
                                images.map((img, index) =>
                                    el(
                                        'div',
                                        { key: img.id + '-' + index, className: 'iclm-sidebar-thumb' },
                                        el('img', { src: img.url, alt: img.alt }),
                                        el(
                                            Button,
                                            {
                                                isDestructive: true,
                                                isSmall: true,
                                                onClick: () => removeImage(index),
                                                title: __('Remove logo', 'iclm')
                                            },
                                            '×'
                                        )
                                    )
                                )
                            )
                    ),
                    el(
                        PanelBody,
                        { title: __('Dimensions & Motion CSS', 'iclm'), initialOpen: true },
                        el(UnitControl, {
                            label: __('Logo Absolute Height', 'iclm'),
                            value: logoHeight,
                            units: units,
                            onChange: (val) => setAttributes({ logoHeight: val || '50px' }),
                            help: __('Width adjusts automatically to keep aspect ratio.', 'iclm')
                        }),
                        el('div', { style: { height: '12px' } }),
                        el(UnitControl, {
                            label: __('Gap Between Logos', 'iclm'),
                            value: logoGap,
                            units: units,
                            onChange: (val) => setAttributes({ logoGap: val || '48px' })
                        }),
                        el('div', { style: { height: '12px' } }),
                        el(RangeControl, {
                            label: __('Scroll Duration (Seconds)', 'iclm'),
                            value: speed,
                            onChange: (val) => setAttributes({ speed: val }),
                            min: 5,
                            max: 120,
                            help: __('Lower = faster, Higher = slower.', 'iclm')
                        }),
                        el(ToggleControl, {
                            label: __('Pause on Hover', 'iclm'),
                            checked: pauseOnHover,
                            onChange: (val) => setAttributes({ pauseOnHover: val })
                        }),
                        el('div', { style: { height: '12px' } }),
                        el(SelectControl, {
                            label: __('Scroll Direction', 'iclm'),
                            value: direction,
                            options: [
                                { value: 'left', label: __('Left (←)', 'iclm') },
                                { value: 'right', label: __('Right (→)', 'iclm') },
                                { value: 'up', label: __('Up (↑)', 'iclm') },
                                { value: 'down', label: __('Down (↓)', 'iclm') },
                            ],
                            onChange: (val) => setAttributes({ direction: val })
                        }),
                        (direction === 'up' || direction === 'down') && el('div', { style: { height: '12px' } }),
                        (direction === 'up' || direction === 'down') && el(UnitControl, {
                            label: __('Marquee Viewport Height', 'iclm'),
                            value: wrapperHeight,
                            units: units,
                            onChange: (val) => setAttributes({ wrapperHeight: val || '200px' }),
                            help: __('Height of the visible marquee area (vertical directions only).', 'iclm')
                        })
                    )
                ),

                // MAIN EDITOR CANVAS PREVIEW (Static & Lightweight)
                el(
                    'div',
                    blockProps,
                    images.length === 0
                        ? el(
                              'div',
                              { className: 'iclm-placeholder' },
                              el('p', null, __('Client Logo Marquee: Upload logos in the right sidebar →', 'iclm'))
                          )
                        : el(
                              'div',
                              { className: 'iclm-editor-preview-strip' },
                              images.map((img, index) =>
                                  el(
                                      'div',
                                      { key: index, className: 'iclm-logo-item' },
                                      el('img', { src: img.url, alt: img.alt })
                                  )
                              )
                          )
                )
            );
        },

        save: function () {
            // Rendered dynamically via PHP
            return null;
        }
    });
})(window.wp.blocks, window.wp.element, window.wp.blockEditor, window.wp.components, window.wp.i18n);
// Version 1.0.2