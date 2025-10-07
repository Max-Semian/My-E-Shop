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

    registerBlockType('my-e-shop/category-hero', {
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var categoryTitle = attributes.categoryTitle;
            var backgroundImage = attributes.backgroundImage;
            var textColor = attributes.textColor;
            var overlayOpacity = attributes.overlayOpacity;

            var blockProps = useBlockProps({
                className: 'category-hero-block'
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
                        title: __('Настройки заголовка', 'My-E-Shop'),
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
                    ])
                ]),

                createElement('div', blockProps, [
                    createElement('div', {
                        key: 'hero-container',
                        className: 'category-hero-container'
                    }, [
                        backgroundImage.url ? createElement('div', {
                            key: 'background',
                            className: 'category-hero-background',
                            style: {
                                backgroundImage: 'url(' + backgroundImage.url + ')'
                            }
                        }, [
                            createElement('div', {
                                key: 'overlay',
                                className: 'category-hero-overlay',
                                style: {
                                    backgroundColor: 'rgba(0, 0, 0, ' + overlayOpacity + ')'
                                }
                            })
                        ]) : null,
                        
                        createElement('div', {
                            key: 'content',
                            className: 'category-hero-content'
                        }, [
                            createElement(RichText, {
                                key: 'title',
                                tagName: 'h1',
                                className: 'category-hero-title',
                                value: categoryTitle,
                                onChange: function(value) {
                                    setAttributes({ categoryTitle: value });
                                },
                                placeholder: __('Введите название категории...', 'My-E-Shop'),
                                style: {
                                    color: textColor
                                }
                            })
                        ])
                    ])
                ])
            ]);
        }
    });
})();