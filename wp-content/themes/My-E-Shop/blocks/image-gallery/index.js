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

    registerBlockType('my-e-shop/image-gallery', {
        edit: function (props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var rows = attributes.rows;

            function addRow() {
                var newRows = rows.slice();
                newRows.push({
                    image1: { id: 0, url: '', alt: '' },
                    image2: { id: 0, url: '', alt: '' }
                });
                setAttributes({ rows: newRows });
            }

            function removeRow(index) {
                var newRows = rows.filter(function (_, i) {
                    return i !== index;
                });
                setAttributes({ rows: newRows });
            }

            function updateImage(rowIndex, imageKey, image) {
                var newRows = rows.slice();
                newRows[rowIndex][imageKey] = {
                    id: image.id,
                    url: image.url,
                    alt: image.alt || ''
                };
                setAttributes({ rows: newRows });
            }

            var rowElements = rows.map(function (row, rowIndex) {
                return el(
                    'div',
                    { key: rowIndex, className: 'gallery-row-editor' },
                    el(
                        'div',
                        { className: 'row-header' },
                        el('span', null, __('Row', 'My-E-Shop') + ' ' + (rowIndex + 1)),
                        rows.length > 1 && el(
                            Button,
                            {
                                isDestructive: true,
                                isSmall: true,
                                onClick: function () { removeRow(rowIndex); }
                            },
                            __('Delete row', 'My-E-Shop')
                        )
                    ),
                    el(
                        'div',
                        { className: 'gallery-row-images' },
                        el(
                            'div',
                            { className: 'gallery-image-item' },
                            el(
                                MediaUploadCheck,
                                null,
                                el(MediaUpload, {
                                    onSelect: function (image) { updateImage(rowIndex, 'image1', image); },
                                    allowedTypes: ['image'],
                                    value: row.image1.id,
                                    render: function (obj) {
                                        return el(
                                            'div',
                                            { className: 'image-upload-wrapper' },
                                            row.image1.url ?
                                                el(
                                                    'div',
                                                    { className: 'image-preview' },
                                                    el('img', { src: row.image1.url, alt: row.image1.alt }),
                                                    el(
                                                        Button,
                                                        { isSecondary: true, onClick: obj.open },
                                                        __('Change', 'My-E-Shop')
                                                    )
                                                ) :
                                                el(
                                                    Button,
                                                    { isPrimary: true, onClick: obj.open },
                                                    __('Select image 1', 'My-E-Shop')
                                                )
                                        );
                                    }
                                })
                            )
                        ),
                        el(
                            'div',
                            { className: 'gallery-image-item' },
                            el(
                                MediaUploadCheck,
                                null,
                                el(MediaUpload, {
                                    onSelect: function (image) { updateImage(rowIndex, 'image2', image); },
                                    allowedTypes: ['image'],
                                    value: row.image2.id,
                                    render: function (obj) {
                                        return el(
                                            'div',
                                            { className: 'image-upload-wrapper' },
                                            row.image2.url ?
                                                el(
                                                    'div',
                                                    { className: 'image-preview' },
                                                    el('img', { src: row.image2.url, alt: row.image2.alt }),
                                                    el(
                                                        Button,
                                                        { isSecondary: true, onClick: obj.open },
                                                        __('Change', 'My-E-Shop')
                                                    )
                                                ) :
                                                el(
                                                    Button,
                                                    { isPrimary: true, onClick: obj.open },
                                                    __('Select image 2', 'My-E-Shop')
                                                )
                                        );
                                    }
                                })
                            )
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
                        { title: __('Gallery settings', 'My-E-Shop') },
                        el(
                            Button,
                            { isPrimary: true, onClick: addRow },
                            __('Add row', 'My-E-Shop')
                        )
                    )
                ),
                el(
                    'div',
                    { className: 'image-gallery-editor' },
                    el('h3', null, __('Image gallery', 'My-E-Shop')),
                    rowElements,
                    el(
                        Button,
                        { isPrimary: true, onClick: addRow, style: { marginTop: '20px' } },
                        __('+ Add another row', 'My-E-Shop')
                    )
                )
            );
        },

        save: function () {
            return null;
        }
    });
})(window.wp);
