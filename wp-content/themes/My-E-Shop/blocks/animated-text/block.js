(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el, useState, useEffect } = wp.element;
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
        SelectControl,
        RangeControl,
        ColorPicker
    } = wp.components;

    const Edit = function(props) {
        const { attributes, setAttributes } = props;
        const { 
            content, 
            textAlign, 
            fontSize, 
            animationType, 
            animationSpeed,
            backgroundColor,
            textColor 
        } = attributes;
        const blockProps = useBlockProps({
            className: `animated-text-block align-${textAlign} font-${fontSize}`,
            style: {
                backgroundColor: backgroundColor || undefined,
                color: textColor || undefined
            }
        });

        return el('div', blockProps,
            el(BlockControls, {},
                el(AlignmentToolbar, {
                    value: textAlign,
                    onChange: function(value) { 
                        setAttributes({ textAlign: value || 'center' }); 
                    }
                })
            ),

            el(InspectorControls, {},
                el(PanelBody, { 
                    title: __('Настройки текста', 'My-E-Shop'), 
                    initialOpen: true 
                },
                    el(SelectControl, {
                        label: __('Размер шрифта', 'My-E-Shop'),
                        value: fontSize,
                        options: [
                            { label: __('Маленький', 'My-E-Shop'), value: 'small' },
                            { label: __('Обычный', 'My-E-Shop'), value: 'normal' },
                            { label: __('Большой', 'My-E-Shop'), value: 'large' },
                            { label: __('Очень большой', 'My-E-Shop'), value: 'extra-large' }
                        ],
                        onChange: function(value) { setAttributes({ fontSize: value }); }
                    })
                ),

                el(PanelBody, { 
                    title: __('Настройки анимации', 'My-E-Shop'), 
                    initialOpen: false 
                },
                    el(SelectControl, {
                        label: __('Тип анимации', 'My-E-Shop'),
                        value: animationType,
                        options: [
                            { label: __('Печатная машинка', 'My-E-Shop'), value: 'typewriter' },
                            { label: __('Появление слов', 'My-E-Shop'), value: 'fadeInWords' },
                            { label: __('Плавное появление', 'My-E-Shop'), value: 'fadeIn' },
                            { label: __('Скольжение снизу', 'My-E-Shop'), value: 'slideUp' }
                        ],
                        onChange: function(value) { setAttributes({ animationType: value }); }
                    }),

                    el(RangeControl, {
                        label: __('Скорость анимации (мс)', 'My-E-Shop'),
                        value: animationSpeed,
                        onChange: function(value) { setAttributes({ animationSpeed: value }); },
                        min: 10,
                        max: 200,
                        step: 10
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

            el('section', { 
                className: 'animated-text-section',
                style: {
                    backgroundColor: backgroundColor || undefined,
                    padding: '40px 20px',
                    textAlign: textAlign
                }
            },
                el('div', { className: 'text-block' },
                    el('div', { 
                        className: 'text-content preview-mode',
                        style: {
                            color: textColor || undefined,
                            fontSize: fontSize === 'small' ? '14px' : 
                                     fontSize === 'normal' ? '16px' :
                                     fontSize === 'large' ? '18px' : '22px'
                        }
                    },
                        el(RichText, {
                            tagName: 'div',
                            value: content,
                            onChange: function(value) { setAttributes({ content: value }); },
                            placeholder: __('Введите ваш текст...', 'My-E-Shop'),
                            allowedFormats: ['core/bold', 'core/italic']
                        })
                    )
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
                el('strong', {}, __('Предпросмотр настроек:', 'My-E-Shop')),
                el('br'),
                __('Анимация:', 'My-E-Shop') + ' ' + animationType + ', ',
                __('Скорость:', 'My-E-Shop') + ' ' + animationSpeed + 'мс, ',
                __('Размер:', 'My-E-Shop') + ' ' + fontSize
            )
        );
    };

    const Save = function(props) {
        const { attributes } = props;
        const { 
            content, 
            textAlign, 
            fontSize, 
            animationType, 
            animationSpeed,
            backgroundColor,
            textColor 
        } = attributes;
        
        const blockProps = useBlockProps.save({
            className: `animated-text-block align-${textAlign} font-${fontSize}`
        });

        return el('div', blockProps,
            el('section', { 
                className: 'animated-text-section',
                style: {
                    backgroundColor: backgroundColor || undefined,
                    color: textColor || undefined
                },
                'data-animation': animationType,
                'data-speed': animationSpeed
            },
                el('div', { className: 'text-block', id: 'textBlock' },
                    el('div', { 
                        className: 'text-content',
                        id: 'textContent',
                        style: {
                            textAlign: textAlign,
                            fontSize: fontSize === 'small' ? '14px' : 
                                     fontSize === 'normal' ? '16px' :
                                     fontSize === 'large' ? '18px' : '22px'
                        },
                        // Сохраняем чистый текст без HTML тегов для корректной работы анимаций
                        'data-text': content ? content.replace(/<[^>]*>/g, '') : ''
                    },
                        el(RichText.Content, {
                            value: content
                        })
                    )
                )
            )
        );
    };

    registerBlockType('my-e-shop/animated-text', {
        title: __('Animated Text Block', 'My-E-Shop'),
        icon: 'format-quote',
        category: 'my-e-shop',
        description: __('Блок с анимированным текстом для создания эффектных заголовков и описаний', 'My-E-Shop'),
        keywords: [__('text', 'My-E-Shop'), __('animated', 'My-E-Shop'), __('quote', 'My-E-Shop')],
        supports: {
            html: false,
            align: ['wide', 'full'],
            spacing: {
                margin: true,
                padding: true
            },
            color: {
                background: true,
                text: true
            }
        },
        attributes: {
            content: {
                type: 'string',
                default: 'T-shirts that combine runway aesthetics with individuality. These are clothes for those who want to be on trend, express themselves and belong to a niche community - without overpaying for a brand.'
            },
            textAlign: {
                type: 'string',
                default: 'center'
            },
            fontSize: {
                type: 'string',
                default: 'large'
            },
            animationType: {
                type: 'string',
                default: 'typewriter'
            },
            animationSpeed: {
                type: 'number',
                default: 50
            },
            backgroundColor: {
                type: 'string',
                default: ''
            },
            textColor: {
                type: 'string',
                default: ''
            }
        },
        edit: Edit,
        save: Save
    });
})();
