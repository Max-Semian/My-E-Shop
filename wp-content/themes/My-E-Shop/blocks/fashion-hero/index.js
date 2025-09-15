(function() {
    'use strict';

    var registerBlockType = wp.blocks.registerBlockType;
    var createElement = wp.element.createElement;
    var useBlockProps = wp.blockEditor.useBlockProps;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var Button = wp.components.Button;
    var __ = wp.i18n.__;

    registerBlockType('my-e-shop/fashion-hero', {
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var blockProps = useBlockProps();

            // Функция для установки отдельного изображения
            function setImage(index, media) {
                var newImages = Object.assign({}, attributes.images);
                newImages['image' + (index + 1)] = {
                    id: media.id,
                    url: media.url,
                    alt: media.alt || ''
                };
                setAttributes({ images: newImages });
            }

            // Функция для удаления отдельного изображения
            function removeImage(index) {
                var newImages = Object.assign({}, attributes.images);
                delete newImages['image' + (index + 1)];
                setAttributes({ images: newImages });
            }

            return createElement('div', blockProps, [
                createElement(InspectorControls, { key: 'inspector' }, [
                    createElement(PanelBody, {
                        key: 'content',
                        title: __('Content Settings', 'My-E-Shop'),
                        initialOpen: true
                    }, [
                        createElement(TextControl, {
                            key: 'title',
                            label: __('Title', 'My-E-Shop'),
                            value: attributes.title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            }
                        }),
                        createElement(TextareaControl, {
                            key: 'subtitle',
                            label: __('Subtitle', 'My-E-Shop'),
                            value: attributes.subtitle,
                            onChange: function(value) {
                                setAttributes({ subtitle: value });
                            }
                        }),
                        createElement(TextControl, {
                            key: 'buttonText',
                            label: __('Button Text', 'My-E-Shop'),
                            value: attributes.buttonText,
                            onChange: function(value) {
                                setAttributes({ buttonText: value });
                            }
                        }),
                        createElement(TextControl, {
                            key: 'buttonUrl',
                            label: __('Button URL', 'My-E-Shop'),
                            value: attributes.buttonUrl,
                            onChange: function(value) {
                                setAttributes({ buttonUrl: value });
                            }
                        })
                    ]),
                    createElement(PanelBody, {
                        key: 'images',
                        title: __('Background Images (7 positions)', 'My-E-Shop'),
                        initialOpen: false
                    }, 
                    // Создаем 7 отдельных MediaUpload для каждой позиции
                    [1, 2, 3, 4, 5, 6, 7].map(function(position, index) {
                        var imageKey = 'image' + position;
                        var currentImage = attributes.images[imageKey];
                        
                        return createElement('div', {
                            key: 'image-' + position,
                            style: {
                                marginBottom: '20px',
                                padding: '15px',
                                border: '1px solid #ddd',
                                borderRadius: '6px',
                                backgroundColor: '#f9f9f9'
                            }
                        }, [
                            createElement('h4', {
                                key: 'title',
                                style: { margin: '0 0 10px 0', fontSize: '14px', fontWeight: '600' }
                            }, __('Fashion Image ' + position, 'My-E-Shop')),
                            
                            createElement(MediaUpload, {
                                key: 'upload',
                                onSelect: function(media) {
                                    setImage(index, media);
                                },
                                allowedTypes: ['image'],
                                value: currentImage ? currentImage.id : '',
                                render: function(obj) {
                                    return createElement('div', {}, [
                                        currentImage && createElement('div', {
                                            key: 'preview',
                                            style: { marginBottom: '10px' }
                                        }, [
                                            createElement('img', {
                                                src: currentImage.url,
                                                alt: currentImage.alt,
                                                style: {
                                                    width: '100%',
                                                    maxWidth: '150px',
                                                    height: '80px',
                                                    objectFit: 'cover',
                                                    borderRadius: '4px'
                                                }
                                            })
                                        ]),
                                        createElement('div', {
                                            key: 'buttons',
                                            style: { display: 'flex', gap: '8px' }
                                        }, [
                                            createElement(Button, {
                                                key: 'select',
                                                onClick: obj.open,
                                                variant: currentImage ? 'secondary' : 'primary'
                                            }, currentImage ? __('Replace Image', 'My-E-Shop') : __('Select Image', 'My-E-Shop')),
                                            
                                            currentImage && createElement(Button, {
                                                key: 'remove',
                                                onClick: function() {
                                                    removeImage(index);
                                                },
                                                variant: 'secondary',
                                                isDestructive: true
                                            }, __('Remove', 'My-E-Shop'))
                                        ])
                                    ]);
                                }
                            })
                        ]);
                    }))
                ]),
                createElement('div', {
                    key: 'preview',
                    className: 'fashion-hero-preview',
                    style: {
                        position: 'relative',
                        minHeight: '400px',
                        background: '#f0f0f0',
                        display: 'flex',
                        alignItems: 'center',
                        justifyContent: 'center',
                        border: '2px dashed #ddd',
                        borderRadius: '8px'
                    }
                }, [
                    // Отображаем 7 позиций для изображений
                    createElement('div', {
                        key: 'images-grid',
                        style: {
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            right: 0,
                            bottom: 0,
                            display: 'flex',
                            opacity: 0.7
                        }
                    }, [1, 2, 3, 4, 5, 6, 7].map(function(position, index) {
                        var imageKey = 'image' + position;
                        var currentImage = attributes.images[imageKey];
                        
                        return createElement('div', {
                            key: index,
                            style: {
                                flex: 1,
                                height: '100%',
                                overflow: 'hidden',
                                border: currentImage ? 'none' : '1px dashed #ccc',
                                display: 'flex',
                                alignItems: 'center',
                                justifyContent: 'center',
                                backgroundColor: currentImage ? 'transparent' : '#f9f9f9'
                            }
                        }, currentImage ? createElement('img', {
                            src: currentImage.url,
                            alt: currentImage.alt,
                            style: {
                                width: '100%',
                                height: '100%',
                                objectFit: 'cover'
                            }
                        }) : createElement('span', {
                            style: {
                                color: '#999',
                                fontSize: '12px',
                                textAlign: 'center'
                            }
                        }, position));
                    })),
                    createElement('div', {
                        key: 'overlay',
                        style: {
                            position: 'absolute',
                            top: 0,
                            left: 0,
                            right: 0,
                            bottom: 0,
                            background: 'linear-gradient(45deg, rgba(0, 0, 0, 0.4) 0%, rgba(0, 0, 0, 0.2) 50%, rgba(0, 0, 0, 0.4) 100%)',
                            zIndex: 1
                        }
                    }),
                    createElement('div', {
                        key: 'content',
                        style: {
                            position: 'relative',
                            zIndex: 2,
                            textAlign: 'center',
                            color: 'white',
                            padding: '40px 20px'
                        }
                    }, [
                        createElement('h1', {
                            key: 'title',
                            style: {
                                fontSize: '2.5rem',
                                fontWeight: '700',
                                marginBottom: '20px',
                                textTransform: 'uppercase',
                                letterSpacing: '3px'
                            }
                        }, attributes.title),
                        createElement('p', {
                            key: 'subtitle',
                            style: {
                                fontSize: '1.2rem',
                                marginBottom: '30px',
                                opacity: 0.95
                            }
                        }, attributes.subtitle),
                        createElement('a', {
                            key: 'button',
                            href: '#',
                            style: {
                                display: 'inline-block',
                                padding: '12px 35px',
                                background: '#2C2C2C',
                                color: '#D85AFF',
                                textDecoration: 'none',
                                fontSize: '16px',
                                textTransform: 'uppercase',
                                letterSpacing: '1px',
                                border: '1px solid #a855f7',
                                borderRadius: '4px'
                            }
                        }, attributes.buttonText)
                    ])
                ])
            ]);
        },

        save: function() {
            return null; // Server-side rendering
        }
    });
})();
