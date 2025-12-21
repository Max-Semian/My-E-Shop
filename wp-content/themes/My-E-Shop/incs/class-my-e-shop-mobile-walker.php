<?php

class My_E_Shop_Mobile_Walker extends Walker_Nav_Menu {
    
    public function start_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $classes = ($depth === 0) ? 'submenu' : 'child-menu';
        $output .= "\n$indent<ul class=\"$classes\">\n";
    }

    public function end_lvl( &$output, $depth = 0, $args = null ) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }

    public function start_el( &$output, $data_object, $depth = 0, $args = null, $current_object_id = 0 ) {
        $menu_item = $data_object;
        $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';
        
        // Определяем классы для элемента
        $classes = empty( $menu_item->classes ) ? array() : (array) $menu_item->classes;
        
        if ($depth === 0) {
            $item_class = 'mobile-nav-item';
        } elseif ($depth === 1) {
            $item_class = 'submenu-item';
        } else {
            $item_class = 'child-item';
        }
        
        // Проверяем, есть ли у элемента дочерние пункты
        $has_children = in_array('menu-item-has-children', $classes);
        if ($has_children) {
            $item_class .= ' has-children';
        }
        
        $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $menu_item, $args, $depth ) );
        $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';
        
        $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $menu_item->ID, $menu_item, $args, $depth );
        $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';
        
        $output .= $indent . '<li class="' . esc_attr($item_class) . '"' . $id . $class_names .'>';
        
        // Создаем ссылку
        $atts = array();
        $atts['title']  = ! empty( $menu_item->attr_title ) ? $menu_item->attr_title : '';
        $atts['target'] = ! empty( $menu_item->target )     ? $menu_item->target     : '';
        $atts['rel']    = ! empty( $menu_item->xfn )        ? $menu_item->xfn        : '';
        $atts['href']   = ! empty( $menu_item->url )        ? $menu_item->url        : '';
        
        $atts = apply_filters( 'nav_menu_link_attributes', $atts, $menu_item, $args, $depth );
        
        $attributes = '';
        foreach ( $atts as $attr => $value ) {
            if ( ! empty( $value ) ) {
                $value = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
                $attributes .= ' ' . $attr . '="' . $value . '"';
            }
        }
        
        $title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );
        $title = apply_filters( 'nav_menu_item_title', $title, $menu_item, $args, $depth );
        
        // Get icon from custom field
        $icon_url = get_post_meta($menu_item->ID, '_menu_item_icon', true);
        
        $item_output = $args->before;
        $item_output .= '<a'. $attributes .'>';
        
        // Add icon if it exists
        if ($icon_url) {
            $item_output .= '<span class="menu-item-icon"><img src="' . esc_url($icon_url) . '" alt="' . esc_attr($title) . '" /></span>';
        }
        
        $item_output .= '<span class="menu-item-text">' . $args->link_before . $title . $args->link_after . '</span>';
        $item_output .= '</a>';
        $item_output .= $args->after;
        
        // Если есть дочерние элементы, добавляем кнопку toggle
        if ($has_children) {
            $toggle_class = ($depth === 0) ? 'submenu-toggle' : 'child-toggle';
            $item_output .= '<button class="' . $toggle_class . '" aria-expanded="false" aria-label="Toggle submenu"><i class="fas fa-plus"></i></button>';
        }
        
        $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $menu_item, $depth, $args );
    }
}
