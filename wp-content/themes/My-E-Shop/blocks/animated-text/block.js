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
            textColor,
            templateTheme 
        } = attributes;
        
        // Automatically set the text color for the dark template
        const effectiveTextColor = templateTheme === 'dark' ? '#F4F0EB' : textColor;

        const blockProps = useBlockProps({
            className: `animated-text-block align-${textAlign} font-${fontSize} theme-${templateTheme}`,
            style: {
                backgroundColor: backgroundColor || undefined,
                color: effectiveTextColor || undefined
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
                    title: __('Template & theme', 'My-E-Shop'),
                    initialOpen: true 
                },
                    el(SelectControl, {
                        label: __('Template theme', 'My-E-Shop'),
                        value: templateTheme,
                        options: [
                            { label: __('Light template', 'My-E-Shop'), value: 'light' },
                            { label: __('Dark template', 'My-E-Shop'), value: 'dark' }
                        ],
                        onChange: function(value) {
                            setAttributes({ templateTheme: value });
                            // When the dark template is selected, automatically set a light text color
                            if (value === 'dark') {
                                setAttributes({ textColor: '#F4F0EB' });
                            }
                        }
                    })
                ),

                el(PanelBody, { 
                    title: __('Animation settings', 'My-E-Shop'),
                    initialOpen: false 
                },
                    el(SelectControl, {
                        label: __('Animation type', 'My-E-Shop'),
                        value: animationType,
                        options: [
                            { label: __('Typewriter', 'My-E-Shop'), value: 'typewriter' },
                            { label: __('Word appearance', 'My-E-Shop'), value: 'fadeInWords' },
                            { label: __('Fade in', 'My-E-Shop'), value: 'fadeIn' },
                            { label: __('Slide up', 'My-E-Shop'), value: 'slideUp' }
                        ],
                        onChange: function(value) { setAttributes({ animationType: value }); }
                    }),

                    el(RangeControl, {
                        label: __('Animation speed (ms)', 'My-E-Shop'),
                        value: animationSpeed,
                        onChange: function(value) { setAttributes({ animationSpeed: value }); },
                        min: 10,
                        max: 200,
                        step: 10
                    })
                ),

                el(PanelBody, { 
                    title: __('Colors', 'My-E-Shop'),
                    initialOpen: false 
                },
                    templateTheme === 'dark' && el('div', { 
                        style: { 
                            padding: '10px', 
                            backgroundColor: '#f0f0f0', 
                            borderRadius: '4px', 
                            marginBottom: '15px',
                            fontSize: '12px' 
                        } 
                    },
                        __('ℹ️ A light text color #F4F0EB is set automatically for the dark template', 'My-E-Shop')
                    ),

                    el('div', { style: { marginBottom: '15px' } },
                        el('label', {}, __('Text color', 'My-E-Shop')),
                        el(ColorPicker, {
                            color: templateTheme === 'dark' ? '#F4F0EB' : textColor,
                            onChange: function(value) { 
                                if (templateTheme !== 'dark') {
                                    setAttributes({ textColor: value }); 
                                }
                            },
                            enableAlpha: true,
                            disabled: templateTheme === 'dark'
                        })
                    ),

                    el('div', { style: { marginBottom: '15px' } },
                        el('label', {}, __('Background color', 'My-E-Shop')),
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
                            color: effectiveTextColor + ' !important',
                            fontSize: '36px',
                            fontFamily: 'Montserrat',
                            fontWeight: 500,
                            lineHeight: '52px',
                            letterSpacing: '1px',
                            verticalAlign: 'middle',
                            minHeight: '156px',
                            display: 'block'
                        }
                    },
                        el(RichText, {
                            tagName: 'div',
                            value: content,
                            onChange: function(value) { setAttributes({ content: value }); },
                            placeholder: __('Enter your text...', 'My-E-Shop'),
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
                el('strong', {}, __('Settings preview:', 'My-E-Shop')),
                el('br'),
                __('Theme:', 'My-E-Shop') + ' ' + (templateTheme === 'dark' ? __('Dark', 'My-E-Shop') : __('Light', 'My-E-Shop')) + ', ',
                __('Animation:', 'My-E-Shop') + ' ' + animationType + ', ',
                __('Speed:', 'My-E-Shop') + ' ' + animationSpeed + 'ms, ',
                __('Size:', 'My-E-Shop') + ' ' + fontSize
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
            textColor,
            templateTheme 
        } = attributes;
        
        // Automatically set the text color for the dark template
        const effectiveTextColor = templateTheme === 'dark' ? '#F4F0EB' : textColor;

        const blockProps = useBlockProps.save({
            className: `animated-text-block align-${textAlign} font-${fontSize} theme-${templateTheme}`
        });

        return el('div', blockProps,
            el('section', { 
                className: 'animated-text-section',
                style: {
                    backgroundColor: backgroundColor || undefined,
                    color: effectiveTextColor || undefined
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
                            fontSize: '36px',
                            fontFamily: 'Montserrat',
                            fontWeight: 500,
                            lineHeight: '52px',
                            letterSpacing: '1px',
                            verticalAlign: 'middle',
                            minHeight: '156px',
                            display: 'block',
                            color: effectiveTextColor + ' !important'
                        },
                        // Store plain text without HTML tags so the animations work correctly
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
        description: __('Animated text block for eye-catching headings and descriptions', 'My-E-Shop'),
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
            },
            templateTheme: {
                type: 'string',
                default: 'light'
            }
        },
        edit: Edit,
        save: Save
    });
})();
