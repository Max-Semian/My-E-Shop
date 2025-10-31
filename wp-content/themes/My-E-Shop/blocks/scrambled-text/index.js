(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { __ } = wp.i18n;
    const { 
        useBlockProps, 
        RichText, 
        InspectorControls,
        BlockControls,
        AlignmentToolbar
    } = wp.blockEditor;
    const { 
        PanelBody, 
        RangeControl,
        TextControl,
        ColorPicker
    } = wp.components;

    const Edit = function(props) {
        const { attributes, setAttributes } = props;
        const { 
            content,
            radius,
            duration,
            speed,
            scrambleChars,
            textColor,
            backgroundColor,
            fontSize,
            textAlign
        } = attributes;
        
        const blockProps = useBlockProps({
            className: `scrambled-text-block font-${fontSize} align-${textAlign}`,
            style: {
                backgroundColor: backgroundColor || undefined
            }
        });

        const getFontSize = () => {
            switch(fontSize) {
                case 'small': return '14px';
                case 'medium': return 'clamp(14px, 4vw, 32px)';
                case 'large': return 'clamp(24px, 5vw, 48px)';
                default: return 'clamp(14px, 4vw, 32px)';
            }
        };

        return el('div', blockProps,
            el(BlockControls, {},
                el(AlignmentToolbar, {
                    value: textAlign,
                    onChange: function(value) { 
                        setAttributes({ textAlign: value || 'left' }); 
                    }
                })
            ),

            el(InspectorControls, {},
                el(PanelBody, { 
                    title: __('Настройки текста', 'My-E-Shop'), 
                    initialOpen: true 
                },
                    el('div', { style: { marginBottom: '15px' } },
                        el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: '500' } }, 
                            __('Размер шрифта', 'My-E-Shop')
                        ),
                        el('div', { style: { display: 'flex', gap: '8px' } },
                            ['small', 'medium', 'large'].map(size => 
                                el('button', {
                                    key: size,
                                    className: 'components-button ' + (fontSize === size ? 'is-primary' : 'is-secondary'),
                                    onClick: () => setAttributes({ fontSize: size }),
                                    style: { flex: 1 }
                                }, __(size.charAt(0).toUpperCase() + size.slice(1), 'My-E-Shop'))
                            )
                        )
                    )
                ),

                el(PanelBody, { 
                    title: __('GSAP анимация', 'My-E-Shop'), 
                    initialOpen: true 
                },
                    el(RangeControl, {
                        label: __('Радиус эффекта (px)', 'My-E-Shop'),
                        value: radius,
                        onChange: function(value) { setAttributes({ radius: value }); },
                        min: 50,
                        max: 300,
                        step: 10,
                        help: __('Расстояние от курсора для активации эффекта', 'My-E-Shop')
                    }),

                    el(RangeControl, {
                        label: __('Длительность (сек)', 'My-E-Shop'),
                        value: duration,
                        onChange: function(value) { setAttributes({ duration: value }); },
                        min: 0.3,
                        max: 3.0,
                        step: 0.1,
                        help: __('Время анимации зашифровки', 'My-E-Shop')
                    }),

                    el(RangeControl, {
                        label: __('Скорость зашифровки', 'My-E-Shop'),
                        value: speed,
                        onChange: function(value) { setAttributes({ speed: value }); },
                        min: 0.1,
                        max: 1.0,
                        step: 0.1,
                        help: __('Скорость смены символов', 'My-E-Shop')
                    }),

                    el(TextControl, {
                        label: __('Символы зашифровки', 'My-E-Shop'),
                        value: scrambleChars,
                        onChange: function(value) { setAttributes({ scrambleChars: value }); },
                        help: __('Символы для эффекта (например: .:!@#)', 'My-E-Shop')
                    })
                ),

                el(PanelBody, { 
                    title: __('Цвета', 'My-E-Shop'), 
                    initialOpen: false 
                },
                    el('div', { style: { marginBottom: '15px' } },
                        el('label', {}, __('Цвет текста', 'My-E-Shop')),
                        el(ColorPicker, {
                            color: textColor,
                            onChange: function(value) { setAttributes({ textColor: value }); },
                            enableAlpha: true
                        })
                    ),

                    el('div', { style: { marginBottom: '15px' } },
                        el('label', {}, __('Цвет фона', 'My-E-Shop')),
                        el(ColorPicker, {
                            color: backgroundColor,
                            onChange: function(value) { setAttributes({ backgroundColor: value }); },
                            enableAlpha: true
                        })
                    )
                )
            ),

            el('div', { 
                className: 'scrambled-text-wrapper',
                style: {
                    backgroundColor: backgroundColor || undefined,
                    padding: '60px 20px',
                    minHeight: '200px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: textAlign === 'center' ? 'center' : textAlign === 'right' ? 'flex-end' : 'flex-start'
                }
            },
                el('div', { 
                    className: 'text-block-preview',
                    style: {
                        color: textColor,
                        fontSize: getFontSize(),
                        textAlign: textAlign,
                        maxWidth: '800px',
                        fontFamily: 'Montserrat, sans-serif',
                        lineHeight: '1.6'
                    }
                },
                    el(RichText, {
                        tagName: 'p',
                        value: content,
                        onChange: function(value) { setAttributes({ content: value }); },
                        placeholder: __('Введите ваш текст...', 'My-E-Shop'),
                        allowedFormats: []
                    })
                )
            ),

            el('div', { 
                style: { 
                    marginTop: '10px', 
                    padding: '10px', 
                    backgroundColor: '#f0f0f0', 
                    fontSize: '12px',
                    borderRadius: '4px'
                } 
            },
                el('strong', {}, __('💡 Совет:', 'My-E-Shop')),
                ' ',
                __('Наведите курсор на текст на фронтенде, чтобы увидеть эффект зашифровки', 'My-E-Shop')
            )
        );
    };

    const Save = function(props) {
        const { attributes } = props;
        const { 
            content,
            radius,
            duration,
            speed,
            scrambleChars,
            textColor,
            backgroundColor,
            fontSize,
            textAlign
        } = attributes;
        
        const blockProps = useBlockProps.save({
            className: `scrambled-text-block font-${fontSize} align-${textAlign}`
        });

        return el('div', blockProps,
            el('div', { 
                className: 'scrambled-text-wrapper',
                style: {
                    backgroundColor: backgroundColor || undefined
                },
                'data-radius': radius,
                'data-duration': duration,
                'data-speed': speed,
                'data-scramble-chars': scrambleChars
            },
                el('div', { 
                    className: 'text-block',
                    style: {
                        color: textColor,
                        textAlign: textAlign
                    }
                },
                    el('p', {
                        className: 'scrambled-text-content'
                    },
                        el(RichText.Content, {
                            value: content
                        })
                    )
                )
            )
        );
    };

    registerBlockType('my-e-shop/scrambled-text', {
        edit: Edit,
        save: Save
    });
})();
