(function() {
    var el = wp.element.createElement;
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var TextControl = wp.components.TextControl;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
    var Button = wp.components.Button;

    registerBlockType('my-e-shop/cta-banner', {
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            function onSelectImage(media) {
                setAttributes({
                    backgroundImage: media.url
                });
            }

            function onRemoveImage() {
                setAttributes({
                    backgroundImage: ''
                });
            }

            function updateTitle(value) {
                setAttributes({ title: value });
            }

            function updateSubtitle(value) {
                setAttributes({ subtitle: value });
            }

            function updateButtonText(value) {
                setAttributes({ buttonText: value });
            }

            function updateButtonLink(value) {
                setAttributes({ buttonLink: value });
            }

            var bannerStyle = {
                backgroundImage: attributes.backgroundImage ? 'url(' + attributes.backgroundImage + ')' : 'none'
            };

            return [
                el(InspectorControls, {},
                    el(PanelBody, { title: 'Настройки баннера', initialOpen: true },
                        el(MediaUploadCheck, {},
                            el(MediaUpload, {
                                onSelect: onSelectImage,
                                allowedTypes: ['image'],
                                value: attributes.backgroundImage,
                                render: function(obj) {
                                    return el(Button, {
                                        onClick: obj.open,
                                        variant: 'secondary'
                                    }, attributes.backgroundImage ? 'Изменить фон' : 'Загрузить фон');
                                }
                            })
                        ),
                        attributes.backgroundImage && el(Button, {
                            onClick: onRemoveImage,
                            variant: 'link',
                            isDestructive: true,
                            style: { marginTop: '10px' }
                        }, 'Удалить фон'),
                        el(TextControl, {
                            label: 'Заголовок',
                            value: attributes.title,
                            onChange: updateTitle
                        }),
                        el(TextControl, {
                            label: 'Подзаголовок',
                            value: attributes.subtitle,
                            onChange: updateSubtitle
                        }),
                        el(TextControl, {
                            label: 'Текст кнопки',
                            value: attributes.buttonText,
                            onChange: updateButtonText
                        }),
                        el(TextControl, {
                            label: 'Ссылка кнопки',
                            value: attributes.buttonLink,
                            onChange: updateButtonLink,
                            placeholder: '#'
                        })
                    )
                ),
                el('div', { className: 'cta-banner-wrapper' },
                    el('div', { className: 'cta-banner', style: bannerStyle },
                        el('div', { className: 'cta-banner-content' },
                            el('h2', { className: 'cta-banner-title' }, attributes.title),
                            el('p', { className: 'cta-banner-subtitle' }, attributes.subtitle),
                            el('a', {
                                href: attributes.buttonLink,
                                className: 'cta-banner-button',
                                onClick: function(e) { e.preventDefault(); }
                            }, attributes.buttonText)
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
