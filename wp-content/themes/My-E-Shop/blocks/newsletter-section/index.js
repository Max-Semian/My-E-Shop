(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls, MediaUpload, MediaUploadCheck } = wp.blockEditor;
    const { 
        PanelBody, 
        TextControl, 
        TextareaControl,
        Button, 
        RangeControl,
        ColorPicker,
        BaseControl 
    } = wp.components;
    const { Fragment } = wp.element;
    const { __ } = wp.i18n;

    registerBlockType('my-e-shop/newsletter-section', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                title, 
                subtitle, 
                buttonText, 
                placeholderText,
                backgroundImage, 
                backgroundImageId,
                backgroundColor,
                textColor,
                overlay
            } = attributes;

            function onSelectImage(media) {
                setAttributes({
                    backgroundImage: media.url,
                    backgroundImageId: media.id
                });
            }

            function onRemoveImage() {
                setAttributes({
                    backgroundImage: '',
                    backgroundImageId: 0
                });
            }

            return [
                wp.element.createElement(InspectorControls, { key: 'inspector' },
                    wp.element.createElement(PanelBody, { title: __('Newsletter Settings', 'my-e-shop'), initialOpen: true },
                        wp.element.createElement(TextControl, {
                            label: __('Title', 'my-e-shop'),
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            }
                        }),
                        wp.element.createElement(TextareaControl, {
                            label: __('Subtitle', 'my-e-shop'),
                            value: subtitle,
                            onChange: function(value) {
                                setAttributes({ subtitle: value });
                            }
                        }),
                        wp.element.createElement(TextControl, {
                            label: __('Button Text', 'my-e-shop'),
                            value: buttonText,
                            onChange: function(value) {
                                setAttributes({ buttonText: value });
                            }
                        }),
                        wp.element.createElement(TextControl, {
                            label: __('Placeholder Text', 'my-e-shop'),
                            value: placeholderText,
                            onChange: function(value) {
                                setAttributes({ placeholderText: value });
                            }
                        })
                    ),
                    wp.element.createElement(PanelBody, { title: __('Background Settings', 'my-e-shop'), initialOpen: false },
                        wp.element.createElement(MediaUploadCheck, {},
                            wp.element.createElement(MediaUpload, {
                                onSelect: onSelectImage,
                                allowedTypes: ['image'],
                                value: backgroundImageId,
                                render: function(obj) {
                                    return wp.element.createElement(Button, {
                                        className: backgroundImage ? 'editor-post-featured-image__preview' : 'editor-post-featured-image__toggle',
                                        onClick: obj.open
                                    }, backgroundImage ? wp.element.createElement('img', { src: backgroundImage, alt: '' }) : __('Set background image', 'my-e-shop'));
                                }
                            })
                        ),
                        backgroundImage && wp.element.createElement(Button, {
                            onClick: onRemoveImage,
                            isLink: true,
                            isDestructive: true
                        }, __('Remove background image', 'my-e-shop')),
                        wp.element.createElement(BaseControl, { label: __('Background Color', 'my-e-shop') },
                            wp.element.createElement(ColorPicker, {
                                color: backgroundColor,
                                onChange: function(color) {
                                    setAttributes({ backgroundColor: color });
                                }
                            })
                        ),
                        wp.element.createElement(BaseControl, { label: __('Text Color', 'my-e-shop') },
                            wp.element.createElement(ColorPicker, {
                                color: textColor,
                                onChange: function(color) {
                                    setAttributes({ textColor: color });
                                }
                            })
                        ),
                        wp.element.createElement(RangeControl, {
                            label: __('Overlay Opacity', 'my-e-shop'),
                            value: overlay,
                            onChange: function(value) {
                                setAttributes({ overlay: value });
                            },
                            min: 0,
                            max: 1,
                            step: 0.1
                        })
                    )
                ),
                wp.element.createElement('div', { 
                    key: 'block',
                    className: 'newsletter-section-preview',
                    style: { 
                        backgroundImage: backgroundImage ? 'url(' + backgroundImage + ')' : 'none',
                        backgroundColor: backgroundColor,
                        color: textColor,
                        minHeight: '400px',
                        backgroundSize: 'cover',
                        backgroundPosition: 'center',
                        position: 'relative',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        padding: '60px 20px'
                    }
                },
                    backgroundImage && wp.element.createElement('div', {
                        style: {
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            right: 0,
                            bottom: 0,
                            backgroundColor: 'rgba(0,0,0,' + overlay + ')',
                            zIndex: 1
                        }
                    }),
                    wp.element.createElement('div', {
                        style: {
                            position: 'relative',
                            zIndex: 2,
                            textAlign: 'center',
                            maxWidth: '600px',
                            width: '100%'
                        }
                    },
                        wp.element.createElement('h2', {
                            style: {
                                fontSize: '2.5rem',
                                marginBottom: '20px',
                                fontWeight: '300',
                                fontFamily: 'Playfair Display, serif'
                            }
                        }, title),
                        wp.element.createElement('p', {
                            style: {
                                fontSize: '1.1rem',
                                marginBottom: '40px',
                                opacity: 0.9,
                                fontFamily: 'Montserrat, sans-serif'
                            }
                        }, subtitle),
                        wp.element.createElement('div', {
                            style: {
                                display: 'flex',
                                gap: '10px',
                                maxWidth: '500px',
                                margin: '0 auto',
                                flexWrap: 'wrap'
                            }
                        },
                            wp.element.createElement('input', {
                                type: 'email',
                                placeholder: placeholderText,
                                style: {
                                    flex: '1',
                                    minWidth: '250px',
                                    padding: '15px 20px',
                                    border: 'none',
                                    borderRadius: '5px',
                                    fontSize: '1rem',
                                    outline: 'none'
                                },
                                disabled: true
                            }),
                            wp.element.createElement('button', {
                                style: {
                                    padding: '15px 30px',
                                    backgroundColor: '#D85AFF',
                                    color: '#ffffff',
                                    border: 'none',
                                    borderRadius: '5px',
                                    fontSize: '1rem',
                                    fontWeight: '600',
                                    cursor: 'pointer',
                                    whiteSpace: 'nowrap'
                                },
                                disabled: true
                            }, buttonText)
                        )
                    )
                )
            ];
        },

        save: function() {
            return null;
        }
    });
})();