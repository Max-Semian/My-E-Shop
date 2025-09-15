// About Section Block
(function() {
    'use strict';

    var __ = wp.i18n.__;
    var registerBlockType = wp.blocks.registerBlockType;
    var createElement = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var InspectorControls = wp.blockEditor.InspectorControls;
    
    // Import necessary components
    if (typeof wp !== 'undefined' && wp.blockEditor && wp.blockEditor.useBlockProps) {
        var useBlockProps = wp.blockEditor.useBlockProps;
        var PanelBody = wp.components.PanelBody;
        var TextControl = wp.components.TextControl;
        var TextareaControl = wp.components.TextareaControl;
        var RangeControl = wp.components.RangeControl;
        var Button = wp.components.Button;
        var ColorPicker = wp.components.ColorPicker;
        var MediaUpload = wp.blockEditor.MediaUpload;
        var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;

        registerBlockType('my-e-shop/about-section', {
            edit: function(props) {
                var attributes = props.attributes;
                var setAttributes = props.setAttributes;
                
                var blockProps = useBlockProps ? useBlockProps({
                    className: 'about-section-block',
                    style: {
                        '--about-bg-color': attributes.backgroundColor,
                        '--about-text-color': attributes.textColor,
                        '--about-overlay': attributes.overlay
                    }
                }) : {
                    className: 'about-section-block',
                    style: {
                        '--about-bg-color': attributes.backgroundColor,
                        '--about-text-color': attributes.textColor,
                        '--about-overlay': attributes.overlay
                    }
                };

                return createElement(Fragment, {}, [
                    createElement(InspectorControls, { key: 'inspector-controls' }, [
                        createElement(PanelBody, {
                            key: 'content-panel',
                            title: __('Контент', 'my-e-shop'),
                            initialOpen: true
                        }, [
                            createElement(TextControl, {
                                key: 'title-control',
                                label: __('Заголовок', 'my-e-shop'),
                                value: attributes.title || 'About the brand',
                                onChange: function(value) {
                                    setAttributes({ title: value });
                                }
                            }),

                            createElement(TextareaControl, {
                                key: 'description-control',
                                label: __('Описание', 'my-e-shop'),
                                value: attributes.description || '',
                                onChange: function(value) {
                                    setAttributes({ description: value });
                                },
                                rows: 4
                            }),

                            createElement(TextControl, {
                                key: 'button-text-control',
                                label: __('Текст кнопки', 'my-e-shop'),
                                value: attributes.buttonText || 'Learn more',
                                onChange: function(value) {
                                    setAttributes({ buttonText: value });
                                }
                            }),

                            createElement(TextControl, {
                                key: 'button-link-control',
                                label: __('Ссылка кнопки', 'my-e-shop'),
                                value: attributes.buttonLink || '#',
                                onChange: function(value) {
                                    setAttributes({ buttonLink: value });
                                },
                                placeholder: 'https://example.com'
                            })
                        ]),

                        createElement(PanelBody, {
                            key: 'design-panel',
                            title: __('Дизайн', 'my-e-shop'),
                            initialOpen: false
                        }, [
                            createElement('h4', {
                                key: 'bg-image-title',
                                style: { marginBottom: '10px' }
                            }, __('Фоновое изображение', 'my-e-shop')),

                            createElement(MediaUploadCheck, {
                                key: 'media-upload-check'
                            }, createElement(MediaUpload, {
                                key: 'bg-media-upload',
                                onSelect: function(media) {
                                    setAttributes({ 
                                        backgroundImage: media.url,
                                        backgroundImageId: media.id
                                    });
                                },
                                allowedTypes: ['image'],
                                value: attributes.backgroundImageId,
                                render: function(obj) {
                                    return createElement(Button, {
                                        onClick: obj.open,
                                        isPrimary: !attributes.backgroundImage,
                                        isSecondary: !!attributes.backgroundImage,
                                        style: { width: '100%', marginBottom: '10px' }
                                    }, attributes.backgroundImage ? __('Заменить изображение', 'my-e-shop') : __('Выбрать изображение', 'my-e-shop'));
                                }
                            })),
                            
                            attributes.backgroundImage && createElement('div', {
                                key: 'bg-image-preview',
                                style: { marginBottom: '15px' }
                            }, [
                                createElement('img', {
                                    key: 'preview-img',
                                    src: attributes.backgroundImage,
                                    style: { maxWidth: '100%', height: 'auto', borderRadius: '4px' }
                                }),
                                createElement('br', { key: 'br' }),
                                createElement(Button, {
                                    key: 'remove-bg-image',
                                    isDestructive: true,
                                    isSmall: true,
                                    onClick: function() {
                                        setAttributes({ 
                                            backgroundImage: '',
                                            backgroundImageId: 0
                                        });
                                    },
                                    style: { marginTop: '10px' }
                                }, __('Удалить изображение', 'my-e-shop'))
                            ]),

                            createElement('div', {
                                key: 'bg-color-section',
                                style: { marginTop: '20px' }
                            }, [
                                createElement('h4', {
                                    key: 'bg-color-label',
                                    style: { marginBottom: '8px' }
                                }, __('Цвет фона', 'my-e-shop')),
                                createElement(ColorPicker, {
                                    key: 'bg-color-picker',
                                    color: attributes.backgroundColor,
                                    onChangeComplete: function(color) {
                                        setAttributes({ backgroundColor: color.hex });
                                    }
                                })
                            ]),

                            createElement('div', {
                                key: 'text-color-section',
                                style: { marginTop: '20px' }
                            }, [
                                createElement('h4', {
                                    key: 'text-color-label',
                                    style: { marginBottom: '8px' }
                                }, __('Цвет текста', 'my-e-shop')),
                                createElement(ColorPicker, {
                                    key: 'text-color-picker',
                                    color: attributes.textColor,
                                    onChangeComplete: function(color) {
                                        setAttributes({ textColor: color.hex });
                                    }
                                })
                            ]),

                            attributes.backgroundImage && createElement(RangeControl, {
                                key: 'overlay-control',
                                label: __('Прозрачность наложения', 'my-e-shop'),
                                value: attributes.overlay,
                                onChange: function(value) {
                                    setAttributes({ overlay: value });
                                },
                                min: 0,
                                max: 1,
                                step: 0.1,
                                style: { marginTop: '20px' }
                            })
                        ])
                    ]),
                    
                    createElement('div', blockProps, [
                        createElement('div', {
                            key: 'about-section-container',
                            className: 'about-section-container',
                            style: {
                                backgroundImage: attributes.backgroundImage ? 'url(' + attributes.backgroundImage + ')' : 'none',
                                backgroundColor: attributes.backgroundColor,
                                color: attributes.textColor,
                                minHeight: '400px',
                                display: 'flex',
                                alignItems: 'center',
                                backgroundSize: 'cover',
                                backgroundPosition: 'center',
                                position: 'relative'
                            }
                        }, [
                            attributes.backgroundImage && createElement('div', {
                                key: 'overlay',
                                style: {
                                    position: 'absolute',
                                    top: 0,
                                    left: 0,
                                    right: 0,
                                    bottom: 0,
                                    backgroundColor: 'rgba(0, 0, 0, ' + attributes.overlay + ')',
                                    zIndex: 1
                                }
                            }),
                            
                            createElement('div', {
                                key: 'content-wrapper',
                                style: {
                                    maxWidth: '1200px',
                                    margin: '0 auto',
                                    padding: '60px 20px',
                                    position: 'relative',
                                    zIndex: 2
                                }
                            }, [
                                createElement('div', {
                                    key: 'content',
                                    style: {
                                        maxWidth: '600px'
                                    }
                                }, [
                                    createElement('h2', {
                                        key: 'title',
                                        style: {
                                            fontSize: '36px',
                                            fontWeight: 'bold',
                                            marginBottom: '20px',
                                            lineHeight: '1.2'
                                        }
                                    }, attributes.title),
                                    
                                    createElement('p', {
                                        key: 'description',
                                        style: {
                                            fontSize: '16px',
                                            lineHeight: '1.6',
                                            marginBottom: '30px',
                                            opacity: 0.9
                                        }
                                    }, attributes.description),
                                    
                                    createElement('a', {
                                        key: 'button',
                                        href: attributes.buttonLink,
                                        style: {
                                            display: 'inline-block',
                                            padding: '12px 24px',
                                            backgroundColor: 'rgba(255, 255, 255, 0.2)',
                                            color: attributes.textColor,
                                            textDecoration: 'none',
                                            border: '2px solid ' + attributes.textColor,
                                            borderRadius: '4px',
                                            fontWeight: '500',
                                            transition: 'all 0.3s ease'
                                        }
                                    }, attributes.buttonText)
                                ])
                            ])
                        ])
                    ])
                ]);
            },
            
            save: function() {
                return null;
            }
        });
    } else {
        // Fallback для старых версий
        console.warn('WordPress Block Editor components not available');
    }
})();
