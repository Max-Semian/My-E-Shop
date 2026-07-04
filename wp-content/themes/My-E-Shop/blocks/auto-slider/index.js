(function (wp) {
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var MediaUpload = wp.blockEditor.MediaUpload;
    var MediaUploadCheck = wp.blockEditor.MediaUploadCheck;
    var PanelBody = wp.components.PanelBody;
    var Button = wp.components.Button;
    var __ = wp.i18n.__;
    var el = wp.element.createElement;
    var Fragment = wp.element.Fragment;

    registerBlockType('my-e-shop/auto-slider', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var images = attributes.images;

            function onSelectImages(newImages) {
                var formattedImages = newImages.map(function(img) {
                    return {
                        id: img.id,
                        url: img.url,
                        alt: img.alt || ''
                    };
                });
                setAttributes({ images: formattedImages });
            }

            function removeImage(index) {
                var newImages = images.filter(function(_, i) {
                    return i !== index;
                });
                setAttributes({ images: newImages });
            }

            function moveImageUp(index) {
                if (index === 0) return;
                var newImages = images.slice();
                var temp = newImages[index - 1];
                newImages[index - 1] = newImages[index];
                newImages[index] = temp;
                setAttributes({ images: newImages });
            }

            function moveImageDown(index) {
                if (index === images.length - 1) return;
                var newImages = images.slice();
                var temp = newImages[index + 1];
                newImages[index + 1] = newImages[index];
                newImages[index] = temp;
                setAttributes({ images: newImages });
            }

            // List of images in the inspector
            var imageControls = images.map(function(image, index) {
                return el(
                    'div',
                    { 
                        key: image.id,
                        style: { 
                            marginBottom: '15px',
                            padding: '10px',
                            border: '1px solid #ddd',
                            borderRadius: '4px'
                        }
                    },
                    el(
                        'div',
                        { style: { marginBottom: '10px' } },
                        el('img', {
                            src: image.url,
                            alt: image.alt,
                            style: { width: '100%', height: 'auto', display: 'block', borderRadius: '4px' }
                        })
                    ),
                    el(
                        'div',
                        { style: { display: 'flex', gap: '5px', flexWrap: 'wrap' } },
                        index > 0 && el(
                            Button,
                            {
                                isSmall: true,
                                onClick: function() { moveImageUp(index); }
                            },
                            '↑'
                        ),
                        index < images.length - 1 && el(
                            Button,
                            {
                                isSmall: true,
                                onClick: function() { moveImageDown(index); }
                            },
                            '↓'
                        ),
                        el(
                            Button,
                            {
                                isDestructive: true,
                                isSmall: true,
                                onClick: function() { removeImage(index); }
                            },
                            __('Remove', 'My-E-Shop')
                        )
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
                        { title: __('Slider settings', 'My-E-Shop'), initialOpen: true },
                        el(
                            'p',
                            { style: { marginBottom: '12px', fontSize: '13px', color: '#666' } },
                            __('Images: ', 'My-E-Shop') + images.length
                        ),
                        el(
                            MediaUploadCheck,
                            null,
                            el(MediaUpload, {
                                onSelect: onSelectImages,
                                allowedTypes: ['image'],
                                multiple: true,
                                gallery: true,
                                value: images.map(function(img) { return img.id; }),
                                render: function(obj) {
                                    return el(
                                        Button,
                                        {
                                            isPrimary: true,
                                            onClick: obj.open,
                                            style: { width: '100%', marginBottom: '15px' }
                                        },
                                        images.length > 0 ? __('Change images', 'My-E-Shop') : __('Add images', 'My-E-Shop')
                                    );
                                }
                            })
                        ),
                        imageControls.length > 0 && el('hr', { style: { margin: '15px 0' } }),
                        imageControls
                    )
                ),
                el(
                    'div',
                    { className: 'auto-slider-editor' },
                    images.length > 0 ? 
                        images.map(function(image, index) {
                            return el('img', {
                                key: image.id,
                                src: image.url,
                                alt: image.alt,
                                style: { 
                                    width: 'calc(25% - 7.5px)',
                                    height: '200px',
                                    objectFit: 'cover',
                                    marginRight: index < images.length - 1 ? '10px' : '0',
                                    borderRadius: '4px'
                                }
                            });
                        }) :
                        el(
                            'p',
                            { style: { textAlign: 'center', padding: '60px 20px', color: '#999' } },
                            __('Add images for the slider using the sidebar →', 'My-E-Shop')
                        )
                )
            );
        },

        save: function () {
            return null;
        }
    });
})(window.wp);
