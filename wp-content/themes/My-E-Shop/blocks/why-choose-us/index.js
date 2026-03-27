(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { 
        InspectorControls,
        MediaUpload,
        MediaUploadCheck
    } = wp.blockEditor;
    const { 
        PanelBody, 
        TextControl, 
        TextareaControl,
        Button, 
        RangeControl,
        SelectControl
    } = wp.components;
    const { __ } = wp.i18n;

    registerBlockType('my-e-shop/why-choose-us', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                title,
                items,
                iconSize
            } = attributes;

            const updateItem = function(index, key, value) {
                const newItems = [...items];
                newItems[index] = { ...newItems[index], [key]: value };
                setAttributes({ items: newItems });
            };

            const addItem = function() {
                const newItem = {
                    id: Date.now(),
                    question: 'Feature Title',
                    answer: 'Feature description goes here...',
                    icon: '',
                    iconImage: '',
                    iconImageId: 0,
                    iconType: 'image',
                    isOpen: false
                };
                setAttributes({ items: [...items, newItem] });
            };

            const removeItem = function(index) {
                const newItems = items.filter(function(item, i) {
                    return i !== index;
                });
                setAttributes({ items: newItems });
            };

            const toggleItem = function(index) {
                updateItem(index, 'isOpen', !items[index].isOpen);
            };

            return [
                el(InspectorControls, { key: 'inspector' },
                    el(PanelBody, { title: __('Settings', 'my-e-shop'), initialOpen: true },
                        el(TextControl, {
                            label: __('Section Title', 'my-e-shop'),
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            }
                        }),
                        el(RangeControl, {
                            label: __('Icon Size', 'my-e-shop'),
                            value: iconSize,
                            onChange: function(value) {
                                setAttributes({ iconSize: value });
                            },
                            min: 20,
                            max: 100,
                            step: 5
                        })
                    )
                ),
                
                el('div', { 
                    key: 'content',
                    className: 'why-choose-us editor-preview',
                    style: {
                        padding: '40px 20px',
                        backgroundColor: '#2a2a2a',
                        color: '#ffffff'
                    }
                },
                    el('div', { className: 'container' },
                        el('h2', {
                            style: {
                                textAlign: 'center',
                                marginBottom: '40px',
                                fontSize: '2.5rem',
                                fontWeight: '300',
                                color: '#F4F0EB'
                            }
                        }, 
                            el(TextControl, {
                                value: title,
                                onChange: function(value) {
                                    setAttributes({ title: value });
                                },
                                placeholder: 'Why choose us',
                                style: {
                                    textAlign: 'center',
                                    fontSize: '2.5rem',
                                    fontWeight: '300',
                                    color: '#F4F0EB',
                                    backgroundColor: 'transparent',
                                    border: 'none'
                                }
                            })
                        ),
                        
                        el('div', { 
                            className: 'row features-row',
                            style: { 
                                marginTop: '40px',
                                display: 'flex',
                                flexWrap: 'wrap',
                                gap: '20px'
                            }
                        },
                            items.map(function(item, index) {
                                return el('div', {
                                    key: 'item-' + index,
                                    style: {
                                        flex: '1 1 300px',
                                        minWidth: '300px'
                                    }
                                },
                                    el('div', {
                                        className: 'feature-item',
                                        style: {
                                            padding: '20px',
                                            border: '1px solid #444',
                                            borderRadius: '8px',
                                            height: '100%',
                                            textAlign: 'center',
                                            backgroundColor: '#333'
                                        }
                                    },
                                        el('div', {
                                            className: 'feature-icon',
                                            style: {
                                                marginBottom: '20px',
                                                display: 'flex',
                                                justifyContent: 'center',
                                                alignItems: 'center',
                                                fontSize: iconSize + 'px',
                                                color: '#fff'
                                            }
                                        }, 
                                            item.iconType === 'image' && item.iconImage ? 
                                                el('img', {
                                                    src: item.iconImage,
                                                    alt: item.question,
                                                    style: {
                                                        width: iconSize + 'px',
                                                        height: iconSize + 'px',
                                                        objectFit: 'cover',
                                                        borderRadius: '4px'
                                                    }
                                                }) : 
                                                el('span', {}, item.icon || '🔧')
                                        ),
                                        el('h3', {
                                            style: {
                                                marginBottom: '15px',
                                                fontSize: '18px',
                                                fontWeight: 'bold',
                                                color: '#fff'
                                            }
                                        }, item.question),
                                        el('p', {
                                            style: {
                                                fontSize: '14px',
                                                lineHeight: '1.5',
                                                marginBottom: '15px',
                                                color: '#ccc'
                                            }
                                        }, item.answer),
                                        el('div', {
                                            style: {
                                                display: 'flex',
                                                justifyContent: 'center',
                                                gap: '10px'
                                            }
                                        },
                                            el(Button, {
                                                onClick: function() {
                                                    toggleItem(index);
                                                },
                                                variant: 'secondary',
                                                size: 'small'
                                            }, item.isOpen ? 'Hide Settings' : 'Edit'),
                                            el(Button, {
                                                onClick: function() {
                                                    removeItem(index);
                                                },
                                                variant: 'secondary',
                                                size: 'small',
                                                style: { color: '#d63638' }
                                            }, '×')
                                        ),
                                        
                                        item.isOpen && el('div', {
                                            style: {
                                                marginTop: '20px',
                                                padding: '15px',
                                                backgroundColor: '#444',
                                                borderRadius: '6px',
                                                textAlign: 'left'
                                            }
                                        },
                                            el(TextControl, {
                                                label: 'Title',
                                                value: item.question,
                                                onChange: function(value) {
                                                    updateItem(index, 'question', value);
                                                }
                                            }),
                                            el(TextareaControl, {
                                                label: 'Description',
                                                value: item.answer,
                                                onChange: function(value) {
                                                    updateItem(index, 'answer', value);
                                                },
                                                rows: 3
                                            }),
                                            el(SelectControl, {
                                                label: 'Icon Type',
                                                value: item.iconType || 'image',
                                                onChange: function(value) {
                                                    updateItem(index, 'iconType', value);
                                                },
                                                options: [
                                                    { label: 'Image', value: 'image' },
                                                    { label: 'Text/Emoji', value: 'text' }
                                                ]
                                            }),
                                            
                                            item.iconType === 'image' ? 
                                                el(MediaUploadCheck, {},
                                                    el(MediaUpload, {
                                                        onSelect: function(media) {
                                                            updateItem(index, 'iconImageId', media.id);
                                                            updateItem(index, 'iconImage', media.url);
                                                        },
                                                        allowedTypes: ['image'],
                                                        value: item.iconImageId,
                                                        render: function(obj) {
                                                            return el(Button, {
                                                                onClick: obj.open,
                                                                variant: 'secondary'
                                                            }, item.iconImage ? 'Change Image' : 'Select Image');
                                                        }
                                                    })
                                                ) :
                                                el(TextControl, {
                                                    label: 'Icon Text/Emoji',
                                                    value: item.icon,
                                                    onChange: function(value) {
                                                        updateItem(index, 'icon', value);
                                                    },
                                                    placeholder: '🔧'
                                                })
                                        )
                                    )
                                );
                            }),
                            
                            el('div', {
                                style: { 
                                    textAlign: 'center',
                                    width: '100%',
                                    marginTop: '20px'
                                }
                            },
                                el(Button, {
                                    onClick: addItem,
                                    className: 'button button-primary'
                                }, __('+ Add Feature', 'my-e-shop'))
                            )
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