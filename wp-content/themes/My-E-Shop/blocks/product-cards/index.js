// Product Cards Block - Version 2.1 - Based on Category Cards
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
        var RangeControl = wp.components.RangeControl;
        var SelectControl = wp.components.SelectControl;
        var Button = wp.components.Button;
        var ColorPicker = wp.components.ColorPicker;
        var MediaUpload = wp.blockEditor.MediaUpload;
        var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;

        registerBlockType('my-e-shop/product-cards', {
            edit: function(props) {
                var attributes = props.attributes;
                var setAttributes = props.setAttributes;
                var clientId = props.clientId;
                
                // Ensure products array exists
                if (!attributes.customImages || !Array.isArray(attributes.customImages)) {
                    setAttributes({ 
                        customImages: [
                            {
                                imageUrl: '',
                                imageId: 0,
                                title: 'Товар 1',
                                price: '$30.00',
                                link: '#'
                            }
                        ]
                    });
                    return createElement('div', null, 'Загрузка...');
                }
                
                var blockProps = useBlockProps ? useBlockProps({
                    className: 'product-cards-block',
                    style: {
                        '--product-cards-bg-color': attributes.backgroundColor
                    }
                }) : {
                    className: 'product-cards-block',
                    style: {
                        '--product-cards-bg-color': attributes.backgroundColor
                    }
                };

                function addProductCard() {
                    var newProducts = attributes.customImages.slice();
                    newProducts.push({
                        imageUrl: '',
                        imageId: 0,
                        title: 'Новый товар',
                        price: '$30.00',
                        link: '#'
                    });
                    setAttributes({ customImages: newProducts });
                }

                function updateProductCard(index, field, value) {
                    var newProducts = attributes.customImages.slice();
                    newProducts[index][field] = value;
                    setAttributes({ customImages: newProducts });
                }

                function removeProductCard(index) {
                    var newProducts = attributes.customImages.slice();
                    newProducts.splice(index, 1);
                    setAttributes({ customImages: newProducts });
                }

                return createElement(Fragment, {}, [
                    createElement(InspectorControls, { key: 'inspector-controls' }, [
                        createElement(PanelBody, {
                            key: 'settings-panel',
                            title: __('Настройки блока', 'my-e-shop'),
                            initialOpen: true
                        }, [
                            createElement('div', {
                                key: 'bg-color-label',
                                style: { 
                                    marginBottom: '8px', 
                                    fontWeight: '600' 
                                }
                            }, __('Цвет фона', 'my-e-shop')),
                            createElement(ColorPicker, {
                                key: 'bg-color-picker',
                                color: attributes.backgroundColor,
                                onChangeComplete: function(color) {
                                    setAttributes({ backgroundColor: color.hex });
                                }
                            }),
                            
                            createElement(TextControl, {
                                key: 'block-title-control',
                                label: __('Заголовок блока', 'my-e-shop'),
                                value: attributes.blockTitle || 'Best Sellers',
                                onChange: function(value) {
                                    setAttributes({ blockTitle: value });
                                },
                                style: { marginTop: '20px' }
                            }),

                            createElement(TextControl, {
                                key: 'block-subtitle-control',
                                label: __('Подзаголовок блока', 'my-e-shop'),
                                value: attributes.blockSubtitle || 'Your aura in cotton',
                                onChange: function(value) {
                                    setAttributes({ blockSubtitle: value });
                                },
                                style: { marginTop: '10px' }
                            }),

                            createElement(TextControl, {
                                key: 'view-all-link-control',
                                label: __('Ссылка "VIEW ALL"', 'my-e-shop'),
                                value: attributes.viewAllLink || '#',
                                onChange: function(value) {
                                    setAttributes({ viewAllLink: value });
                                },
                                style: { marginTop: '10px' },
                                placeholder: 'https://example.com/products'
                            }),

                            createElement(RangeControl, {
                                key: 'columns-control',
                                label: __('Количество колонок', 'my-e-shop'),
                                value: attributes.columns || 4,
                                onChange: function(value) {
                                    setAttributes({ columns: value });
                                },
                                min: 1,
                                max: 6
                            }),

                            createElement(SelectControl, {
                                key: 'card-style-control',
                                label: __('Стиль карточек', 'my-e-shop'),
                                value: attributes.cardStyle || 'classic',
                                onChange: function(value) {
                                    setAttributes({ cardStyle: value });
                                },
                                options: [
                                    { label: 'Классический', value: 'classic' },
                                    { label: 'Минималистичный', value: 'minimal' },
                                    { label: 'Современный', value: 'modern' },
                                    { label: 'Элегантный', value: 'elegant' }
                                ]
                            })
                        ]),
                        
                        createElement(PanelBody, {
                            key: 'products-panel',
                            title: __('Карточки товаров', 'my-e-shop'),
                            initialOpen: true
                        }, [
                            createElement(Button, {
                                key: 'add-product-button',
                                isPrimary: true,
                                onClick: addProductCard,
                                style: { marginBottom: '20px' }
                            }, __('+ Добавить товар', 'my-e-shop'))
                        ].concat(
                            attributes.customImages.map(function(product, index) {
                                return createElement('div', {
                                    key: 'product-' + index,
                                    style: { 
                                        border: '1px solid #ddd', 
                                        padding: '15px', 
                                        marginBottom: '15px',
                                        borderRadius: '4px'
                                    }
                                }, [
                                    createElement('h4', {
                                        key: 'product-title-' + index,
                                        style: { marginTop: '0', marginBottom: '15px' }
                                    }, __('Товар ', 'my-e-shop') + (index + 1)),
                                    
                                    createElement(MediaUploadCheck, {
                                        key: 'media-upload-check-' + index
                                    }, createElement(MediaUpload, {
                                        key: 'media-upload-' + index,
                                        onSelect: function(media) {
                                            updateProductCard(index, 'imageUrl', media.url);
                                            updateProductCard(index, 'imageId', media.id);
                                        },
                                        allowedTypes: ['image'],
                                        value: product.imageId,
                                        render: function(obj) {
                                            return createElement(Button, {
                                                onClick: obj.open,
                                                isPrimary: !product.imageUrl,
                                                isSecondary: !!product.imageUrl
                                            }, product.imageUrl ? __('Заменить изображение', 'my-e-shop') : __('Выбрать изображение', 'my-e-shop'));
                                        }
                                    })),
                                    
                                    product.imageUrl && createElement('div', {
                                        key: 'image-preview-' + index,
                                        style: { marginTop: '10px', marginBottom: '10px' }
                                    }, [
                                        createElement('img', {
                                            key: 'preview-img-' + index,
                                            src: product.imageUrl,
                                            style: { maxWidth: '100px', height: 'auto' }
                                        }),
                                        createElement(Button, {
                                            key: 'remove-image-' + index,
                                            isDestructive: true,
                                            onClick: function() {
                                                updateProductCard(index, 'imageUrl', '');
                                                updateProductCard(index, 'imageId', 0);
                                            },
                                            style: { marginLeft: '10px' }
                                        }, __('Удалить изображение', 'my-e-shop'))
                                    ]),
                                    
                                    createElement(TextControl, {
                                        key: 'product-name-input-' + index,
                                        label: __('Название товара', 'my-e-shop'),
                                        value: product.title,
                                        onChange: function(value) {
                                            updateProductCard(index, 'title', value);
                                        }
                                    }),
                                    
                                    createElement(TextControl, {
                                        key: 'product-price-input-' + index,
                                        label: __('Цена', 'my-e-shop'),
                                        value: product.price,
                                        onChange: function(value) {
                                            updateProductCard(index, 'price', value);
                                        }
                                    }),
                                    
                                    createElement(TextControl, {
                                        key: 'product-link-input-' + index,
                                        label: __('Ссылка', 'my-e-shop'),
                                        value: product.link,
                                        onChange: function(value) {
                                            updateProductCard(index, 'link', value);
                                        }
                                    }),
                                    
                                    createElement(Button, {
                                        key: 'remove-product-' + index,
                                        isDestructive: true,
                                        onClick: function() {
                                            removeProductCard(index);
                                        },
                                        style: { marginTop: '10px' }
                                    }, __('Удалить товар', 'my-e-shop'))
                                ]);
                            })
                        ))
                    ]),
                    
                    createElement('div', blockProps, [
                        createElement('div', {
                            key: 'product-cards-container',
                            className: 'product-cards-container',
                            style: {
                                backgroundColor: attributes.backgroundColor,
                                padding: '40px 20px',
                                textAlign: 'center'
                            }
                        }, [
                            createElement('div', {
                                key: 'product-cards-header',
                                style: {
                                    display: 'flex',
                                    justifyContent: 'space-between',
                                    alignItems: 'flex-start',
                                    marginBottom: '40px',
                                    textAlign: 'left'
                                }
                            }, [
                                createElement('div', {
                                    key: 'product-cards-titles'
                                }, [
                                    createElement('h2', {
                                        key: 'block-title',
                                        className: 'block-title',
                                        style: {
                                            fontSize: '32px',
                                            fontWeight: 'bold',
                                            marginBottom: '8px',
                                            color: '#333',
                                            margin: '0 0 8px 0'
                                        }
                                    }, attributes.blockTitle),
                                    
                                    createElement('p', {
                                        key: 'block-subtitle',
                                        className: 'block-subtitle',
                                        style: {
                                            fontSize: '16px',
                                            color: '#666',
                                            margin: '0'
                                        }
                                    }, attributes.blockSubtitle)
                                ]),
                                
                                createElement('a', {
                                    key: 'view-all-btn',
                                    href: attributes.viewAllLink || '#',
                                    style: {
                                        display: 'flex',
                                        alignItems: 'center',
                                        gap: '8px',
                                        color: '#333',
                                        textDecoration: 'none',
                                        fontSize: '14px',
                                        fontWeight: '500',
                                        textTransform: 'uppercase',
                                        letterSpacing: '1px',
                                        transition: 'color 0.3s ease'
                                    }
                                }, [
                                    'VIEW ALL',
                                    createElement('svg', {
                                        key: 'arrow-icon',
                                        width: '16',
                                        height: '16',
                                        viewBox: '0 0 24 24',
                                        fill: 'none'
                                    }, createElement('path', {
                                        d: 'M5 12H19M19 12L12 5M19 12L12 19',
                                        stroke: 'currentColor',
                                        strokeWidth: '2',
                                        strokeLinecap: 'round',
                                        strokeLinejoin: 'round'
                                    }))
                                ])
                            ]),
                            
                            createElement('div', {
                                key: 'products-grid',
                                className: 'products-grid',
                                style: {
                                    display: 'grid',
                                    gridTemplateColumns: 'repeat(' + (attributes.columns || 4) + ', 1fr)',
                                    gap: '15px',
                                    maxWidth: '1200px',
                                    margin: '0 auto'
                                }
                            }, attributes.customImages.map(function(product, index) {
                                return createElement('div', {
                                    key: 'product-card-' + index,
                                    className: 'product-card',
                                    style: {
                                        backgroundColor: '#fff',
                                        borderRadius: '8px',
                                        overflow: 'hidden',
                                        boxShadow: '0 2px 10px rgba(0,0,0,0.1)',
                                        transition: 'transform 0.3s ease'
                                    }
                                }, [
                                    createElement('div', {
                                        key: 'product-image-container-' + index,
                                        className: 'product-image-container',
                                        style: {
                                            position: 'relative',
                                            height: '280px',
                                            overflow: 'hidden',
                                            borderRadius: '8px 8px 0 0'
                                        }
                                    }, [
                                        product.imageUrl ? 
                                            createElement('img', {
                                                key: 'product-image-' + index,
                                                src: product.imageUrl,
                                                alt: product.title,
                                                style: {
                                                    width: '100%',
                                                    height: '100%',
                                                    objectFit: 'cover'
                                                }
                                            }) :
                                            createElement('div', {
                                                key: 'product-placeholder-' + index,
                                                style: {
                                                    width: '100%',
                                                    height: '100%',
                                                    backgroundColor: '#f8f9fa',
                                                    display: 'flex',
                                                    alignItems: 'center',
                                                    justifyContent: 'center',
                                                    color: '#6c757d'
                                                }
                                            }, '🛍️ Изображение товара')
                                    ]),
                                    
                                    createElement('div', {
                                        key: 'product-content-' + index,
                                        className: 'product-content',
                                        style: {
                                            padding: '15px',
                                            background: '#fff',
                                            borderRadius: '0 0 8px 8px'
                                        }
                                    }, [
                                        createElement('h3', {
                                            key: 'product-title-display-' + index,
                                            style: {
                                                margin: '0 0 8px 0',
                                                fontSize: '14px',
                                                fontWeight: '500',
                                                color: '#333',
                                                textTransform: 'uppercase',
                                                letterSpacing: '0.5px'
                                            }
                                        }, product.title),
                                        
                                        createElement('div', {
                                            key: 'product-price-display-' + index,
                                            style: {
                                                fontSize: '14px',
                                                fontWeight: '600',
                                                color: '#333',
                                                margin: '0'
                                            }
                                        }, product.price)
                                    ])
                                ]);
                            }))
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
