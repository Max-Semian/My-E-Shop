// Newsletter Subscription Block
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

        registerBlockType('my-e-shop/newsletter-subscription', {
            edit: function(props) {
                var attributes = props.attributes;
                var setAttributes = props.setAttributes;
                
                var blockProps = useBlockProps ? useBlockProps({
                    className: 'my-e-shop-newsletter-subscription-block',
                    style: {
                        '--newsletter-button-color': attributes.buttonColor,
                        '--newsletter-text-color': attributes.textColor
                    }
                }) : {
                    className: 'my-e-shop-newsletter-subscription-block',
                    style: {
                        '--newsletter-button-color': attributes.buttonColor,
                        '--newsletter-text-color': attributes.textColor
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
                                key: 'main-title',
                                label: __('Главный заголовок', 'my-e-shop'),
                                value: attributes.mainTitle,
                                onChange: function(value) {
                                    setAttributes({ mainTitle: value });
                                }
                            }),
                            
                            createElement(TextControl, {
                                key: 'offer-title',
                                label: __('Заголовок предложения', 'my-e-shop'),
                                value: attributes.offerTitle,
                                onChange: function(value) {
                                    setAttributes({ offerTitle: value });
                                }
                            }),
                            
                            createElement(TextareaControl, {
                                key: 'description',
                                label: __('Описание', 'my-e-shop'),
                                value: attributes.description,
                                onChange: function(value) {
                                    setAttributes({ description: value });
                                }
                            }),
                            
                            createElement(TextControl, {
                                key: 'placeholder-text',
                                label: __('Текст placeholder', 'my-e-shop'),
                                value: attributes.placeholderText,
                                onChange: function(value) {
                                    setAttributes({ placeholderText: value });
                                }
                            }),
                            
                            createElement(TextControl, {
                                key: 'button-text',
                                label: __('Текст кнопки', 'my-e-shop'),
                                value: attributes.buttonText,
                                onChange: function(value) {
                                    setAttributes({ buttonText: value });
                                }
                            })
                        ]),
                        
                        createElement(PanelBody, {
                            key: 'design-panel',
                            title: __('Дизайн', 'my-e-shop'),
                            initialOpen: false
                        }, [
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
                                    }, attributes.backgroundImage ? __('Заменить фоновое изображение', 'my-e-shop') : __('Выбрать фоновое изображение', 'my-e-shop'));
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
                                key: 'button-color-section',
                                style: { marginTop: '20px' }
                            }, [
                                createElement('h4', {
                                    key: 'button-color-title',
                                    style: { marginBottom: '10px' }
                                }, __('Цвет кнопки', 'my-e-shop')),
                                createElement(ColorPicker, {
                                    key: 'button-color-picker',
                                    color: attributes.buttonColor,
                                    onChange: function(color) {
                                        setAttributes({ buttonColor: color });
                                    }
                                })
                            ]),
                            
                            createElement('div', {
                                key: 'text-color-section',
                                style: { marginTop: '20px' }
                            }, [
                                createElement('h4', {
                                    key: 'text-color-title',
                                    style: { marginBottom: '10px' }
                                }, __('Цвет текста', 'my-e-shop')),
                                createElement(ColorPicker, {
                                    key: 'text-color-picker',
                                    color: attributes.textColor,
                                    onChange: function(color) {
                                        setAttributes({ textColor: color });
                                    }
                                })
                            ])
                        ])
                    ]),
                    
                    createElement('div', blockProps, [
                        createElement('div', {
                            key: 'newsletter-container',
                            className: 'my-e-shop-newsletter-subscription-container',
                            style: {
                                backgroundImage: attributes.backgroundImage ? 'url(' + attributes.backgroundImage + ')' : 'none',
                                color: attributes.textColor
                            }
                        }, [
                            attributes.backgroundImage && createElement('div', {
                                key: 'overlay',
                                className: 'my-e-shop-newsletter-overlay',
                                style: {
                                    backgroundColor: 'rgba(0, 0, 0, 0)'
                                }
                            }),
                            
                            createElement('div', {
                                key: 'content',
                                className: 'my-e-shop-newsletter-content'
                            }, [
                                createElement('div', {
                                    key: 'left-content',
                                    className: 'my-e-shop-newsletter-left'
                                }, [
                                    createElement('h2', {
                                        key: 'main-title',
                                        className: 'my-e-shop-newsletter-main-title',
                                        style: { color: attributes.textColor }
                                    }, attributes.mainTitle),
                                    
                                    createElement('div', {
                                        key: 'read-blog-btn',
                                        className: 'my-e-shop-read-blog-button'
                                    }, 'READ IN BLOG →')
                                ]),
                                
                                createElement('div', {
                                    key: 'right-content',
                                    className: 'my-e-shop-newsletter-right'
                                }, [
                                    createElement('h3', {
                                        key: 'offer-title',
                                        className: 'my-e-shop-newsletter-offer-title',
                                        style: { color: attributes.textColor }
                                    }, attributes.offerTitle),
                                    
                                    createElement('p', {
                                        key: 'description',
                                        className: 'my-e-shop-newsletter-description',
                                        style: { color: attributes.textColor }
                                    }, attributes.description),
                                    
                                    createElement('div', {
                                        key: 'form',
                                        className: 'my-e-shop-newsletter-form'
                                    }, [
                                        createElement('input', {
                                            key: 'email-input',
                                            type: 'email',
                                            placeholder: attributes.placeholderText,
                                            className: 'my-e-shop-newsletter-email-input'
                                        }),
                                        createElement('button', {
                                            key: 'submit-button',
                                            type: 'button',
                                            className: 'my-e-shop-newsletter-submit-button',
                                            style: { backgroundColor: attributes.buttonColor }
                                        }, attributes.buttonText)
                                    ])
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