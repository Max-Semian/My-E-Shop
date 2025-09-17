(function() {(function() {

    const { registerBlockType } = wp.blocks;    const { registerBlockType } = wp.blocks;

    const { createElement: el } = wp.element;    const { createElement: el } = wp.element;

    const {     const { 

        InspectorControls,        InspectorControls,

        MediaUpload,        MediaUpload,

        MediaUploadCheck        MediaUploadCheck

    } = wp.blockEditor;    } = wp.blockEditor;

    const {     const { 

        PanelBody,         PanelBody, 

        TextControl,         TextControl, 

        TextareaControl,        TextareaControl,

        Button,         Button, 

        RangeControl,        RangeControl,

        SelectControl        SelectControl,

    } = wp.components;        ToggleControl

    const { __ } = wp.i18n;    } = wp.components;

    const { __ } = wp.i18n;

    registerBlockType('my-e-shop/why-choose-us', {

        edit: function(props) {    registerBlockType('my-e-shop/why-choose-us', {

            const { attributes, setAttributes } = props;        edit: function(props) {

            const {             const { attributes, setAttributes } = props;

                title,            const { 

                items,                title,

                iconSize                items,

            } = attributes;                iconSize

            } = attributes;

            const updateItem = function(index, key, value) {

                const newItems = [...items];            const updateItem = function(index, key, value) {

                newItems[index] = { ...newItems[index], [key]: value };                const newItems = [...items];

                setAttributes({ items: newItems });                newItems[index] = { ...newItems[index], [key]: value };

            };                setAttributes({ items: newItems });

            };

            const addItem = function() {

                const newItem = {            const addItem = function() {

                    id: Date.now(),                const newItem = {

                    question: 'Feature Title',                    id: Date.now(),

                    answer: 'Feature description goes here...',                    question: 'Feature Title',

                    icon: '',                    answer: 'Feature description goes here...',

                    iconImage: '',                    icon: '',

                    iconImageId: 0,                    iconImage: '',

                    iconType: 'image',                    iconImageId: 0,

                    isOpen: false                    iconType: 'image',

                };                    isOpen: false

                setAttributes({ items: [...items, newItem] });                };

            };                setAttributes({ items: [...items, newItem] });

            };

            const removeItem = function(index) {

                const newItems = items.filter(function(item, i) {            const removeItem = function(index) {

                    return i !== index;                const newItems = items.filter(function(item, i) {

                });                    return i !== index;

                setAttributes({ items: newItems });                });

            };                setAttributes({ items: newItems });

            };

            const toggleItem = function(index) {

                updateItem(index, 'isOpen', !items[index].isOpen);            const toggleItem = function(index) {

            };                const newItems = [...items];

                newItems[index].isOpen = !newItems[index].isOpen;

            return [                setAttributes({ items: newItems });

                el(InspectorControls, { key: 'inspector' },            };

                    el(PanelBody, { title: __('Settings', 'my-e-shop'), initialOpen: true },

                        el(TextControl, {            return [

                            label: __('Section Title', 'my-e-shop'),                el(InspectorControls, null,

                            value: title,                    el(PanelBody, { title: __('Основные настройки', 'my-e-shop'), initialOpen: true },

                            onChange: function(value) {                        el(TextControl, {

                                setAttributes({ title: value });                            label: __('Заголовок блока', 'my-e-shop'),

                            }                            value: title,

                        }),                            onChange: function(value) {

                        el(RangeControl, {                                setAttributes({ title: value });

                            label: __('Icon Size', 'my-e-shop'),                            }

                            value: iconSize,                        }),

                            onChange: function(value) {                        el(RangeControl, {

                                setAttributes({ iconSize: value });                            label: __('Размер иконки (px)', 'my-e-shop'),

                            },                            value: iconSize,

                            min: 20,                            onChange: function(value) {

                            max: 100,                                setAttributes({ iconSize: value });

                            step: 5                            },

                        })                            min: 16,

                    )                            max: 48

                ),                        })

                                    )

                el('div', {                 ),

                    key: 'content',                el('div', { 

                    className: 'why-choose-us editor-preview',                    className: 'why-choose-us-block-editor',

                    style: {                    style: {

                        padding: '40px 20px',                        padding: '40px 20px',

                        backgroundColor: '#2a2a2a',                        minHeight: '300px'

                        color: '#ffffff'                    }

                    }                },

                },                    el('div', { className: 'container' },

                    el('div', { className: 'container' },                        el('h2', { 

                        el('h2', {                            className: 'section-title',

                            style: {                            style: { 

                                textAlign: 'center',                                textAlign: 'center',

                                marginBottom: '40px',                                marginBottom: '40px',

                                fontSize: '2.5rem',                                fontSize: '2.5rem',

                                fontWeight: '300',                                fontWeight: '300'

                                color: '#F4F0EB'                            }

                            }                        }, title),

                        },                         

                            el(TextControl, {                        el('div', { 

                                value: title,                            className: 'row features-row',

                                onChange: function(value) {                            style: { marginTop: '40px' }

                                    setAttributes({ title: value });                        },

                                },                            items.map(function(item, index) {

                                placeholder: 'Why choose us',                                return el('div', {

                                style: {                                    key: 'item-' + index,

                                    textAlign: 'center',                                    className: 'col-lg-4 col-md-6 mb-4'

                                    fontSize: '2.5rem',                                },

                                    fontWeight: '300',                                    el('div', {

                                    color: '#F4F0EB',                                        className: 'feature-item text-center',

                                    backgroundColor: 'transparent',                                        style: {

                                    border: 'none'                                            padding: '20px',

                                }                                            border: '1px solid #ddd',

                            })                                            borderRadius: '8px',

                        ),                                            height: '100%'

                                                                }

                        el('div', {                                     },

                            className: 'row features-row',                                        el('div', {

                            style: {                                             className: 'feature-icon',

                                marginTop: '40px',                                            style: {

                                display: 'flex',                                                marginBottom: '20px',

                                flexWrap: 'wrap',                                                display: 'flex',

                                gap: '20px'                                                justifyContent: 'center',

                            }                                                alignItems: 'center',

                        },                                                fontSize: iconSize + 'px'

                            items.map(function(item, index) {                                            }

                                return el('div', {                                        }, 

                                    key: 'item-' + index,                                            item.iconType === 'image' && item.iconImage ? 

                                    style: {                                                el('img', {

                                        flex: '1 1 300px',                                                    src: item.iconImage,

                                        minWidth: '300px'                                                    alt: item.question,

                                    }                                                    style: {

                                },                                                        width: iconSize + 'px',

                                    el('div', {                                                        height: iconSize + 'px',

                                        className: 'feature-item',                                                        objectFit: 'cover',

                                        style: {                                                        borderRadius: '4px'

                                            padding: '20px',                                                    }

                                            border: '1px solid #444',                                                }) : 

                                            borderRadius: '8px',                                                el('span', {}, item.icon || '🔧')

                                            height: '100%',                                        ),

                                            textAlign: 'center',                                        el('h3', {

                                            backgroundColor: '#333'                                            style: {

                                        }                                                marginBottom: '15px',

                                    },                                                fontSize: '18px',

                                        el('div', {                                                fontWeight: 'bold'

                                            className: 'feature-icon',                                            }

                                            style: {                                        }, item.question),

                                                marginBottom: '20px',                                        el('p', {

                                                display: 'flex',                                            style: {

                                                justifyContent: 'center',                                                fontSize: '14px',

                                                alignItems: 'center',                                                lineHeight: '1.5',

                                                fontSize: iconSize + 'px',                                                marginBottom: '15px'

                                                color: '#fff'                                            }

                                            }                                        }, item.answer),

                                        },                                         el('div', {

                                            item.iconType === 'image' && item.iconImage ?                                             style: {

                                                el('img', {                                                display: 'flex',

                                                    src: item.iconImage,                                                justifyContent: 'center',

                                                    alt: item.question,                                                gap: '10px'

                                                    style: {                                            }

                                                        width: iconSize + 'px',                                        },

                                                        height: iconSize + 'px',                                            el(Button, {

                                                        objectFit: 'cover',                                                onClick: function() {

                                                        borderRadius: '4px'                                                    toggleItem(index);

                                                    }                                                },

                                                }) :                                                 variant: 'secondary',

                                                el('span', {}, item.icon || '🔧')                                                size: 'small'

                                        ),                                            }, item.isOpen ? 'Hide Settings' : 'Edit'),

                                        el('h3', {                                            el(Button, {

                                            style: {                                                onClick: function() {

                                                marginBottom: '15px',                                                    removeItem(index);

                                                fontSize: '18px',                                                },

                                                fontWeight: 'bold',                                                variant: 'secondary',

                                                color: '#fff'                                                size: 'small',

                                            }                                                style: { color: '#d63638' }

                                        }, item.question),                                            }, '×')

                                        el('p', {                                        ),

                                            style: {                                        

                                                fontSize: '14px',                                        item.isOpen && el('div', {

                                                lineHeight: '1.5',                                            style: {

                                                marginBottom: '15px',                                                marginTop: '20px',

                                                color: '#ccc'                                                padding: '15px',

                                            }                                                backgroundColor: '#f9f9f9',

                                        }, item.answer),                                                borderRadius: '6px',

                                        el('div', {                                                textAlign: 'left'

                                            style: {                                            }

                                                display: 'flex',                                        },

                                                justifyContent: 'center',                                            el(TextControl, {

                                                gap: '10px'                                                label: 'Title',

                                            }                                                value: item.question,

                                        },                                                onChange: function(value) {

                                            el(Button, {                                                    updateItem(index, 'question', value);

                                                onClick: function() {                                                }

                                                    toggleItem(index);                                            }),

                                                },                                            el(TextareaControl, {

                                                variant: 'secondary',                                                label: 'Description',

                                                size: 'small'                                                value: item.answer,

                                            }, item.isOpen ? 'Hide Settings' : 'Edit'),                                                onChange: function(value) {

                                            el(Button, {                                                    updateItem(index, 'answer', value);

                                                onClick: function() {                                                },

                                                    removeItem(index);                                                rows: 3

                                                },                                            }),

                                                variant: 'secondary',                                            el(SelectControl, {

                                                size: 'small',                                                label: 'Icon Type',

                                                style: { color: '#d63638' }                                                value: item.iconType || 'image',

                                            }, '×')                                                onChange: function(value) {

                                        ),                                                    updateItem(index, 'iconType', value);

                                                                                        },

                                        item.isOpen && el('div', {                                                options: [

                                            style: {                                                    { label: 'Image', value: 'image' },

                                                marginTop: '20px',                                                    { label: 'Text/Emoji', value: 'text' }

                                                padding: '15px',                                                ]

                                                backgroundColor: '#444',                                            }),

                                                borderRadius: '6px',                                            

                                                textAlign: 'left'                                            item.iconType === 'image' ? 

                                            }                                                el(MediaUploadCheck, {},

                                        },                                                    el(MediaUpload, {

                                            el(TextControl, {                                                        onSelect: function(media) {

                                                label: 'Title',                                                            updateItem(index, 'iconImageId', media.id);

                                                value: item.question,                                                            updateItem(index, 'iconImage', media.url);

                                                onChange: function(value) {                                                        },

                                                    updateItem(index, 'question', value);                                                        allowedTypes: ['image'],

                                                }                                                        value: item.iconImageId,

                                            }),                                                        render: function(obj) {

                                            el(TextareaControl, {                                                            return el(Button, {

                                                label: 'Description',                                                                onClick: obj.open,

                                                value: item.answer,                                                                variant: 'secondary'

                                                onChange: function(value) {                                                            }, item.iconImage ? 'Change Image' : 'Select Image');

                                                    updateItem(index, 'answer', value);                                                        }

                                                },                                                    })

                                                rows: 3                                                ) :

                                            }),                                                el(TextControl, {

                                            el(SelectControl, {                                                    label: 'Icon Text/Emoji',

                                                label: 'Icon Type',                                                    value: item.icon,

                                                value: item.iconType || 'image',                                                    onChange: function(value) {

                                                onChange: function(value) {                                                        updateItem(index, 'icon', value);

                                                    updateItem(index, 'iconType', value);                                                    },

                                                },                                                    placeholder: '🔧'

                                                options: [                                                })

                                                    { label: 'Image', value: 'image' },                                        )

                                                    { label: 'Text/Emoji', value: 'text' }                                    )

                                                ]                                );

                                            }),                            }),

                                                                            return el('div', {

                                            item.iconType === 'image' ?                                     key: 'item-' + index,

                                                el(MediaUploadCheck, {},                                    className: 'accordion-item-editor',

                                                    el(MediaUpload, {                                    style: {

                                                        onSelect: function(media) {                                        marginBottom: '15px',

                                                            updateItem(index, 'iconImageId', media.id);                                        borderRadius: '8px',

                                                            updateItem(index, 'iconImage', media.url);                                        overflow: 'hidden'

                                                        },                                    }

                                                        allowedTypes: ['image'],                                },

                                                        value: item.iconImageId,                                    el('div', {

                                                        render: function(obj) {                                        className: 'accordion-header-editor',

                                                            return el(Button, {                                        style: {

                                                                onClick: obj.open,                                            padding: '15px 20px',

                                                                variant: 'secondary'                                            cursor: 'pointer',

                                                            }, item.iconImage ? 'Change Image' : 'Select Image');                                            display: 'flex',

                                                        }                                            alignItems: 'center',

                                                    })                                            gap: '15px'

                                                ) :                                        },

                                                el(TextControl, {                                        onClick: function() { toggleItem(index); }

                                                    label: 'Icon Text/Emoji',                                    },

                                                    value: item.icon,                                        el('span', {

                                                    onChange: function(value) {                                            style: {

                                                        updateItem(index, 'icon', value);                                                fontSize: iconSize + 'px',

                                                    },                                                minWidth: iconSize + 'px',

                                                    placeholder: '🔧'                                                display: 'flex',

                                                })                                                alignItems: 'center',

                                        )                                                justifyContent: 'center'

                                    )                                            }

                                );                                        }, item.iconType === 'image' && item.iconImage ? 

                            }),                                            el('img', {

                                                                            src: item.iconImage,

                            el('div', {                                                alt: '',

                                style: {                                                 style: {

                                    textAlign: 'center',                                                    width: iconSize + 'px',

                                    width: '100%',                                                    height: iconSize + 'px',

                                    marginTop: '20px'                                                    objectFit: 'cover',

                                }                                                    borderRadius: '4px'

                            },                                                }

                                el(Button, {                                            }) : item.icon

                                    onClick: addItem,                                        ),

                                    className: 'button button-primary'                                        el(TextControl, {

                                }, __('+ Add Feature', 'my-e-shop'))                                            value: item.question,

                            )                                            onChange: function(value) {

                        )                                                updateItem(index, 'question', value);

                    )                                            },

                )                                            style: {

            ];                                                flex: 1,

        },                                                border: 'none',

                                                background: 'transparent'

        save: function() {                                            },

            return null; // Server-side rendering                                            onClick: function(e) {

        }                                                e.stopPropagation();

    });                                            }

})();                                        }),
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
                            }, __('+ Add Feature', 'my-e-shop'))
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
