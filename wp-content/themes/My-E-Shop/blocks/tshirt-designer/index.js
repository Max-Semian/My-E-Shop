(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { 
        InspectorControls,
        PanelColorSettings,
        MediaUpload,
        MediaUploadCheck
    } = wp.blockEditor;
    const { 
        PanelBody, 
        RangeControl,
        ToggleControl,
        Button
    } = wp.components;
    const { __ } = wp.i18n;

    registerBlockType('my-e-shop/tshirt-designer', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                backgroundColor,
                tshirtColor,
                printAreaWidth,
                printAreaHeight,
                enableTextTool,
                enableGraphicsTool,
                enableUploadTool,
                allowedGraphics
            } = attributes;

            const onSelectGraphics = function(media) {
                setAttributes({
                    allowedGraphics: media.map(function(item) {
                        return {
                            id: item.id,
                            url: item.url,
                            alt: item.alt || ''
                        };
                    })
                });
            };

            const removeGraphic = function(index) {
                const newGraphics = [...allowedGraphics];
                newGraphics.splice(index, 1);
                setAttributes({ allowedGraphics: newGraphics });
            };

            return [
                el(InspectorControls, null,
                    el(PanelColorSettings, {
                        title: __('Colors', 'my-e-shop'),
                        initialOpen: true,
                        colorSettings: [
                            {
                                value: backgroundColor,
                                onChange: function(color) {
                                    setAttributes({ backgroundColor: color });
                                },
                                label: __('Background Color', 'my-e-shop')
                            },
                            {
                                value: tshirtColor,
                                onChange: function(color) {
                                    setAttributes({ tshirtColor: color });
                                },
                                label: __('Default T-Shirt Color', 'my-e-shop')
                            }
                        ]
                    }),
                    el(PanelBody, { title: __('Print Area', 'my-e-shop'), initialOpen: false },
                        el(RangeControl, {
                            label: __('Area Width (px)', 'my-e-shop'),
                            value: printAreaWidth,
                            onChange: function(value) {
                                setAttributes({ printAreaWidth: value });
                            },
                            min: 200,
                            max: 400
                        }),
                        el(RangeControl, {
                            label: __('Area Height (px)', 'my-e-shop'),
                            value: printAreaHeight,
                            onChange: function(value) {
                                setAttributes({ printAreaHeight: value });
                            },
                            min: 250,
                            max: 500
                        })
                    ),
                    el(PanelBody, { title: __('Tools', 'my-e-shop'), initialOpen: false },
                        el(ToggleControl, {
                            label: __('Text Tool', 'my-e-shop'),
                            checked: enableTextTool,
                            onChange: function(value) {
                                setAttributes({ enableTextTool: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: __('Upload Images', 'my-e-shop'),
                            checked: enableUploadTool,
                            onChange: function(value) {
                                setAttributes({ enableUploadTool: value });
                            }
                        }),
                        el(ToggleControl, {
                            label: __('Graphics Library', 'my-e-shop'),
                            checked: enableGraphicsTool,
                            onChange: function(value) {
                                setAttributes({ enableGraphicsTool: value });
                            }
                        })
                    ),
                    enableGraphicsTool && el(PanelBody, { 
                        title: __('Graphics Library Assets', 'my-e-shop'), 
                        initialOpen: false 
                    },
                        el(MediaUploadCheck, null,
                            el(MediaUpload, {
                                onSelect: onSelectGraphics,
                                allowedTypes: ['image'],
                                multiple: true,
                                value: allowedGraphics.map(function(img) { return img.id; }),
                                render: function(obj) {
                                    return el(Button, {
                                        onClick: obj.open,
                                        className: 'button button-large',
                                        style: { marginBottom: '15px' }
                                    }, allowedGraphics.length > 0 ? 
                                        __('Change Graphics', 'my-e-shop') : 
                                        __('Add Graphics', 'my-e-shop')
                                    );
                                }
                            })
                        ),
                        allowedGraphics.length > 0 && el('div', {
                            style: {
                                display: 'grid',
                                gridTemplateColumns: 'repeat(3, 1fr)',
                                gap: '10px'
                            }
                        },
                            allowedGraphics.map(function(graphic, index) {
                                return el('div', {
                                    key: 'graphic-' + index,
                                    style: {
                                        position: 'relative',
                                        paddingTop: '100%',
                                        border: '1px solid #ddd',
                                        borderRadius: '4px',
                                        overflow: 'hidden'
                                    }
                                },
                                    el('img', {
                                        src: graphic.url,
                                        alt: graphic.alt,
                                        style: {
                                            position: 'absolute',
                                            top: 0,
                                            left: 0,
                                            width: '100%',
                                            height: '100%',
                                            objectFit: 'cover'
                                        }
                                    }),
                                    el(Button, {
                                        onClick: function() { removeGraphic(index); },
                                        className: 'button-link-delete',
                                        style: {
                                            position: 'absolute',
                                            top: '5px',
                                            right: '5px',
                                            background: 'rgba(255,255,255,0.9)',
                                            border: 'none',
                                            borderRadius: '50%',
                                            width: '24px',
                                            height: '24px',
                                            cursor: 'pointer',
                                            fontSize: '18px',
                                            lineHeight: '20px',
                                            color: '#dc3545'
                                        }
                                    }, '×')
                                );
                            })
                        )
                    )
                ),
                el('div', { 
                    className: 'tshirt-designer-editor',
                    style: {
                        backgroundColor: backgroundColor,
                        padding: '40px 20px',
                        minHeight: '600px'
                    }
                },
                    el('div', { 
                        className: 'container',
                        style: { textAlign: 'center' }
                    },
                        el('h3', { 
                            style: { 
                                marginBottom: '20px',
                                fontFamily: 'Playfair Display, serif',
                                fontSize: '32px'
                            }
                        }, __('T-Shirt Print Designer', 'my-e-shop')),
                        el('p', {
                            style: {
                                color: '#666',
                                marginBottom: '40px'
                            }
                        }, __('Editor preview. A fully functional designer will be available on the frontend.', 'my-e-shop')),
                        el('div', {
                            style: {
                                display: 'inline-block',
                                backgroundColor: '#fff',
                                padding: '30px',
                                borderRadius: '8px',
                                boxShadow: '0 2px 10px rgba(0,0,0,0.1)'
                            }
                        },
                            el('div', {
                                style: {
                                    display: 'flex',
                                    alignItems: 'center',
                                    gap: '20px',
                                    marginBottom: '20px'
                                }
                            },
                                el('div', {
                                    style: {
                                        fontSize: '14px',
                                        color: '#666'
                                    }
                                }, '🎨 Tools: ' + 
                                    (enableTextTool ? 'Text ' : '') +
                                    (enableUploadTool ? 'Upload ' : '') +
                                    (enableGraphicsTool ? 'Graphics' : '')
                                )
                            ),
                            el('div', {
                                style: {
                                    width: '400px',
                                    height: '500px',
                                    backgroundColor: tshirtColor,
                                    border: '2px dashed #ddd',
                                    borderRadius: '8px',
                                    display: 'flex',
                                    alignItems: 'center',
                                    justifyContent: 'center',
                                    position: 'relative',
                                    backgroundImage: 'url("data:image/svg+xml,%3Csvg width=\'400\' height=\'500\' xmlns=\'http://www.w3.org/2000/svg\'%3E%3Cpath d=\'M200,50 Q150,60 120,80 L120,350 Q120,400 150,450 L250,450 Q280,400 280,350 L280,80 Q250,60 200,50 M200,50 L180,30 Q200,20 220,30 Z M120,80 L80,100 L80,200 L120,180 M280,80 L320,100 L320,200 L280,180\' fill=\'none\' stroke=\'%23ddd\' stroke-width=\'2\'/%3E%3C/svg%3E")',
                                    backgroundSize: 'contain',
                                    backgroundPosition: 'center',
                                    backgroundRepeat: 'no-repeat'
                                }
                            },
                                el('div', {
                                    style: {
                                        width: printAreaWidth + 'px',
                                        height: printAreaHeight + 'px',
                                        border: '2px dashed #AA2DD0',
                                        borderRadius: '4px',
                                        display: 'flex',
                                        alignItems: 'center',
                                        justifyContent: 'center',
                                        backgroundColor: 'rgba(170, 45, 208, 0.05)'
                                    }
                                },
                                    el('div', {
                                        style: {
                                            textAlign: 'center',
                                            color: '#999',
                                            fontSize: '14px'
                                        }
                                    },
                                        el('div', null, '📐 Print Area'),
                                        el('div', { style: { fontSize: '12px', marginTop: '5px' } },
                                            printAreaWidth + 'px × ' + printAreaHeight + 'px'
                                        )
                                    )
                                )
                            ),
                            el('div', {
                                style: {
                                    marginTop: '20px',
                                    display: 'flex',
                                    gap: '10px',
                                    justifyContent: 'center'
                                }
                            },
                                el('button', {
                                    style: {
                                        padding: '10px 20px',
                                        backgroundColor: '#6B7280',
                                        color: '#fff',
                                        border: 'none',
                                        borderRadius: '4px',
                                        cursor: 'default'
                                    }
                                }, 'Front side'),
                                el('button', {
                                    style: {
                                        padding: '10px 20px',
                                        backgroundColor: '#E5E7EB',
                                        color: '#6B7280',
                                        border: 'none',
                                        borderRadius: '4px',
                                        cursor: 'default'
                                    }
                                }, 'Back side')
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
