(function() {
    'use strict';

    var __ = wp.i18n.__;
    var registerBlockType = wp.blocks.registerBlockType;
    var createElement = wp.element.createElement;
    var Fragment = wp.element.Fragment;
    var InspectorControls = wp.blockEditor.InspectorControls;
    
    if (typeof wp !== 'undefined' && wp.blockEditor && wp.blockEditor.useBlockProps) {
        var useBlockProps = wp.blockEditor.useBlockProps;
        var PanelBody = wp.components.PanelBody;
        var TextControl = wp.components.TextControl;
        var Button = wp.components.Button;
        var MediaUpload = wp.blockEditor.MediaUpload;
        var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;

        registerBlockType('my-e-shop/collection-cards', {
            edit: function(props) {
                var attributes = props.attributes;
                var setAttributes = props.setAttributes;
                
                // Ensure collections array exists
                if (!attributes.collections || !Array.isArray(attributes.collections)) {
                    setAttributes({ 
                        collections: [
                            {
                                imageUrl: '',
                                imageId: 0,
                                title: 'Tender Thoughts',
                                subtitle: 'Everything you need here',
                                url: '#'
                            },
                            {
                                imageUrl: '',
                                imageId: 0,
                                title: 'Witch Core',
                                subtitle: 'Everything you need here',
                                url: '#'
                            },
                            {
                                imageUrl: '',
                                imageId: 0,
                                title: 'Identity by design',
                                subtitle: 'Everything you need here',
                                url: '#'
                            },
                            {
                                imageUrl: '',
                                imageId: 0,
                                title: 'Data Muse',
                                subtitle: 'Everything you need here',
                                url: '#'
                            }
                        ]
                    });
                    return createElement('div', null, 'Загрузка...');
                }
                
                var blockProps = useBlockProps ? useBlockProps({
                    className: 'collection-cards-block-editor'
                }) : {
                    className: 'collection-cards-block-editor'
                };

                function addCollection() {
                    var newCollections = attributes.collections.slice();
                    newCollections.push({
                        imageUrl: '',
                        imageId: 0,
                        title: 'Новая коллекция',
                        subtitle: 'Everything you need here',
                        url: '#'
                    });
                    setAttributes({ collections: newCollections });
                }

                function removeCollection(index) {
                    var newCollections = attributes.collections.filter(function(_, i) {
                        return i !== index;
                    });
                    setAttributes({ collections: newCollections });
                }

                function updateCollection(index, field, value) {
                    var newCollections = attributes.collections.slice();
                    newCollections[index] = Object.assign({}, newCollections[index]);
                    newCollections[index][field] = value;
                    setAttributes({ collections: newCollections });
                }
                
                function updateCollectionImage(index, media) {
                    var newCollections = attributes.collections.slice();
                    newCollections[index] = Object.assign({}, newCollections[index], {
                        imageUrl: media.url,
                        imageId: media.id
                    });
                    setAttributes({ collections: newCollections });
                }

                return createElement(
                    Fragment,
                    null,
                    createElement(
                        InspectorControls,
                        null,
                        createElement(
                            PanelBody,
                            { title: 'Настройки заголовка', initialOpen: true },
                            createElement(TextControl, {
                                label: 'Заголовок блока',
                                value: attributes.blockTitle || '',
                                onChange: function(value) { setAttributes({ blockTitle: value }); }
                            }),
                            createElement(TextControl, {
                                label: 'Подзаголовок блока',
                                value: attributes.blockSubtitle || '',
                                onChange: function(value) { setAttributes({ blockSubtitle: value }); }
                            })
                        ),
                        attributes.collections.map(function(collection, index) {
                            return createElement(
                                PanelBody,
                                { 
                                    title: 'Коллекция ' + (index + 1) + ': ' + (collection.title || 'Без названия'),
                                    initialOpen: false,
                                    key: index
                                },
                                createElement(
                                    MediaUploadCheck,
                                    null,
                                    createElement(MediaUpload, {
                                        onSelect: function(media) {
                                            updateCollectionImage(index, media);
                                        },
                                        allowedTypes: ['image'],
                                        value: collection.imageId,
                                        render: function(obj) {
                                            return createElement(
                                                Button,
                                                {
                                                    className: collection.imageUrl ? 'editor-post-featured-image__preview' : 'editor-post-featured-image__toggle',
                                                    onClick: obj.open
                                                },
                                                !collection.imageUrl && 'Загрузить изображение',
                                                collection.imageUrl && createElement('img', {
                                                    src: collection.imageUrl,
                                                    alt: collection.title,
                                                    style: { width: '100%', height: 'auto', marginBottom: '10px' }
                                                })
                                            );
                                        }
                                    }),
                                    collection.imageUrl && createElement(
                                        Button,
                                        {
                                            onClick: function() {
                                                updateCollection(index, 'imageUrl', '');
                                                updateCollection(index, 'imageId', 0);
                                            },
                                            isDestructive: true,
                                            style: { marginTop: '10px' }
                                        },
                                        'Удалить изображение'
                                    )
                                ),
                                createElement(TextControl, {
                                    label: 'Заголовок коллекции',
                                    value: collection.title,
                                    onChange: function(value) { updateCollection(index, 'title', value); }
                                }),
                                createElement(TextControl, {
                                    label: 'Подзаголовок',
                                    value: collection.subtitle,
                                    onChange: function(value) { updateCollection(index, 'subtitle', value); }
                                }),
                                createElement(TextControl, {
                                    label: 'Ссылка (URL)',
                                    value: collection.url,
                                    onChange: function(value) { updateCollection(index, 'url', value); },
                                    placeholder: 'https://example.com'
                                }),
                                createElement(
                                    Button,
                                    {
                                        onClick: function() { removeCollection(index); },
                                        isDestructive: true,
                                        style: { marginTop: '10px' }
                                    },
                                    'Удалить коллекцию'
                                )
                            );
                        }),
                        createElement(
                            PanelBody,
                            { title: 'Добавить коллекцию', initialOpen: false },
                            createElement(
                                Button,
                                {
                                    onClick: addCollection,
                                    isPrimary: true
                                },
                                'Добавить новую коллекцию'
                            )
                        )
                    ),
                    createElement(
                        'div',
                        blockProps,
                        createElement(
                            'div',
                            { className: 'collection-cards-header' },
                            createElement('h2', { className: 'collection-cards-title' }, 
                                attributes.blockTitle || 'Our Collections'
                            ),
                            createElement('p', { className: 'collection-cards-subtitle' }, 
                                attributes.blockSubtitle || 'Each piece tells a story — choose yours'
                            )
                        ),
                        createElement(
                            'div',
                            { className: 'collection-cards-grid' },
                            attributes.collections.map(function(collection, index) {
                                return createElement(
                                    'div',
                                    { className: 'collection-card-item', key: index },
                                    createElement(
                                        'div',
                                        { className: 'collection-card-link' },
                                        createElement(
                                            'div',
                                            { className: 'collection-card-image-container' },
                                            collection.imageUrl ? 
                                                createElement('img', {
                                                    src: collection.imageUrl,
                                                    alt: collection.title,
                                                    className: 'collection-card-image'
                                                }) :
                                                createElement(
                                                    'div',
                                                    { className: 'collection-card-image-placeholder' },
                                                    '📁 Изображение не выбрано'
                                                ),
                                            createElement('div', { className: 'collection-card-overlay' }),
                                            createElement(
                                                'div',
                                                { className: 'collection-card-content' },
                                                createElement('h3', { className: 'collection-card-title' }, collection.title),
                                                createElement('p', { className: 'collection-card-subtitle' }, collection.subtitle)
                                            )
                                        )
                                    )
                                );
                            })
                        )
                    )
                );
            },

            save: function() {
                return null;
            }
        });
    }
})();
