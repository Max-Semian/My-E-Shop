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
        var Button = wp.components.Button;
        var ColorPicker = wp.components.ColorPicker;
        var MediaUpload = wp.blockEditor.MediaUpload;
        var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;

        registerBlockType('my-e-shop/category-cards', {
            edit: function(props) {
                var attributes = props.attributes;
                var setAttributes = props.setAttributes;
                var clientId = props.clientId;
                
                // Ensure categories array exists
                if (!attributes.categories || !Array.isArray(attributes.categories)) {
                    setAttributes({ 
                        categories: [
                            {
                                imageUrl: '',
                                imageId: 0,
                                iconUrl: '',
                                iconId: 0,
                                title: 'STEP EXECUTING',
                                subtitle: 'START EXECUTING',
                                titleColor: '#ffffff',
                                url: '#'
                            }
                        ]
                    });
                    return createElement('div', null, 'Loading...');
                }

                // Ensure all categories have titleColor field
                var updatedCategories = attributes.categories.map(function(category) {
                    if (!category.titleColor) {
                        return { ...category, titleColor: '#ffffff' };
                    }
                    return category;
                });
                
                if (JSON.stringify(updatedCategories) !== JSON.stringify(attributes.categories)) {
                    setAttributes({ categories: updatedCategories });
                }
                
                var blockProps = useBlockProps ? useBlockProps({
                    className: 'category-cards-block',
                    style: {
                        '--category-cards-bg-color': attributes.backgroundColor
                    }
                }) : {
                    className: 'category-cards-block',
                    style: {
                        '--category-cards-bg-color': attributes.backgroundColor
                    }
                };

                function addCategoryCard() {
                    var newCategories = attributes.categories.slice();
                    newCategories.push({
                        imageUrl: '',
                        imageId: 0,
                        iconUrl: '',
                        iconId: 0,
                        title: 'New category',
                        subtitle: 'Description',
                        titleColor: '#ffffff',
                        url: '#'
                    });
                    setAttributes({ categories: newCategories });
                }

                function updateCategoryCard(index, field, value) {
                    var newCategories = attributes.categories.slice();
                    newCategories[index][field] = value;
                    setAttributes({ categories: newCategories });
                }

                function removeCategoryCard(index) {
                    var newCategories = attributes.categories.slice();
                    newCategories.splice(index, 1);
                    setAttributes({ categories: newCategories });
                }

                return createElement(Fragment, {}, [
                    createElement(InspectorControls, { key: 'inspector-controls' }, [
                        createElement(PanelBody, {
                            key: 'settings-panel',
                            title: __('Block settings', 'my-e-shop'),
                            initialOpen: true
                        }, [
                            createElement('div', {
                                key: 'bg-color-label',
                                style: { 
                                    marginBottom: '8px', 
                                    fontWeight: '600' 
                                }
                            }, __('Background color', 'my-e-shop')),
                            createElement(ColorPicker, {
                                key: 'bg-color-picker',
                                color: attributes.backgroundColor,
                                onChangeComplete: function(color) {
                                    setAttributes({ backgroundColor: color.hex });
                                }
                            }),
                            
                            createElement(TextControl, {
                                key: 'block-title-control',
                                label: __('Block title', 'my-e-shop'),
                                value: attributes.blockTitle || 'Collection',
                                onChange: function(value) {
                                    setAttributes({ blockTitle: value });
                                },
                                style: { marginTop: '20px' }
                            }),

                            createElement(TextControl, {
                                key: 'block-subtitle-control',
                                label: __('Block subtitle', 'my-e-shop'),
                                value: attributes.blockSubtitle || 'Wear your mark. Feel your power',
                                onChange: function(value) {
                                    setAttributes({ blockSubtitle: value });
                                },
                                style: { marginTop: '10px' }
                            })
                        ]),
                        
                        createElement(PanelBody, {
                            key: 'categories-panel',
                            title: __('Category cards', 'my-e-shop'),
                            initialOpen: true
                        }, [
                            createElement(Button, {
                                key: 'add-category-button',
                                isPrimary: true,
                                onClick: addCategoryCard,
                                style: { marginBottom: '20px' }
                            }, __('+ Add category', 'my-e-shop'))
                        ].concat(
                            attributes.categories.map(function(category, index) {
                                return createElement('div', {
                                    key: 'category-' + index,
                                    style: { 
                                        border: '1px solid #ddd', 
                                        padding: '15px', 
                                        marginBottom: '15px',
                                        borderRadius: '4px'
                                    }
                                }, [
                                    createElement('h4', {
                                        key: 'category-title-' + index,
                                        style: { marginTop: '0', marginBottom: '15px' }
                                    }, __('Category ', 'my-e-shop') + (index + 1)),
                                    
                                    createElement(MediaUploadCheck, {
                                        key: 'media-upload-check-' + index
                                    }, createElement(MediaUpload, {
                                        key: 'media-upload-' + index,
                                        onSelect: function(media) {
                                            if (media && media.url) {
                                                var newCategories = attributes.categories.map(function(category, i) {
                                                    if (i === index) {
                                                        return { 
                                                            ...category, 
                                                            imageUrl: media.url,
                                                            imageId: media.id
                                                        };
                                                    }
                                                    return category;
                                                });
                                                setAttributes({ categories: newCategories });
                                            }
                                        },
                                        allowedTypes: ['image'],
                                        value: category.imageId,
                                        render: function(obj) {
                                            return createElement('div', {
                                                key: 'media-container-' + index
                                            }, [
                                                createElement(Button, {
                                                    key: 'select-image-' + index,
                                                    onClick: obj.open,
                                                    isSecondary: true,
                                                    style: { marginBottom: '10px', marginRight: '10px' }
                                                }, category.imageUrl ? __('Change image', 'my-e-shop') : __('Select image', 'my-e-shop'))
                                            ]);
                                        }
                                    })),
                                    
                                    category.imageUrl && createElement('div', {
                                        key: 'image-preview-' + index,
                                        style: { marginTop: '10px', marginBottom: '10px' }
                                    }, [
                                        createElement('img', {
                                            key: 'preview-img-' + index,
                                            src: category.imageUrl,
                                            style: { maxWidth: '100px', height: 'auto' }
                                        }),
                                        createElement(Button, {
                                            key: 'remove-image-' + index,
                                            isDestructive: true,
                                            onClick: function() {
                                                updateCategoryCard(index, 'imageUrl', '');
                                                updateCategoryCard(index, 'imageId', 0);
                                            },
                                            style: { marginLeft: '10px' }
                                        }, __('Remove image', 'my-e-shop'))
                                    ]),

                                    createElement('hr', {
                                        key: 'separator-' + index,
                                        style: { margin: '20px 0', border: '1px solid #ddd' }
                                    }),

                                    createElement('h5', {
                                        key: 'icon-title-' + index,
                                        style: { marginTop: '0', marginBottom: '10px', fontSize: '14px' }
                                    }, __('Category icon', 'my-e-shop')),
                                    
                                    createElement(MediaUploadCheck, {
                                        key: 'icon-media-upload-check-' + index
                                    }, createElement(MediaUpload, {
                                        key: 'icon-media-upload-' + index,
                                        onSelect: function(media) {
                                            if (media && media.url) {
                                                var newCategories = attributes.categories.map(function(category, i) {
                                                    if (i === index) {
                                                        return { 
                                                            ...category, 
                                                            iconUrl: media.url,
                                                            iconId: media.id
                                                        };
                                                    }
                                                    return category;
                                                });
                                                setAttributes({ categories: newCategories });
                                            }
                                        },
                                        allowedTypes: ['image'],
                                        value: category.iconId,
                                        render: function(obj) {
                                            return createElement('div', {
                                                key: 'icon-media-container-' + index
                                            }, [
                                                createElement(Button, {
                                                    key: 'select-icon-' + index,
                                                    onClick: obj.open,
                                                    isSecondary: true,
                                                    style: { marginBottom: '10px', marginRight: '10px' }
                                                }, category.iconUrl ? __('Change icon', 'my-e-shop') : __('Select icon', 'my-e-shop'))
                                            ]);
                                        }
                                    })),
                                    
                                    category.iconUrl && createElement('div', {
                                        key: 'icon-preview-' + index,
                                        style: { marginTop: '10px', marginBottom: '10px' }
                                    }, [
                                        createElement('img', {
                                            key: 'preview-icon-' + index,
                                            src: category.iconUrl,
                                            style: { maxWidth: '50px', height: 'auto' }
                                        }),
                                        createElement(Button, {
                                            key: 'remove-icon-' + index,
                                            isDestructive: true,
                                            onClick: function() {
                                                updateCategoryCard(index, 'iconUrl', '');
                                                updateCategoryCard(index, 'iconId', 0);
                                            },
                                            style: { marginLeft: '10px' }
                                        }, __('Remove icon', 'my-e-shop'))
                                    ]),
                                    
                                    createElement(TextControl, {
                                        key: 'category-title-input-' + index,
                                        label: __('Category name', 'my-e-shop'),
                                        value: category.title,
                                        onChange: function(value) {
                                            updateCategoryCard(index, 'title', value);
                                        }
                                    }),
                                    
                                    createElement(TextControl, {
                                        key: 'category-subtitle-input-' + index,
                                        label: __('Subtitle', 'my-e-shop'),
                                        value: category.subtitle,
                                        onChange: function(value) {
                                            updateCategoryCard(index, 'subtitle', value);
                                        }
                                    }),
                                    
                                    createElement(TextControl, {
                                        key: 'category-url-input-' + index,
                                        label: __('Link', 'my-e-shop'),
                                        value: category.url,
                                        onChange: function(value) {
                                            updateCategoryCard(index, 'url', value);
                                        }
                                    }),

                                    createElement('div', {
                                        key: 'title-color-wrapper-' + index,
                                        style: { marginTop: '15px', marginBottom: '15px' }
                                    }, [
                                        createElement('label', {
                                            key: 'title-color-label-' + index,
                                            style: { 
                                                display: 'block', 
                                                marginBottom: '8px', 
                                                fontWeight: '600',
                                                fontSize: '13px'
                                            }
                                        }, __('Title color', 'my-e-shop')),
                                        createElement(ColorPicker, {
                                            key: 'title-color-picker-' + index,
                                            color: category.titleColor || '#ffffff',
                                            onChangeComplete: function(color) {
                                                updateCategoryCard(index, 'titleColor', color.hex);
                                            }
                                        })
                                    ]),
                                    
                                    createElement(Button, {
                                        key: 'remove-category-' + index,
                                        isDestructive: true,
                                        onClick: function() {
                                            removeCategoryCard(index);
                                        },
                                        style: { marginTop: '10px' }
                                    }, __('Remove category', 'my-e-shop'))
                                ]);
                            })
                        ))
                    ]),
                    
                    createElement('div', blockProps, [
                        createElement('div', {
                            key: 'category-cards-container',
                            className: 'category-cards-container',
                            style: {
                                backgroundColor: attributes.backgroundColor,
                                padding: '40px 20px',
                                textAlign: 'center'
                            }
                        }, [
                            createElement('h2', {
                                key: 'block-title',
                                className: 'block-title',
                                style: {
                                    fontSize: '32px',
                                    fontWeight: 'bold',
                                    marginBottom: '8px',
                                    color: '#333'
                                }
                            }, attributes.blockTitle),
                            
                            createElement('p', {
                                key: 'block-subtitle',
                                className: 'block-subtitle',
                                style: {
                                    fontSize: '16px',
                                    color: '#666',
                                    marginBottom: '40px'
                                }
                            }, attributes.blockSubtitle),
                            
                            createElement('div', {
                                key: 'categories-grid',
                                className: 'categories-grid',
                                style: {
                                    display: 'grid',
                                    gridTemplateColumns: 'repeat(4, 1fr)',
                                    gap: '15px',
                                    maxWidth: '1200px',
                                    margin: '0 auto'
                                }
                            }, attributes.categories.map(function(category, index) {
                                return createElement('div', {
                                    key: 'category-card-' + index,
                                    className: 'category-card',
                                    style: {
                                        position: 'relative',
                                        overflow: 'hidden',
                                        borderRadius: '8px',
                                        aspectRatio: '1',
                                        background: category.imageUrl ? 'url(' + category.imageUrl + ') center/cover' : '#f0f0f0',
                                        display: 'flex',
                                        flexDirection: 'column',
                                        justifyContent: 'flex-end',
                                        padding: '20px',
                                        color: 'white',
                                        textAlign: 'left',
                                        minHeight: '200px'
                                    }
                                }, [
                                    createElement('div', {
                                        key: 'overlay-' + index,
                                        style: {
                                            position: 'absolute',
                                            top: 0,
                                            left: 0,
                                            right: 0,
                                            bottom: 0,
                                            background: 'linear-gradient(to top, rgba(0,0,0,0.7), rgba(0,0,0,0.2))',
                                            zIndex: 1
                                        }
                                    }),
                                    
                                    createElement('div', {
                                        key: 'content-' + index,
                                        style: {
                                            position: 'relative',
                                            zIndex: 2
                                        }
                                    }, [
                                        category.subtitle && createElement('p', {
                                            key: 'subtitle-' + index,
                                            style: {
                                                fontSize: '12px',
                                                marginBottom: '5px',
                                                opacity: 0.8
                                            }
                                        }, category.subtitle),
                                        
                                        createElement('h3', {
                                            key: 'title-' + index,
                                            style: {
                                                fontSize: '18px',
                                                fontWeight: 'bold',
                                                margin: 0,
                                                display: 'flex',
                                                alignItems: 'center',
                                                gap: '8px',
                                                color: category.titleColor || '#ffffff'
                                            }
                                        }, [
                                            category.iconUrl && createElement('img', {
                                                key: 'icon-' + index,
                                                src: category.iconUrl,
                                                style: {
                                                    width: '20px',
                                                    height: '20px',
                                                    objectFit: 'contain'
                                                }
                                            }),
                                            category.title
                                        ])
                                    ])
                                ]);
                            }))
                        ])
                    ])
                ]);
            }
        });
    } else {
        // Fallback for older versions
        console.warn('WordPress Block Editor components not available');
    }
})();
