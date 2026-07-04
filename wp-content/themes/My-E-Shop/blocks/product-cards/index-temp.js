// Product Cards Block - Version 2.0 - Simple cards without WooCommerce
(function() {
    'use strict';

    var __ = wp.i18n.__;
    var registerBlockType = wp.blocks.registerBlockType;
    var createElement = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var RichText = wp.blockEditor.RichText;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var RangeControl = wp.components.RangeControl;
    var SelectControl = wp.components.SelectControl;
    var Button = wp.components.Button;

    registerBlockType('my-e-shop/product-cards', {
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var title = attributes.title || 'Best Sellers';
            var subtitle = attributes.subtitle || 'Your aura in cotton';
            var columns = attributes.columns || 4;
            var cardStyle = attributes.cardStyle || 'classic';
            var customImages = attributes.customImages || [];

            // Function to add an image
            var addImage = function() {
                var newImages = customImages.slice();
                newImages.push({
                    id: Date.now(),
                    url: '',
                    alt: '',
                    title: 'New product ' + (customImages.length + 1),
                    price: '$0.00',
                    link: '#'
                });
                setAttributes({ customImages: newImages });
            };

            // Function to add a card from a template
            var addImageWithTemplate = function(template) {
                var templates = {
                    clothing: { title: 'Stylish clothing', price: '$29.99', alt: 'Fashionable clothing' },
                    accessories: { title: 'Accessory', price: '$19.99', alt: 'Stylish accessory' },
                    shoes: { title: 'Shoes', price: '$79.99', alt: 'Comfortable shoes' },
                    bags: { title: 'Bag', price: '$49.99', alt: 'Stylish bag' }
                };
                
                var selectedTemplate = templates[template] || templates.clothing;
                var newImages = customImages.slice();
                newImages.push({
                    id: Date.now(),
                    url: '',
                    alt: selectedTemplate.alt,
                    title: selectedTemplate.title,
                    price: selectedTemplate.price,
                    link: '#'
                });
                setAttributes({ customImages: newImages });
            };

            // Function to update an image
            var updateImage = function(index, field, value) {
                var newImages = customImages.slice();
                newImages[index] = Object.assign({}, newImages[index]);
                newImages[index][field] = value;
                setAttributes({ customImages: newImages });
            };

            // Function to remove an image
            var removeImage = function(index) {
                var newImages = customImages.filter(function(_, i) { return i !== index; });
                setAttributes({ customImages: newImages });
            };

            // Function to duplicate a card
            var duplicateImage = function(index) {
                var imageToDuplicate = Object.assign({}, customImages[index]);
                imageToDuplicate.id = Date.now();
                imageToDuplicate.title = imageToDuplicate.title + ' (copy)';
                var newImages = customImages.slice();
                newImages.splice(index + 1, 0, imageToDuplicate);
                setAttributes({ customImages: newImages });
            };

            return createElement(Fragment, {},
                createElement(InspectorControls, {},
                    createElement(PanelBody, { title: 'Block Settings', initialOpen: true },
                        createElement(TextControl, {
                            label: 'Title',
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); }
                        }),
                        createElement(TextControl, {
                            label: 'Subtitle',
                            value: subtitle,
                            onChange: function(value) { setAttributes({ subtitle: value }); }
                        }),
                        createElement(RangeControl, {
                            label: 'Number of columns',
                            value: columns,
                            onChange: function(value) { setAttributes({ columns: value }); },
                            min: 1,
                            max: 6
                        }),
                        createElement(SelectControl, {
                            label: 'Card style',
                            value: cardStyle,
                            onChange: function(value) { setAttributes({ cardStyle: value }); },
                            options: [
                                { label: 'Classic', value: 'classic' },
                                { label: 'Minimal', value: 'minimal' },
                                { label: 'Modern', value: 'modern' },
                                { label: 'Elegant', value: 'elegant' }
                            ]
                        })
                    ),
                    createElement(PanelBody, { title: 'Manage product cards', initialOpen: true },
                        createElement('div', { 
                            style: { 
                                textAlign: 'center', 
                                marginBottom: '20px',
                                padding: '15px',
                                border: '2px dashed #007cba',
                                borderRadius: '8px',
                                backgroundColor: '#f8f9fa'
                            }
                        },
                            createElement(Button, {
                                isPrimary: true,
                                onClick: addImage,
                                style: { 
                                    fontSize: '14px',
                                    fontWeight: 'bold',
                                    padding: '10px 20px',
                                    marginBottom: '10px',
                                    width: '100%'
                                }
                            }, '➕ Add empty card'),
                            createElement('div', { 
                                style: { 
                                    marginTop: '10px',
                                    marginBottom: '10px',
                                    fontSize: '12px',
                                    color: '#666'
                                }
                            }, 'or choose a template:'),
                            createElement('div', { 
                                style: { 
                                    display: 'grid',
                                    gridTemplateColumns: '1fr 1fr',
                                    gap: '5px',
                                    marginBottom: '10px'
                                }
                            },
                                createElement(Button, {
                                    isSecondary: true,
                                    onClick: function() { addImageWithTemplate('clothing'); },
                                    isSmall: true,
                                    style: { fontSize: '11px' }
                                }, '👕 Clothing'),
                                createElement(Button, {
                                    isSecondary: true,
                                    onClick: function() { addImageWithTemplate('accessories'); },
                                    isSmall: true,
                                    style: { fontSize: '11px' }
                                }, '💍 Accessories'),
                                createElement(Button, {
                                    isSecondary: true,
                                    onClick: function() { addImageWithTemplate('shoes'); },
                                    isSmall: true,
                                    style: { fontSize: '11px' }
                                }, '👟 Shoes'),
                                createElement(Button, {
                                    isSecondary: true,
                                    onClick: function() { addImageWithTemplate('bags'); },
                                    isSmall: true,
                                    style: { fontSize: '11px' }
                                }, '👜 Bags')
                            ),
                            createElement('p', { 
                                style: { 
                                    margin: '8px 0 0 0', 
                                    fontSize: '12px', 
                                    color: '#666',
                                    fontStyle: 'italic'
                                }
                            }, 'Total cards: ' + customImages.length)
                        ),
                        customImages.map(function(image, index) {
                            return createElement('div', { 
                                key: image.id || index,
                                style: { 
                                    marginBottom: '25px', 
                                    padding: '20px', 
                                    border: '1px solid #e0e0e0',
                                    borderRadius: '8px',
                                    backgroundColor: '#ffffff',
                                    boxShadow: '0 2px 4px rgba(0,0,0,0.1)'
                                }
                            },
                                createElement('div', {
                                    style: {
                                        display: 'flex',
                                        justifyContent: 'space-between',
                                        alignItems: 'center',
                                        marginBottom: '15px',
                                        paddingBottom: '10px',
                                        borderBottom: '1px solid #f0f0f0'
                                    }
                                },
                                    createElement('h4', { style: { margin: '0', color: '#1e1e1e' } }, '📦 Card ' + (index + 1)),
                                    createElement('div', { style: { display: 'flex', gap: '5px' } },
                                        createElement(Button, {
                                            isSecondary: true,
                                            onClick: function() { duplicateImage(index); },
                                            isSmall: true,
                                            style: { fontSize: '12px' },
                                            title: 'Duplicate card'
                                        }, '📋 Copy'),
                                        createElement(Button, {
                                            isDestructive: true,
                                            onClick: function() { removeImage(index); },
                                            isSmall: true,
                                            style: { fontSize: '12px' },
                                            title: 'Remove card'
                                        }, '🗑️ Remove')
                                    )
                                ),
                                createElement('div', {
                                    style: {
                                        display: 'grid',
                                        gridTemplateColumns: '1fr 1fr',
                                        gap: '10px',
                                        marginBottom: '15px'
                                    }
                                },
                                    createElement(TextControl, {
                                        label: '📝 Product name',
                                        value: image.title,
                                        onChange: function(value) { updateImage(index, 'title', value); },
                                        placeholder: 'Enter product name'
                                    }),
                                    createElement(TextControl, {
                                        label: '💰 Price',
                                        value: image.price,
                                        onChange: function(value) { updateImage(index, 'price', value); },
                                        placeholder: '$29.99'
                                    })
                                ),
                                createElement('div', { style: { marginBottom: '15px' } },
                                    createElement('label', { 
                                        style: { 
                                            display: 'block', 
                                            marginBottom: '8px',
                                            fontWeight: '600',
                                            fontSize: '13px'
                                        }
                                    }, '🖼️ Product image'),
                                    createElement(MediaUploadCheck, {},
                                        createElement(MediaUpload, {
                                            onSelect: function(media) {
                                                updateImage(index, 'url', media.url);
                                                updateImage(index, 'id', media.id);
                                                if (!image.alt) {
                                                    updateImage(index, 'alt', media.alt || image.title);
                                                }
                                            },
                                            allowedTypes: ['image'],
                                            value: image.id,
                                            render: function(obj) {
                                                return createElement('div', {
                                                    style: {
                                                        border: '2px dashed #ccc',
                                                        borderRadius: '4px',
                                                        padding: '15px',
                                                        textAlign: 'center',
                                                        backgroundColor: image.url ? 'transparent' : '#fafafa'
                                                    }
                                                },
                                                    image.url ? 
                                                        createElement('div', {},
                                                            createElement('img', {
                                                                src: image.url,
                                                                alt: image.alt,
                                                                style: { 
                                                                    width: '100%', 
                                                                    maxWidth: '200px',
                                                                    height: 'auto',
                                                                    borderRadius: '4px',
                                                                    marginBottom: '10px'
                                                                }
                                                            }),
                                                            createElement('div', {},
                                                                createElement(Button, {
                                                                    isSecondary: true,
                                                                    onClick: obj.open,
                                                                    style: { marginRight: '10px' },
                                                                    isSmall: true
                                                                }, '🔄 Replace'),
                                                                createElement(Button, {
                                                                    isDestructive: true,
                                                                    onClick: function() {
                                                                        updateImage(index, 'url', '');
                                                                        updateImage(index, 'id', '');
                                                                    },
                                                                    isSmall: true
                                                                }, '❌ Remove')
                                                            )
                                                        ) :
                                                        createElement('div', {},
                                                            createElement('div', { 
                                                                style: { 
                                                                    fontSize: '48px', 
                                                                    color: '#ccc',
                                                                    marginBottom: '10px'
                                                                }
                                                            }, '📷'),
                                                            createElement(Button, {
                                                                isPrimary: true,
                                                                onClick: obj.open
                                                            }, 'Choose image')
                                                        )
                                                );
                                            }
                                        })
                                    )
                                ),
                                createElement('div', {
                                    style: {
                                        display: 'grid',
                                        gridTemplateColumns: '1fr 1fr',
                                        gap: '10px'
                                    }
                                },
                                    createElement(TextControl, {
                                        label: '🔗 Product link',
                                        value: image.link,
                                        onChange: function(value) { updateImage(index, 'link', value); },
                                        placeholder: 'https://example.com/product'
                                    }),
                                    createElement(TextControl, {
                                        label: '🏷️ Alt text',
                                        value: image.alt,
                                        onChange: function(value) { updateImage(index, 'alt', value); },
                                        placeholder: 'Image description'
                                    })
                                )
                            );
                        })
                    )
                ),
                createElement('div', { 
                    className: 'product-cards-block card-style-' + cardStyle + ' columns-' + columns
                },
                    createElement('div', { className: 'product-cards-header' },
                        createElement(RichText, {
                            tagName: 'h2',
                            className: 'product-cards-title',
                            value: title,
                            onChange: function(value) { setAttributes({ title: value }); },
                            placeholder: 'Enter a title...'
                        }),
                        createElement(RichText, {
                            tagName: 'p',
                            className: 'product-cards-subtitle',
                            value: subtitle,
                            onChange: function(value) { setAttributes({ subtitle: value }); },
                            placeholder: 'Enter a subtitle...'
                        })
                    ),
                    createElement('div', { className: 'product-cards-section' },
                        createElement('div', { className: 'products-grid' },
                            customImages.length > 0 ?
                                customImages.map(function(image, index) {
                                    return createElement('div', { 
                                        key: image.id || index,
                                        className: 'product-card'
                                    },
                                        createElement('div', { className: 'product-image' },
                                            image.url ?
                                                createElement('img', {
                                                    src: image.url,
                                                    alt: image.alt || image.title,
                                                    style: { width: '100%', height: 'auto' }
                                                }) :
                                                createElement('div', { 
                                                    style: { 
                                                        backgroundColor: '#f0f0f0', 
                                                        padding: '40px', 
                                                        textAlign: 'center',
                                                        color: '#666'
                                                    }
                                                }, 'No image')
                                        ),
                                        createElement('div', { className: 'product-card-info' },
                                            createElement('h3', { className: 'product-card-title' },
                                                createElement('a', { href: image.link }, image.title)
                                            ),
                                            createElement('div', { className: 'product-card-price' }, image.price),
                                            createElement('div', { className: 'product-card-actions' },
                                                createElement('a', { 
                                                    href: image.link,
                                                    className: 'add-to-cart-btn'
                                                }, 'Learn more')
                                            )
                                        )
                                    );
                                }) :
                                createElement('div', { 
                                    style: { 
                                        gridColumn: '1 / -1', 
                                        textAlign: 'center', 
                                        padding: '40px',
                                        color: '#666'
                                    }
                                }, 'Add product cards in the block settings')
                        )
                    )
                )
            );
        },

        save: function() {
            // Use server-side rendering
            return null;
        }
    });

})();
