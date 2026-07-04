(function() {
    'use strict';
    
    var registerBlockType = wp.blocks.registerBlockType;
    var InspectorControls = wp.blockEditor.InspectorControls;
    var PanelBody = wp.components.PanelBody;
    var SelectControl = wp.components.SelectControl;
    var ToggleControl = wp.components.ToggleControl;
    var RangeControl = wp.components.RangeControl;
    var ColorPicker = wp.components.ColorPicker;
    var ServerSideRender = wp.serverSideRender || wp.components.ServerSideRender;
    var useState = wp.element.useState;
    var useEffect = wp.element.useEffect;
    var apiFetch = wp.apiFetch;
    var Placeholder = wp.components.Placeholder;
    var Spinner = wp.components.Spinner;

    registerBlockType('my-e-shop/category-products', {
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;
            var categoryId = attributes.categoryId;
            var categorySlug = attributes.categorySlug;
            var showBreadcrumbs = attributes.showBreadcrumbs;
            var showSearch = attributes.showSearch;
            var productsPerSlide = attributes.productsPerSlide;
            var showNavigation = attributes.showNavigation;
            var showPagination = attributes.showPagination;
            var autoplay = attributes.autoplay;
            var autoplaySpeed = attributes.autoplaySpeed;
            var backgroundColor = attributes.backgroundColor;
            var titleColor = attributes.titleColor;
            var priceColor = attributes.priceColor;
            var productInfoBackgroundColor = attributes.productInfoBackgroundColor;

            // Attribute debugging (remove after testing)
            // console.log('Category Products Attributes:', { backgroundColor, titleColor, priceColor });

            var categoriesState = useState([]);
            var categories = categoriesState[0];
            var setCategories = categoriesState[1];

            useEffect(function() {
                apiFetch({
                    path: '/wp/v2/product_cat?per_page=100'
                }).then(function(fetchedCategories) {
                    var categoryOptions = [{ label: 'Select a category', value: 0 }];
                    fetchedCategories.forEach(function(cat) {
                        categoryOptions.push({
                            label: cat.name,
                            value: cat.id
                        });
                    });
                    setCategories(categoryOptions);
                }).catch(function(error) {
                    console.error('Error fetching categories:', error);
                    // Fallback if API is not available
                    setCategories([
                        { label: 'Select a category', value: 0 }
                    ]);
                });
            }, []);

            var inspectorControls = wp.element.createElement(
                InspectorControls,
                {},
                wp.element.createElement(
                    PanelBody,
                    { title: 'Category settings', initialOpen: true },
                    wp.element.createElement(SelectControl, {
                        label: 'Select a category',
                        value: categoryId,
                        options: categories,
                        onChange: function(newCategoryId) {
                            var selectedCategory = categories.find(function(cat) {
                                return cat.value == newCategoryId;
                            });
                            setAttributes({
                                categoryId: parseInt(newCategoryId),
                                categorySlug: selectedCategory ? selectedCategory.label.toLowerCase() : ''
                            });
                        }
                    })
                ),
                wp.element.createElement(
                    PanelBody,
                    { title: 'Display settings', initialOpen: false },
                    wp.element.createElement(ToggleControl, {
                        label: 'Show breadcrumbs',
                        checked: showBreadcrumbs,
                        onChange: function(value) {
                            setAttributes({ showBreadcrumbs: value });
                        }
                    }),
                    wp.element.createElement(ToggleControl, {
                        label: 'Show search',
                        checked: showSearch,
                        onChange: function(value) {
                            setAttributes({ showSearch: value });
                        }
                    }),
                    wp.element.createElement(RangeControl, {
                        label: 'Products per slide',
                        value: productsPerSlide,
                        onChange: function(value) {
                            setAttributes({ productsPerSlide: value });
                        },
                        min: 1,
                        max: 8
                    })
                ),
                wp.element.createElement(
                    PanelBody,
                    { title: 'Slider settings', initialOpen: false },
                    wp.element.createElement(ToggleControl, {
                        label: 'Show navigation',
                        checked: showNavigation,
                        onChange: function(value) {
                            setAttributes({ showNavigation: value });
                        }
                    }),
                    wp.element.createElement(ToggleControl, {
                        label: 'Show pagination',
                        checked: showPagination,
                        onChange: function(value) {
                            setAttributes({ showPagination: value });
                        }
                    }),
                    wp.element.createElement(ToggleControl, {
                        label: 'Autoplay',
                        checked: autoplay,
                        onChange: function(value) {
                            setAttributes({ autoplay: value });
                        }
                    }),
                    autoplay && wp.element.createElement(RangeControl, {
                        label: 'Autoplay speed (ms)',
                        value: autoplaySpeed,
                        onChange: function(value) {
                            setAttributes({ autoplaySpeed: value });
                        },
                        min: 1000,
                        max: 10000,
                        step: 500
                    })
                ),
                wp.element.createElement(
                    PanelBody,
                    { title: 'Color settings', initialOpen: false },
                    wp.element.createElement(
                        'div',
                        { style: { marginBottom: '16px' } },
                        wp.element.createElement('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } }, 'Background color'),
                        wp.element.createElement(ColorPicker, {
                            color: backgroundColor || '#ffffff',
                            onChange: function(color) {
                                var colorValue = color.hex || color;
                                setAttributes({ backgroundColor: colorValue });
                            }
                        })
                    ),
                    wp.element.createElement(
                        'div',
                        { style: { marginBottom: '16px' } },
                        wp.element.createElement('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } }, 'Product title color'),
                        wp.element.createElement(ColorPicker, {
                            color: titleColor || '#333333',
                            onChange: function(color) {
                                var colorValue = color.hex || color;
                                setAttributes({ titleColor: colorValue });
                            }
                        })
                    ),
                    wp.element.createElement(
                        'div',
                        { style: { marginBottom: '16px' } },
                        wp.element.createElement('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } }, 'Price color'),
                        wp.element.createElement(ColorPicker, {
                            color: priceColor || '#e74c3c',
                            onChange: function(color) {
                                var colorValue = color.hex || color;
                                setAttributes({ priceColor: colorValue });
                            }
                        })
                    ),
                    wp.element.createElement(
                        'div',
                        { style: { marginBottom: '16px' } },
                        wp.element.createElement('label', { style: { display: 'block', marginBottom: '8px', fontWeight: 'bold' } }, 'Product info background color'),
                        wp.element.createElement(ColorPicker, {
                            color: productInfoBackgroundColor || '#f8f9fa',
                            onChange: function(color) {
                                var colorValue = color.hex || color;
                                setAttributes({ productInfoBackgroundColor: colorValue });
                            }
                        })
                    )
                )
            );

            var previewContent;
            if (categoryId === 0) {
                previewContent = wp.element.createElement(
                    Placeholder,
                    { 
                        icon: 'category',
                        label: 'Category Products'
                    },
                    wp.element.createElement(
                        'p',
                        {},
                        'Select a category in the block settings'
                    )
                );
            } else {
                var blockStyles = '';
                if (backgroundColor && backgroundColor !== '#ffffff') {
                    blockStyles += '.wp-block-my-e-shop-category-products .category-products-block { background-color: ' + backgroundColor + ' !important; }';
                }
                if (titleColor && titleColor !== '#333333') {
                    blockStyles += '.wp-block-my-e-shop-category-products .category-products-block .product-title a { color: ' + titleColor + ' !important; }';
                }
                if (priceColor && priceColor !== '#e74c3c') {
                    blockStyles += '.wp-block-my-e-shop-category-products .category-products-block .product-price, .wp-block-my-e-shop-category-products .category-products-block .product-price .woocommerce-Price-amount { color: ' + priceColor + ' !important; }';
                }

                if (!ServerSideRender) {
                    previewContent = wp.element.createElement(
                        Placeholder,
                        { label: 'Category Products' },
                        'ServerSideRender is not available'
                    );
                } else {
                    previewContent = wp.element.createElement(
                        'div',
                        {},
                        blockStyles && wp.element.createElement('style', {}, blockStyles),
                        wp.element.createElement(ServerSideRender, {
                            block: 'my-e-shop/category-products',
                            attributes: attributes
                        })
                    );
                }
            }

            return wp.element.createElement(
                'div',
                {},
                inspectorControls,
                previewContent
            );
        },

        save: function() {
            return null;
        }
    });
})();