(function (wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var Button = wp.components.Button;
    var TextControl = wp.components.TextControl;
    var TextareaControl = wp.components.TextareaControl;
    var __ = wp.i18n.__;
    var el = wp.element.createElement;
    var Fragment = wp.element.Fragment;

    registerBlockType('my-e-shop/faq', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var items = attributes.items;

            // Добавляем уникальные ID если их нет
            if (!items[0] || !items[0].id) {
                var itemsWithIds = items.map(function(item, idx) {
                    return {
                        id: item.id || 'faq-' + Date.now() + '-' + idx,
                        question: item.question || '',
                        answer: item.answer || ''
                    };
                });
                setAttributes({ items: itemsWithIds });
                return null;
            }

            function addItem() {
                var newItems = items.slice();
                newItems.push({
                    id: 'faq-' + Date.now(),
                    question: '',
                    answer: ''
                });
                setAttributes({ items: newItems });
            }

            function removeItem(index) {
                var newItems = items.filter(function (_, i) {
                    return i !== index;
                });
                setAttributes({ items: newItems });
            }

            function updateQuestion(itemId, value) {
                var newItems = items.map(function(item) {
                    if (item.id === itemId) {
                        return {
                            id: item.id,
                            question: value,
                            answer: item.answer
                        };
                    }
                    return item;
                });
                setAttributes({ items: newItems });
            }

            function updateAnswer(itemId, value) {
                var newItems = items.map(function(item) {
                    if (item.id === itemId) {
                        return {
                            id: item.id,
                            question: item.question,
                            answer: value
                        };
                    }
                    return item;
                });
                setAttributes({ items: newItems });
            }

            function moveItemUp(index) {
                if (index === 0) return;
                var newItems = items.slice();
                var temp = newItems[index - 1];
                newItems[index - 1] = newItems[index];
                newItems[index] = temp;
                setAttributes({ items: newItems });
            }

            function moveItemDown(index) {
                if (index === items.length - 1) return;
                var newItems = items.slice();
                var temp = newItems[index + 1];
                newItems[index + 1] = newItems[index];
                newItems[index] = temp;
                setAttributes({ items: newItems });
            }

            // Создаем панели для бокового меню
            var inspectorPanels = items.map(function (item, index) {
                var itemId = item.id;
                
                return el(
                    PanelBody,
                    {
                        key: itemId,
                        title: __('Вопрос', 'My-E-Shop') + ' ' + (index + 1) + ': ' + (item.question || __('Без заголовка', 'My-E-Shop')),
                        initialOpen: false
                    },
                    el(TextControl, {
                        label: __('Вопрос', 'My-E-Shop'),
                        value: item.question || '',
                        onChange: (function(id) {
                            return function(value) { 
                                updateQuestion(id, value); 
                            };
                        })(itemId),
                        placeholder: __('Введите вопрос...', 'My-E-Shop')
                    }),
                    el(TextareaControl, {
                        label: __('Ответ', 'My-E-Shop'),
                        value: item.answer || '',
                        onChange: (function(id) {
                            return function(value) { 
                                updateAnswer(id, value); 
                            };
                        })(itemId),
                        placeholder: __('Введите ответ...', 'My-E-Shop'),
                        rows: 6
                    }),
                    el(
                        'div',
                        { style: { marginTop: '15px', display: 'flex', gap: '8px', flexWrap: 'wrap' } },
                        index > 0 && el(
                            Button,
                            {
                                isSecondary: true,
                                isSmall: true,
                                onClick: function () { moveItemUp(index); }
                            },
                            __('↑ Вверх', 'My-E-Shop')
                        ),
                        index < items.length - 1 && el(
                            Button,
                            {
                                isSecondary: true,
                                isSmall: true,
                                onClick: function () { moveItemDown(index); }
                            },
                            __('↓ Вниз', 'My-E-Shop')
                        ),
                        el(
                            Button,
                            {
                                isDestructive: true,
                                isSmall: true,
                                onClick: function () { removeItem(index); }
                            },
                            __('🗑 Удалить', 'My-E-Shop')
                        )
                    )
                );
            });

            // Превью в редакторе
            var previewItems = items.map(function (item, index) {
                var answerLines = item.answer ? item.answer.split('\n') : [];
                var answerElements = answerLines.map(function(line, i) {
                    return el('p', { key: i }, line || ' ');
                });
                
                return el(
                    'div',
                    { key: item.id, className: 'faq-item active' },
                    el(
                        'div',
                        { className: 'faq-question' },
                        item.question || __('Вопрос', 'My-E-Shop') + ' ' + (index + 1)
                    ),
                    el(
                        'div',
                        { className: 'faq-answer' },
                        answerElements.length > 0 ? answerElements : el('p', null, __('Ответ не заполнен', 'My-E-Shop'))
                    )
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
                        { title: __('FAQ - Управление', 'My-E-Shop'), initialOpen: true },
                        el(
                            'p',
                            { style: { marginBottom: '12px', fontSize: '13px', color: '#666' } },
                            __('Всего вопросов: ', 'My-E-Shop') + items.length
                        ),
                        el(
                            Button,
                            { 
                                isPrimary: true, 
                                onClick: addItem,
                                style: { width: '100%' }
                            },
                            __('+ Добавить новый вопрос', 'My-E-Shop')
                        )
                    ),
                    inspectorPanels
                ),
                el(
                    'div',
                    { className: 'faq-block' },
                    previewItems.length > 0 ? previewItems : el(
                        'p',
                        { style: { textAlign: 'center', padding: '40px', color: '#999' } },
                        __('FAQ блок пустой. Добавьте вопросы в боковой панели →', 'My-E-Shop')
                    )
                )
            );
        },

        save: function () {
            return null;
        }
    });
})(window.wp);
