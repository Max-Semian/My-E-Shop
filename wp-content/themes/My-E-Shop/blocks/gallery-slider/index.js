(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { 
        InspectorControls, 
        MediaUpload, 
        MediaUploadCheck,
        PanelColorSettings
    } = wp.blockEditor;
    const { 
        PanelBody, 
        TextControl, 
        Button, 
        RangeControl
    } = wp.components;
    const { __ } = wp.i18n;

    registerBlockType('my-e-shop/gallery-slider', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                title, 
                subtitle, 
                images, 
                backgroundColor, 
                titleColor, 
                subtitleColor,
                animationSpeed,
                cardWidth,
                cardHeight
            } = attributes;

            const onSelectImages = function(media) {
                setAttributes({
                    images: media.map(function(item) {
                        return {
                            id: item.id,
                            url: item.url,
                            alt: item.alt || ''
                        };
                    })
                });
            };

            const removeImage = function(index) {
                const newImages = [...images];
                newImages.splice(index, 1);
                setAttributes({ images: newImages });
            };

            return [
                el(InspectorControls, null,
                    el(PanelBody, { title: __('Gallery settings', 'my-e-shop'), initialOpen: true },
                        el(TextControl, {
                            label: __('Title', 'my-e-shop'),
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            }
                        }),
                        el(TextControl, {
                            label: __('Subtitle', 'my-e-shop'),
                            value: subtitle,
                            onChange: function(value) {
                                setAttributes({ subtitle: value });
                            }
                        }),
                        el(RangeControl, {
                            label: __('Animation speed', 'my-e-shop'),
                            value: animationSpeed,
                            onChange: function(value) {
                                setAttributes({ animationSpeed: value });
                            },
                            min: 10,
                            max: 100
                        }),
                        el(RangeControl, {
                            label: __('Card width (px)', 'my-e-shop'),
                            value: cardWidth,
                            onChange: function(value) {
                                setAttributes({ cardWidth: value });
                            },
                            min: 200,
                            max: 400
                        }),
                        el(RangeControl, {
                            label: __('Card height (px)', 'my-e-shop'),
                            value: cardHeight,
                            onChange: function(value) {
                                setAttributes({ cardHeight: value });
                            },
                            min: 250,
                            max: 500
                        })
                    ),
                    el(PanelColorSettings, {
                        title: __('Colors', 'my-e-shop'),
                        initialOpen: false,
                        colorSettings: [
                            {
                                value: backgroundColor,
                                onChange: function(color) {
                                    setAttributes({ backgroundColor: color });
                                },
                                label: __('Background color', 'my-e-shop')
                            },
                            {
                                value: titleColor,
                                onChange: function(color) {
                                    setAttributes({ titleColor: color });
                                },
                                label: __('Title color', 'my-e-shop')
                            },
                            {
                                value: subtitleColor,
                                onChange: function(color) {
                                    setAttributes({ subtitleColor: color });
                                },
                                label: __('Subtitle color', 'my-e-shop')
                            }
                        ]
                    })
                ),
                el('div', { 
                    className: 'gallery-slider-block-editor',
                    style: {
                        backgroundColor: backgroundColor,
                        padding: '40px 20px',
                        minHeight: '400px'
                    }
                },
                    el('div', { className: 'container' },
                        el('div', { className: 'row mb-5' },
                            el('div', { className: 'col-12 text-center' },
                                el('h2', { 
                                    className: 'section-title',
                                    style: { color: titleColor }
                                },
                                    el('span', null, title)
                                ),
                                el('p', { 
                                    className: 'section-description',
                                    style: { color: subtitleColor }
                                },
                                    el('span', null, subtitle)
                                )
                            )
                        ),
                        el('div', { className: 'collections-slider-section' },
                            el(MediaUploadCheck, null,
                                el(MediaUpload, {
                                    onSelect: onSelectImages,
                                    allowedTypes: ['image'],
                                    multiple: true,
                                    value: images.map(function(img) { return img.id; }),
                                    render: function(obj) {
                                        return el(Button, {
                                            onClick: obj.open,
                                            className: 'button button-large',
                                            style: { 
                                                marginBottom: '20px',
                                                display: 'block',
                                                margin: '0 auto 20px'
                                            }
                                        }, images.length > 0 ?
                                            __('Change images', 'my-e-shop') :
                                            __('Select images', 'my-e-shop')
                                        );
                                    }
                                })
                            ),
                            images.length > 0 && el('div', { 
                                className: 'collections-horizontal-carousel',
                                style: { overflow: 'hidden' }
                            },
                                el('div', { 
                                    className: 'collections-smooth-track',
                                    style: {
                                        display: 'flex',
                                        gap: '15px',
                                        width: 'fit-content'
                                    }
                                },
                                    images.concat(images).map(function(image, index) {
                                        return el('div', {
                                            key: 'img-' + index,
                                            className: 'collections-square-card',
                                            style: {
                                                width: cardWidth + 'px',
                                                height: cardHeight + 'px',
                                                flexShrink: 0,
                                                position: 'relative'
                                            }
                                        },
                                            el('img', {
                                                src: image.url,
                                                alt: image.alt,
                                                className: 'collections-square-image',
                                                style: {
                                                    width: '100%',
                                                    height: '100%',
                                                    objectFit: 'cover',
                                                    borderRadius: '8px'
                                                }
                                            }),
                                            index < images.length && el(Button, {
                                                onClick: function() { removeImage(index); },
                                                className: 'button-link-delete',
                                                style: {
                                                    position: 'absolute',
                                                    top: '5px',
                                                    right: '5px',
                                                    background: 'rgba(255,255,255,0.8)',
                                                    border: 'none',
                                                    borderRadius: '50%',
                                                    width: '24px',
                                                    height: '24px',
                                                    cursor: 'pointer'
                                                }
                                            }, '×')
                                        );
                                    })
                                )
                            ),
                            images.length === 0 && el('div', {
                                style: {
                                    textAlign: 'center',
                                    padding: '60px 20px',
                                    border: '2px dashed #ddd',
                                    borderRadius: '8px',
                                    color: '#999'
                                }
                            },
                                el('p', null, __('Select images for the gallery', 'my-e-shop'))
                            )
                        )
                    )
                )
            ];
        },

        save: function() {
            return null; // Server-side rendering
        }
    });
})();
