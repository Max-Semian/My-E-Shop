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
        SelectControl,
        ToggleControl
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
                    question: 'New Question',
                    answer: 'New Answer',
                    icon: '❓',
                    iconImage: '',
                    iconImageId: 0,
                    iconType: 'text',
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
                const newItems = [...items];
                newItems[index].isOpen = !newItems[index].isOpen;
                setAttributes({ items: newItems });
            };

            return [
                el(InspectorControls, null,
                    el(PanelBody, { title: __('Основные настройки', 'my-e-shop'), initialOpen: true },
                        el(TextControl, {
                            label: __('Заголовок блока', 'my-e-shop'),
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            }
                        }),
                        el(RangeControl, {
                            label: __('Размер иконки (px)', 'my-e-shop'),
                            value: iconSize,
                            onChange: function(value) {
                                setAttributes({ iconSize: value });
                            },
                            min: 16,
                            max: 48
                        })
                    )
                ),
                el('div', { 
                    className: 'why-choose-us-block-editor',
                    style: {
                        padding: '40px 20px',
                        minHeight: '300px'
                    }
                },
                    el('div', { className: 'container' },
                        el('h2', { 
                            className: 'section-title',
                            style: { 
                                textAlign: 'center',
                                marginBottom: '40px',
                                fontSize: '2.5rem',
                                fontWeight: '300'
                            }
                        }, title),
                        
                        el('div', { className: 'accordion-container' },
                            items.map(function(item, index) {
                                return el('div', {
                                    key: 'item-' + index,
                                    className: 'accordion-item-editor',
                                    style: {
                                        marginBottom: '15px',
                                        borderRadius: '8px',
                                        overflow: 'hidden'
                                    }
                                },
                                    el('div', {
                                        className: 'accordion-header-editor',
                                        style: {
                                            padding: '15px 20px',
                                            cursor: 'pointer',
                                            display: 'flex',
                                            alignItems: 'center',
                                            gap: '15px'
                                        },
                                        onClick: function() { toggleItem(index); }
                                    },
                                        el('span', {
                                            style: {
                                                fontSize: iconSize + 'px',
                                                minWidth: iconSize + 'px',
                                                display: 'flex',
                                                alignItems: 'center',
                                                justifyContent: 'center'
                                            }
                                        }, item.iconType === 'image' && item.iconImage ? 
                                            el('img', {
                                                src: item.iconImage,
                                                alt: '',
                                                style: {
                                                    width: iconSize + 'px',
                                                    height: iconSize + 'px',
                                                    objectFit: 'cover',
                                                    borderRadius: '4px'
                                                }
                                            }) : item.icon
                                        ),
                                        el(TextControl, {
                                            value: item.question,
                                            onChange: function(value) {
                                                updateItem(index, 'question', value);
                                            },
                                            style: {
                                                flex: 1,
                                                border: 'none',
                                                background: 'transparent'
                                            },
                                            onClick: function(e) {
                                                e.stopPropagation();
                                            }
                                        }),
                                        el('span', {
                                            style: {
                                                fontSize: '18px',
                                                transform: item.isOpen ? 'rotate(45deg)' : 'rotate(0deg)',
                                                transition: 'transform 0.3s ease'
                                            }
                                        }, '+'),
                                        el(Button, {
                                            onClick: function(e) {
                                                e.stopPropagation();
                                                removeItem(index);
                                            },
                                            className: 'button-link-delete',
                                            style: {
                                                color: '#d63638',
                                                marginLeft: '10px'
                                            }
                                        }, '×')
                                    ),
                                    
                                    item.isOpen && el('div', {
                                        className: 'accordion-content-editor',
                                        style: {
                                            padding: '20px'
                                        }
                                    },
                                        el('div', {
                                            style: {
                                                marginBottom: '15px',
                                                fontSize: '12px',
                                                color: '#888',
                                                fontWeight: 'bold'
                                            }
                                        }, __('Тип иконки:', 'my-e-shop')),
                                        el(SelectControl, {
                                            value: item.iconType || 'text',
                                            onChange: function(value) {
                                                updateItem(index, 'iconType', value);
                                            },
                                            options: [
                                                { label: __('Текст/Эмодзи', 'my-e-shop'), value: 'text' },
                                                { label: __('Изображение', 'my-e-shop'), value: 'image' }
                                            ],
                                            style: { marginBottom: '15px' }
                                        }),
                                        
                                        item.iconType === 'text' && el('div', null,
                                            el('div', {
                                                style: {
                                                    marginBottom: '10px',
                                                    fontSize: '12px',
                                                    color: '#888'
                                                }
                                            }, __('Текстовая иконка/Эмодзи:', 'my-e-shop')),
                                            el(TextControl, {
                                                value: item.icon,
                                                onChange: function(value) {
                                                    updateItem(index, 'icon', value);
                                                },
                                                placeholder: __('Введите эмодзи или символ', 'my-e-shop'),
                                                style: {
                                                    marginBottom: '15px',
                                                    maxWidth: '150px'
                                                }
                                            })
                                        ),
                                        
                                        item.iconType === 'image' && el('div', null,
                                            el('div', {
                                                style: {
                                                    marginBottom: '10px',
                                                    fontSize: '12px',
                                                    color: '#888'
                                                }
                                            }, __('Изображение иконки:', 'my-e-shop')),
                                            el(MediaUploadCheck, null,
                                                el(MediaUpload, {
                                                    onSelect: function(media) {
                                                        updateItem(index, 'iconImage', media.url);
                                                        updateItem(index, 'iconImageId', media.id);
                                                    },
                                                    allowedTypes: ['image'],
                                                    value: item.iconImageId,
                                                    render: function(obj) {
                                                        return el('div', {
                                                            style: { marginBottom: '15px' }
                                                        },
                                                            item.iconImage && el('div', {
                                                                style: {
                                                                    marginBottom: '10px',
                                                                    textAlign: 'center'
                                                                }
                                                            },
                                                                el('img', {
                                                                    src: item.iconImage,
                                                                    alt: '',
                                                                    style: {
                                                                        width: '60px',
                                                                        height: '60px',
                                                                        objectFit: 'cover',
                                                                        borderRadius: '8px',
                                                                        border: '2px solid #ddd'
                                                                    }
                                                                })
                                                            ),
                                                            el(Button, {
                                                                onClick: obj.open,
                                                                className: 'button button-primary',
                                                                style: { 
                                                                    marginRight: '10px',
                                                                    fontSize: '12px'
                                                                }
                                                            }, item.iconImage ? 
                                                                __('Изменить изображение', 'my-e-shop') : 
                                                                __('Выбрать изображение', 'my-e-shop')
                                                            ),
                                                            item.iconImage && el(Button, {
                                                                onClick: function() {
                                                                    updateItem(index, 'iconImage', '');
                                                                    updateItem(index, 'iconImageId', 0);
                                                                },
                                                                className: 'button',
                                                                style: { fontSize: '12px' }
                                                            }, __('Удалить', 'my-e-shop'))
                                                        );
                                                    }
                                                })
                                            )
                                        ),
                                        
                                        el('div', {
                                            style: {
                                                marginBottom: '10px',
                                                fontSize: '12px',
                                                color: '#888'
                                            }
                                        }, __('Ответ:', 'my-e-shop')),
                                        el(TextareaControl, {
                                            value: item.answer,
                                            onChange: function(value) {
                                                updateItem(index, 'answer', value);
                                            },
                                            style: {
                                                minHeight: '80px'
                                            }
                                        })
                                    )
                                );
                            }),
                            
                            el(Button, {
                                onClick: addItem,
                                className: 'button button-primary',
                                style: {
                                    marginTop: '20px',
                                    display: 'block',
                                    margin: '20px auto 0'
                                }
                            }, __('+ Добавить элемент', 'my-e-shop'))
                        )
                    )
                )
            ];
        },

        save: function() {
            return null; // Server-side rendering
        }
    });
})();
