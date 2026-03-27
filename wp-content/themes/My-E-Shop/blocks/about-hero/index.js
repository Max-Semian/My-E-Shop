(function() {
    'use strict';

    var __ = wp.i18n.__;
    var registerBlockType = wp.blocks.registerBlockType;
    var createElement = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var useBlockProps = wp.blockEditor.useBlockProps;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var RichText = wp.blockEditor.RichText;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
    var PanelBody = wp.components.PanelBody;
    var Button = wp.components.Button;
    var RangeControl = wp.components.RangeControl;
    var ColorPicker = wp.components.ColorPicker;
    var TextControl = wp.components.TextControl;

    registerBlockType('my-e-shop/about-hero', {
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var title = attributes.title;
            var buttonText = attributes.buttonText;
            var buttonUrl = attributes.buttonUrl;
            var backgroundImage = attributes.backgroundImage;
            var textColor = attributes.textColor;
            var overlayOpacity = attributes.overlayOpacity;

            var blockProps = useBlockProps({
                className: 'about-hero-block'
            });

            function onSelectImage(media) {
                setAttributes({
                    backgroundImage: {
                        id: media.id,
                        url: media.url,
                        alt: media.alt
                    }
                });
            }

            function removeImage() {
                setAttributes({
                    backgroundImage: {
                        id: 0,
                        url: '',
                        alt: ''
                    }
                });
            }

            return createElement(Fragment, {}, [
                createElement(InspectorControls, { key: 'inspector' }, [
                    createElement(PanelBody, {
                        key: 'settings',
                        title: __('Настройки блока', 'My-E-Shop'),
                        initialOpen: true
                    }, [
                        createElement(MediaUploadCheck, { key: 'media-check' },
                            createElement(MediaUpload, {
                                key: 'media-upload',
                                onSelect: onSelectImage,
                                allowedTypes: ['image'],
                                value: backgroundImage.id,
                                render: function(obj) {
                                    return createElement('div', {
                                        className: 'media-upload-container'
                                    }, [
                                        createElement(Button, {
                                            key: 'select-button',
                                            onClick: obj.open,
                                            variant: 'secondary',
                                            style: { marginBottom: '10px' }
                                        }, backgroundImage.url ? 
                                            __('Изменить фоновое изображение', 'My-E-Shop') : 
                                            __('Выбрать фоновое изображение', 'My-E-Shop')
                                        ),
                                        backgroundImage.url ? createElement('div', { key: 'image-preview' }, [
                                            createElement('img', {
                                                key: 'preview-img',
                                                src: backgroundImage.url,
                                                alt: backgroundImage.alt,
                                                style: {
                                                    width: '100%',
                                                    height: '100px',
                                                    objectFit: 'cover',
                                                    marginBottom: '10px'
                                                }
                                            }),
                                            createElement(Button, {
                                                key: 'remove-button',
                                                onClick: removeImage,
                                                variant: 'secondary',
                                                isDestructive: true
                                            }, __('Удалить изображение', 'My-E-Shop'))
                                        ]) : null
                                    ]);
                                }
                            })
                        ),

                        createElement('div', {
                            key: 'color-picker-container',
                            style: { marginTop: '20px' }
                        }, [
                            createElement('label', { key: 'color-label' }, __('Цвет текста', 'My-E-Shop')),
                            createElement(ColorPicker, {
                                key: 'color-picker',
                                color: textColor,
                                onChange: function(color) {
                                    setAttributes({ textColor: color });
                                }
                            })
                        ]),

                        createElement(RangeControl, {
                            key: 'opacity-range',
                            label: __('Прозрачность наложения', 'My-E-Shop'),
                            value: overlayOpacity,
                            onChange: function(value) {
                                setAttributes({ overlayOpacity: value });
                            },
                            min: 0,
                            max: 1,
                            step: 0.1
                        })
                    ]),

                    createElement(PanelBody, {
                        key: 'button-settings',
                        title: __('Настройки кнопки', 'My-E-Shop'),
                        initialOpen: true
                    }, [
                        createElement(TextControl, {
                            key: 'button-text',
                            label: __('Текст кнопки', 'My-E-Shop'),
                            value: buttonText,
                            onChange: function(value) {
                                setAttributes({ buttonText: value });
                            },
                            placeholder: __('Узнать больше', 'My-E-Shop')
                        }),

                        createElement(TextControl, {
                            key: 'button-url',
                            label: __('Ссылка кнопки', 'My-E-Shop'),
                            value: buttonUrl,
                            onChange: function(value) {
                                setAttributes({ buttonUrl: value });
                            },
                            placeholder: __('https://example.com', 'My-E-Shop'),
                            type: 'url'
                        })
                    ])
                ]),

                createElement('div', blockProps, [
                    createElement('div', {
                        key: 'hero-container',
                        className: 'about-hero-container'
                    }, [
                        backgroundImage.url ? createElement('div', {
                            key: 'background',
                            className: 'about-hero-background',
                            style: {
                                backgroundImage: 'url(' + backgroundImage.url + ')'
                            }
                        }, [
                            createElement('div', {
                                key: 'overlay',
                                className: 'about-hero-overlay',
                                style: {
                                    backgroundColor: 'rgba(0, 0, 0, ' + overlayOpacity + ')'
                                }
                            })
                        ]) : null,
                        
                        createElement('div', {
                            key: 'content',
                            className: 'about-hero-content'
                        }, [
                            createElement(RichText, {
                                key: 'title',
                                tagName: 'h1',
                                className: 'about-hero-title',
                                value: title,
                                onChange: function(value) {
                                    setAttributes({ title: value });
                                },
                                placeholder: __('О НАС', 'My-E-Shop'),
                                style: {
                                    color: textColor
                                }
                            }),
                            
                            createElement('div', {
                                key: 'button-wrapper',
                                className: 'about-hero-button-wrapper'
                            }, [
                                createElement(RichText, {
                                    key: 'button',
                                    tagName: 'span',
                                    className: 'about-hero-button',
                                    value: buttonText,
                                    onChange: function(value) {
                                        setAttributes({ buttonText: value });
                                    },
                                    placeholder: __('Узнать больше', 'My-E-Shop')
                                })
                            ])
                        ])
                    ])
                ])
            ]);
        }
    });
})();
