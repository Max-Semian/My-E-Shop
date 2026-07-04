(function() {
    const { registerBlockType } = wp.blocks;
    const { createElement: el } = wp.element;
    const { __ } = wp.i18n;
    const { 
        useBlockProps, 
        InspectorControls,
        BlockControls,
        AlignmentToolbar
    } = wp.blockEditor;
    const { 
        PanelBody, 
        RangeControl,
        ColorPicker,
        TextareaControl
    } = wp.components;

    const Edit = function(props) {
        const { attributes, setAttributes } = props;
        const { 
            content,
            speed,
            textColor,
            backgroundColor,
            fontSize,
            textAlign
        } = attributes;
        
        const blockProps = useBlockProps({
            className: `fade-in-words-block font-${fontSize} align-${textAlign}`,
            style: {
                backgroundColor: backgroundColor || undefined
            }
        });

        const getFontSize = () => {
            switch(fontSize) {
                case 'small': return '24px';
                case 'medium': return '40px';
                case 'large': return '56px';
                default: return '40px';
            }
        };

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
                    title: __('Content', 'My-E-Shop'),
                    initialOpen: true
                },
                    el(TextareaControl, {
                        label: __('Block text', 'My-E-Shop'),
                        value: content,
                        onChange: function(value) { setAttributes({ content: value }); },
                        placeholder: __('Enter your text...', 'My-E-Shop'),
                        rows: 4,
                        help: __('Each word will appear with an animation', 'My-E-Shop')
                    })
                ),

                el(PanelBody, { 
                    title: __('Text settings', 'My-E-Shop'),
                    initialOpen: false
                },
                    el('div', { style: { marginBottom: '15px' } },
                        el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: '500' } },
                            __('Font size', 'My-E-Shop')
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
                    title: __('Animation settings', 'My-E-Shop'),
                    initialOpen: false
                },
                    el(RangeControl, {
                        label: __('Speed (ms)', 'My-E-Shop'),
                        value: speed,
                        onChange: function(value) { setAttributes({ speed: value }); },
                        min: 30,
                        max: 300,
                        step: 10,
                        help: __('Delay between words appearing', 'My-E-Shop')
                    })
                ),

                el(PanelBody, { 
                    title: __('Colors', 'My-E-Shop'),
                    initialOpen: false
                },
                    el('div', { style: { marginBottom: '15px' } },
                        el('label', {}, __('Text color', 'My-E-Shop')),
                        el(ColorPicker, {
                            color: textColor,
                            onChange: function(value) { setAttributes({ textColor: value }); },
                            enableAlpha: true
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

            el('div', { 
                className: 'fade-in-words-wrapper',
                style: {
                    backgroundColor: backgroundColor || undefined,
                    padding: '50px 20px',
                    minHeight: '200px',
                    display: 'flex',
                    alignItems: 'center',
                    justifyContent: textAlign === 'left' ? 'flex-start' : textAlign === 'right' ? 'flex-end' : 'center'
                }
            },
                el('div', { className: 'text-block' },
                    el('p', {
                        style: {
                            margin: 0,
                            color: textColor || undefined,
                            fontSize: getFontSize(),
                            fontFamily: "'Playfair Display', serif",
                            fontWeight: 500,
                            lineHeight: '1.3',
                            letterSpacing: '1px',
                            textAlign: textAlign,
                            whiteSpace: 'pre-wrap'
                        }
                    }, content || __('Enter text in the sidebar →', 'My-E-Shop'))
                )
            )
        );
    };

    const Save = function(props) {
        const { attributes } = props;
        const { 
            content,
            speed,
            textColor,
            backgroundColor,
            fontSize,
            textAlign
        } = attributes;
        
        const blockProps = useBlockProps.save({
            className: `fade-in-words-block font-${fontSize} align-${textAlign}`
        });

        return el('div', blockProps,
            el('div', { 
                className: 'fade-in-words-wrapper',
                style: {
                    backgroundColor: backgroundColor || undefined
                },
                'data-speed': speed
            },
                el('div', { className: 'text-block' },
                    el('p', { 
                        className: 'text-content',
                        style: {
                            color: textColor || undefined,
                            textAlign: textAlign
                        },
                        'data-text': content || ''
                    })
                )
            )
        );
    };

    registerBlockType('my-e-shop/fade-in-words', {
        edit: Edit,
        save: Save
    });
})();
