(function() {
    const { registerBlockType } = wp.blocks;
    const { InspectorControls } = wp.blockEditor;
    const { 
        PanelBody, 
        TextControl, 
        TextareaControl
    } = wp.components;
    const { __ } = wp.i18n;

    registerBlockType('my-e-shop/newsletter-simple', {
        edit: function(props) {
            const { attributes, setAttributes } = props;
            const { 
                title, 
                subtitle, 
                buttonText, 
                placeholderText,
                disclaimerText
            } = attributes;

            return [
                wp.element.createElement(InspectorControls, { key: 'inspector' },
                    wp.element.createElement(PanelBody, { title: __('Newsletter Settings', 'my-e-shop'), initialOpen: true },
                        wp.element.createElement(TextControl, {
                            label: __('Title', 'my-e-shop'),
                            value: title,
                            onChange: function(value) {
                                setAttributes({ title: value });
                            }
                        }),
                        wp.element.createElement(TextareaControl, {
                            label: __('Subtitle', 'my-e-shop'),
                            value: subtitle,
                            onChange: function(value) {
                                setAttributes({ subtitle: value });
                            }
                        }),
                        wp.element.createElement(TextControl, {
                            label: __('Button Text', 'my-e-shop'),
                            value: buttonText,
                            onChange: function(value) {
                                setAttributes({ buttonText: value });
                            }
                        }),
                        wp.element.createElement(TextControl, {
                            label: __('Placeholder Text', 'my-e-shop'),
                            value: placeholderText,
                            onChange: function(value) {
                                setAttributes({ placeholderText: value });
                            }
                        }),
                        wp.element.createElement(TextareaControl, {
                            label: __('Disclaimer Text', 'my-e-shop'),
                            value: disclaimerText,
                            onChange: function(value) {
                                setAttributes({ disclaimerText: value });
                            }
                        })
                    )
                ),
                wp.element.createElement('div', { 
                    key: 'block',
                    className: 'newsletter-simple-preview',
                    style: { 
                        backgroundColor: '#F4F0EB',
                        borderRadius: '10px',
                        padding: '60px 80px',
                        textAlign: 'center'
                    }
                },
                    wp.element.createElement('h2', {
                        style: {
                            fontFamily: 'Playfair Display, serif',
                            fontWeight: 600,
                            fontSize: '32px',
                            marginBottom: '15px',
                            color: '#2C2C2C'
                        }
                    }, title),
                    wp.element.createElement('p', {
                        style: {
                            fontFamily: 'Montserrat, sans-serif',
                            fontSize: '14px',
                            marginBottom: '35px',
                            color: '#454545'
                        }
                    }, subtitle),
                    wp.element.createElement('div', {
                        style: {
                            maxWidth: '600px',
                            margin: '0 auto 20px'
                        }
                    },
                        wp.element.createElement('div', {
                            style: {
                                display: 'flex',
                                gap: 0,
                                border: '1px solid #B539DB',
                                borderRadius: '4px',
                                overflow: 'hidden'
                            }
                        },
                            wp.element.createElement('input', {
                                type: 'email',
                                placeholder: placeholderText,
                                style: {
                                    flex: 1,
                                    padding: '14px 20px',
                                    border: 'none',
                                    fontSize: '14px',
                                    fontFamily: 'Montserrat, sans-serif',
                                    outline: 'none'
                                },
                                disabled: true
                            }),
                            wp.element.createElement('button', {
                                style: {
                                    padding: '14px 32px',
                                    backgroundColor: '#B539DB',
                                    color: '#FFFFFF',
                                    border: 'none',
                                    fontFamily: 'Montserrat, sans-serif',
                                    fontSize: '12px',
                                    fontWeight: 600,
                                    textTransform: 'uppercase',
                                    letterSpacing: '0.5px',
                                    cursor: 'pointer',
                                    whiteSpace: 'nowrap'
                                },
                                disabled: true
                            }, buttonText)
                        )
                    ),
                    disclaimerText && wp.element.createElement('p', {
                        style: {
                            fontFamily: 'Montserrat, sans-serif',
                            fontSize: '11px',
                            color: '#888',
                            margin: '20px auto 0',
                            maxWidth: '600px',
                            lineHeight: 1.5
                        }
                    }, disclaimerText)
                )
            ];
        },

        save: function() {
            return null;
        }
    });
})();
