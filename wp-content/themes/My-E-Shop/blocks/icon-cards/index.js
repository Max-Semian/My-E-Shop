(function (wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
    var PanelBody = wp.components.PanelBody;
    var Button = wp.components.Button;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var __ = wp.i18n.__;
    var el = wp.element.createElement;
    var Fragment = wp.element.Fragment;

    registerBlockType('my-e-shop/icon-cards', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var cards = attributes.cards;
            var mainTitle = attributes.mainTitle;

            // Добавляем уникальные ID если их нет
            if (!cards[0] || !cards[0].id) {
                var cardsWithIds = cards.map(function(card, idx) {
                    return {
                        id: card.id || 'card-' + Date.now() + '-' + idx,
                        icon: card.icon || { id: 0, url: '', alt: '' },
                        title: card.title || '',
                        description: card.description || ''
                    };
                });
                setAttributes({ cards: cardsWithIds });
                return null;
            }

            function addCard() {
                var newCards = cards.slice();
                newCards.push({
                    id: 'card-' + Date.now(),
                    icon: { id: 0, url: '', alt: '' },
                    title: '',
                    description: ''
                });
                setAttributes({ cards: newCards });
            }

            function removeCard(index) {
                var newCards = cards.filter(function (_, i) {
                    return i !== index;
                });
                setAttributes({ cards: newCards });
            }

            function updateTitle(cardId, value) {
                var newCards = cards.map(function(card) {
                    if (card.id === cardId) {
                        return {
                            id: card.id,
                            icon: card.icon,
                            title: value,
                            description: card.description
                        };
                    }
                    return card;
                });
                setAttributes({ cards: newCards });
            }

            function updateDescription(cardId, value) {
                var newCards = cards.map(function(card) {
                    if (card.id === cardId) {
                        return {
                            id: card.id,
                            icon: card.icon,
                            title: card.title,
                            description: value
                        };
                    }
                    return card;
                });
                setAttributes({ cards: newCards });
            }

            function updateIcon(cardId, media) {
                var newCards = cards.map(function(card) {
                    if (card.id === cardId) {
                        return {
                            id: card.id,
                            icon: {
                                id: media.id,
                                url: media.url,
                                alt: media.alt || ''
                            },
                            title: card.title,
                            description: card.description
                        };
                    }
                    return card;
                });
                setAttributes({ cards: newCards });
            }

            function removeIcon(cardId) {
                var newCards = cards.map(function(card) {
                    if (card.id === cardId) {
                        return {
                            id: card.id,
                            icon: { id: 0, url: '', alt: '' },
                            title: card.title,
                            description: card.description
                        };
                    }
                    return card;
                });
                setAttributes({ cards: newCards });
            }

            function moveCardUp(index) {
                if (index === 0) return;
                var newCards = cards.slice();
                var temp = newCards[index - 1];
                newCards[index - 1] = newCards[index];
                newCards[index] = temp;
                setAttributes({ cards: newCards });
            }

            function moveCardDown(index) {
                if (index === cards.length - 1) return;
                var newCards = cards.slice();
                var temp = newCards[index + 1];
                newCards[index + 1] = newCards[index];
                newCards[index] = temp;
                setAttributes({ cards: newCards });
            }

            // Создаем панели для бокового меню
            var inspectorPanels = cards.map(function (card, index) {
                var cardId = card.id;
                
                return el(
                    PanelBody,
                    {
                        key: cardId,
                        title: __('Карточка', 'My-E-Shop') + ' ' + (index + 1) + ': ' + (card.title || __('Без названия', 'My-E-Shop')),
                        initialOpen: false
                    },
                    el(
                        'div',
                        { style: { marginBottom: '15px' } },
                        el('label', { style: { display: 'block', marginBottom: '8px', fontWeight: '600' } }, __('Иконка', 'My-E-Shop')),
                        el(
                            MediaUploadCheck,
                            null,
                            el(MediaUpload, {
                                onSelect: (function(id) {
                                    return function(media) { 
                                        updateIcon(id, media); 
                                    };
                                })(cardId),
                                allowedTypes: ['image'],
                                value: card.icon.id,
                                render: function(obj) {
                                    return el(
                                        'div',
                                        null,
                                        card.icon.url ? 
                                            el(
                                                'div',
                                                { style: { marginBottom: '10px' } },
                                                el('img', { 
                                                    src: card.icon.url, 
                                                    alt: card.icon.alt,
                                                    style: { maxWidth: '100px', height: 'auto', display: 'block', marginBottom: '8px' }
                                                }),
                                                el(
                                                    'div',
                                                    { style: { display: 'flex', gap: '8px' } },
                                                    el(
                                                        Button,
                                                        { isSecondary: true, isSmall: true, onClick: obj.open },
                                                        __('Заменить', 'My-E-Shop')
                                                    ),
                                                    el(
                                                        Button,
                                                        { 
                                                            isDestructive: true, 
                                                            isSmall: true, 
                                                            onClick: (function(id) {
                                                                return function() { removeIcon(id); };
                                                            })(cardId)
                                                        },
                                                        __('Удалить', 'My-E-Shop')
                                                    )
                                                )
                                            ) :
                                            el(
                                                Button,
                                                { isPrimary: true, onClick: obj.open },
                                                __('Выбрать иконку', 'My-E-Shop')
                                            )
                                    );
                                }
                            })
                        )
                    ),
                    el(TextControl, {
                        label: __('Заголовок', 'My-E-Shop'),
                        value: card.title || '',
                        onChange: (function(id) {
                            return function(value) { 
                                updateTitle(id, value); 
                            };
                        })(cardId),
                        placeholder: __('Введите заголовок...', 'My-E-Shop')
                    }),
                    el(TextareaControl, {
                        label: __('Описание', 'My-E-Shop'),
                        value: card.description || '',
                        onChange: (function(id) {
                            return function(value) { 
                                updateDescription(id, value); 
                            };
                        })(cardId),
                        placeholder: __('Введите описание...', 'My-E-Shop'),
                        rows: 4
                    }),
                    el(
                        'div',
                        { style: { marginTop: '15px', display: 'flex', gap: '8px', flexWrap: 'wrap' } },
                        index > 0 && el(
                            Button,
                            {
                                isSecondary: true,
                                isSmall: true,
                                onClick: function () { moveCardUp(index); }
                            },
                            __('↑ Вверх', 'My-E-Shop')
                        ),
                        index < cards.length - 1 && el(
                            Button,
                            {
                                isSecondary: true,
                                isSmall: true,
                                onClick: function () { moveCardDown(index); }
                            },
                            __('↓ Вниз', 'My-E-Shop')
                        ),
                        el(
                            Button,
                            {
                                isDestructive: true,
                                isSmall: true,
                                onClick: function () { removeCard(index); }
                            },
                            __('🗑 Удалить', 'My-E-Shop')
                        )
                    )
                );
            });

            // Превью в редакторе
            var previewCards = cards.map(function (card, index) {
                return el(
                    'div',
                    { key: card.id, className: 'icon-card' },
                    card.icon.url && el('img', { 
                        src: card.icon.url, 
                        alt: card.icon.alt,
                        className: 'icon-card-icon'
                    }),
                    el('h3', { className: 'icon-card-title' }, card.title || __('Заголовок', 'My-E-Shop')),
                    el('p', { className: 'icon-card-description' }, card.description || __('Описание не заполнено', 'My-E-Shop'))
                );
            });

            return el(
                Fragment,
                null,
                el(
                    InspectorControls,
                    null,
                    el(
                        PanelBody,
                        { title: __('Основные настройки', 'My-E-Shop'), initialOpen: true },
                        el(TextControl, {
                            label: __('Основной заголовок (H2)', 'My-E-Shop'),
                            value: mainTitle || '',
                            onChange: function(value) { 
                                setAttributes({ mainTitle: value }); 
                            },
                            placeholder: __('Введите заголовок блока...', 'My-E-Shop')
                        }),
                        el('hr', { style: { margin: '20px 0' } }),
                        el(
                            'p',
                            { style: { marginBottom: '12px', fontSize: '13px', color: '#666' } },
                            __('Всего карточек: ', 'My-E-Shop') + cards.length
                        ),
                        el(
                            Button,
                            { 
                                isPrimary: true, 
                                onClick: addCard,
                                style: { width: '100%' }
                            },
                            __('+ Добавить карточку', 'My-E-Shop')
                        )
                    ),
                    inspectorPanels
                ),
                el(
                    'div',
                    { className: 'icon-cards-wrapper' },
                    mainTitle && el('h2', { className: 'icon-cards-main-title' }, mainTitle),
                    el(
                        'div',
                        { className: 'icon-cards-block' },
                        previewCards.length > 0 ? previewCards : el(
                            'p',
                            { style: { textAlign: 'center', padding: '40px', color: '#999' } },
                            __('Блок пустой. Добавьте карточки в боковой панели →', 'My-E-Shop')
                        )
                    )
                )
            );
        },

        save: function () {
            return null;
        }
    });
})(window.wp);
